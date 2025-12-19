<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaktorResikoPTM;
use App\Models\Pasien;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;

class FaktorResikoPTMController extends Controller
{
    /**
     * Tampilkan daftar data
     */
    public function index()
    {
        $faktor = FaktorResikoPTM::with(['pasien', 'puskesmas'])
            ->latest()
            ->get();

        return view('petugas.faktor_resiko.index', compact('faktor'));
    }

    /**
     * Form tambah data
     */
    public function create()
    {
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.faktor_resiko.create', compact('pasien', 'puskesmas'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pasien_id'               => 'required|exists:pasien,id',
            'puskesmas_id'            => 'required|exists:puskesmas,id',
            'tanggal_pemeriksaan'     => 'required|date',
            'merokok'                 => 'required|in:Ya,Tidak',
            'alkohol'                 => 'required|in:Ya,Tidak',
            'kurang_aktivitas_fisik'  => 'required|in:Ya,Tidak',
        ]);

        FaktorResikoPTM::create([
            'pasien_id'              => $request->pasien_id,
            'puskesmas_id'           => $request->puskesmas_id,
            'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
            'merokok'                => $request->merokok,
            'alkohol'                => $request->alkohol,
            'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
            'petugas_id'             => Auth::id(),
        ]);

        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('success', 'Data faktor risiko berhasil ditambahkan.');
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $faktor = FaktorResikoPTM::findOrFail($id);
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.faktor_resiko.edit', compact('faktor', 'pasien', 'puskesmas'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $faktor = FaktorResikoPTM::findOrFail($id);

        $request->validate([
            'pasien_id'               => 'required|exists:pasien,id',
            'puskesmas_id'            => 'required|exists:puskesmas,id',
            'tanggal_pemeriksaan'     => 'required|date',
            'merokok'                 => 'required|in:Ya,Tidak',
            'alkohol'                 => 'required|in:Ya,Tidak',
            'kurang_aktivitas_fisik'  => 'required|in:Ya,Tidak',
        ]);

        $faktor->update([
            'pasien_id'              => $request->pasien_id,
            'puskesmas_id'           => $request->puskesmas_id,
            'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
            'merokok'                => $request->merokok,
            'alkohol'                => $request->alkohol,
            'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
        ]);

        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('success', 'Data faktor risiko berhasil diperbarui.');
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        FaktorResikoPTM::findOrFail($id)->delete();

        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('success', 'Data faktor risiko berhasil dihapus.');
    }
}
