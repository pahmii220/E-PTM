<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\Puskesmas;
use Illuminate\Http\Request;

class PetugasProfileController extends Controller
{
    /**
     * Tampilkan form profil petugas
     */
    public function edit()
    {
        // ambil data petugas berdasarkan user login
        $petugas = Petugas::where('user_id', auth()->id())->first();

        // ambil daftar puskesmas
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.profil.edit', compact('petugas', 'puskesmas'));
    }

    /**
     * Simpan / update profil petugas
     */
    
public function update(Request $request)
{
    $request->validate([
        'nip' => 'required',
        'nama_pegawai' => 'required',
        'puskesmas_id' => 'required',
    ]);

    $petugas = Petugas::firstOrNew([
        'user_id' => auth()->id(),
    ]);

    $petugas->user_id = auth()->id(); // ✅ WAJIB
    $petugas->nip = $request->nip;
    $petugas->nama_pegawai = $request->nama_pegawai;
    $petugas->tanggal_lahir = $request->tanggal_lahir;
    $petugas->telepon = $request->telepon;
    $petugas->alamat = $request->alamat;
    $petugas->jabatan = $request->jabatan;
    $petugas->bidang = $request->bidang;
    $petugas->puskesmas_id = $request->puskesmas_id;

    $petugas->save(); // 🔥 INI TITIK PENENTU

    return redirect()
        ->route('petugas.dashboard')
        ->with('success', 'Profil petugas berhasil disimpan');
}
}
