<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;


class PasienController extends Controller
{
    /**
     * Tampilkan daftar semua pasien
     */
    public function index()
    {
        $pasien = Pasien::with('puskesmas')
            ->latest()
            ->get();

        return view('petugas.pasien.index', compact('pasien'));
    }

    /**
     * Tampilkan form tambah pasien
     */
    public function create()
    {
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
        return view('petugas.pasien.create', compact('puskesmas'));
    }

    /**
     * Simpan data pasien baru
     */
    public function store(Request $request)
{
    $request->validate([
        'nama_lengkap'     => 'required|string|max:100',
        'no_rekam_medis'   => 'required|string|max:50|unique:pasien',
        'tanggal_lahir'    => 'required|date',
        'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
        'alamat'           => 'required|string',
        'kontak'           => 'required|string|max:20',
    ]);

    Pasien::create([
        'puskesmas_id'     => Auth::user()->petugas->puskesmas_id, // 🔒 KUNCI
        'nama_lengkap'     => $request->nama_lengkap,
        'no_rekam_medis'   => $request->no_rekam_medis,
        'tanggal_lahir'    => $request->tanggal_lahir,
        'jenis_kelamin'    => $request->jenis_kelamin,
        'alamat'           => $request->alamat,
        'kontak'           => $request->kontak,
        'created_by'       => Auth::id(),
    ]);

    return redirect()
        ->route('petugas.pasien.index')
        ->with('success', 'Data pasien berhasil ditambahkan.');
}


    /**
     * Tampilkan form edit pasien
     */
public function edit($id)
{
    $pasien = Pasien::findOrFail($id);

    // 🔒 Jika bukan admin dan data sudah approved → tolak
    if (Auth::user()->role_name !== 'admin' && $pasien->verification_status === 'approved') {
        return redirect()
            ->route('petugas.pasien.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat diedit.');
    }

    $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
    return view('petugas.pasien.edit', compact('pasien', 'puskesmas'));
}


    /**
     * Update data pasien
     */
public function update(Request $request, $id)
{
    $pasien = Pasien::findOrFail($id);

    // 🔒 Cegah edit jika sudah approved (kecuali admin)
    if (Auth::user()->role_name !== 'admin' && $pasien->verification_status === 'approved') {
        return redirect()
            ->route('petugas.pasien.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat diubah.');
    }

    $request->validate([
        'puskesmas_id'     => 'required|exists:puskesmas,id',
        'nama_lengkap'     => 'required|string|max:100',
        'no_rekam_medis'   => 'required|string|max:50|unique:pasien,no_rekam_medis,' . $id,
        'tanggal_lahir'    => 'required|date',
        'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
        'alamat'           => 'required|string',
        'kontak'           => 'required|string|max:20',
    ]);

    $pasien->update($request->all());

    return redirect()
        ->route('petugas.pasien.index')
        ->with('success', 'Data pasien berhasil diperbarui.');
}


    /**
     * Hapus data pasien
     */
public function destroy($id)
{
    $pasien = Pasien::findOrFail($id);

    // 🔒 Petugas tidak boleh hapus jika sudah approved
    if (Auth::user()->role_name !== 'admin' && $pasien->verification_status === 'approved') {
        return redirect()
            ->route('petugas.pasien.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat dihapus.');
    }

    $pasien->delete();

    return redirect()
        ->route('petugas.pasien.index')
        ->with('success', 'Data pasien berhasil dihapus.');
}

}
