<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Puskesmas;

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
            'puskesmas_id'     => 'required|exists:puskesmas,id',
            'nama_lengkap'     => 'required|string|max:100',
            'no_rekam_medis'   => 'required|string|max:50|unique:pasien',
            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'alamat'           => 'required|string',
            'kontak'           => 'required|string|max:20',
        ]);

        Pasien::create([
            'puskesmas_id'     => $request->puskesmas_id,
            'nama_lengkap'     => $request->nama_lengkap,
            'no_rekam_medis'   => $request->no_rekam_medis,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'alamat'           => $request->alamat,
            'kontak'           => $request->kontak,
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
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.pasien.edit', compact('pasien', 'puskesmas'));
    }

    /**
     * Update data pasien
     */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $request->validate([
            'puskesmas_id'     => 'required|exists:puskesmas,id',
            'nama_lengkap'     => 'required|string|max:100',
            'no_rekam_medis'   => 'required|string|max:50|unique:pasien,no_rekam_medis,' . $id,
            'tanggal_lahir'    => 'required|date',
            'jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'alamat'           => 'required|string',
            'kontak'           => 'required|string|max:20',
        ]);

        $pasien->update([
            'puskesmas_id'     => $request->puskesmas_id,
            'nama_lengkap'     => $request->nama_lengkap,
            'no_rekam_medis'   => $request->no_rekam_medis,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'alamat'           => $request->alamat,
            'kontak'           => $request->kontak,
        ]);

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
        $pasien->delete();

        return redirect()
            ->route('petugas.pasien.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }
}
