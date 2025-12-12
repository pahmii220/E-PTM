<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;

class PasienController extends Controller
{
    /**
     * Tampilkan daftar semua pasien.
     */
    public function index()
    {
        $pasien = Pasien::latest()->get();
        return view('petugas.pasien.index', compact('pasien'));
    }

    /**
     * Tampilkan form tambah pasien.
     */
    public function create()
    {
        return view('petugas.pasien.create');
    }

    /**
     * Simpan data pasien baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'no_rekam_medis' => 'required|string|max:50|unique:pasien',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'puskesmas' => 'required|string|max:100',
        ]);

        Pasien::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_rekam_medis' => $request->no_rekam_medis,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'kontak' => $request->kontak,
            'puskesmas' => $request->puskesmas,
        ]);

        return redirect()->route('petugas.pasien.index')->with('success', 'Data pasien berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit pasien.
     */
    public function edit($id)
    {
        $pasien = Pasien::findOrFail($id);
        return view('petugas.pasien.edit', compact('pasien'));
    }

    /**
     * Update data pasien yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'no_rekam_medis' => 'required|string|max:50|unique:pasien,no_rekam_medis,' . $id,
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'kontak' => 'required|string|max:20',
            'puskesmas' => 'required|string|max:100',
        ]);

        $pasien->update($request->all());

        return redirect()->route('petugas.pasien.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Hapus data pasien.
     */
    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        $pasien->delete();

        return redirect()->route('petugas.pasien.index')->with('success', 'Data pasien berhasil dihapus.');
    }
}
