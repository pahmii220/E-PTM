<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaktorResikoPTM;
use App\Models\Pasien;
use Illuminate\Support\Facades\Auth;

class FaktorResikoPTMController extends Controller
{
    public function index()
    {
        $faktor = FaktorResikoPTM::with('pasien')->get();
        return view('petugas.faktor_resiko.index', compact('faktor'));
    }

    public function create()
    {
        $pasien = Pasien::all();
        return view('petugas.faktor_resiko.create', compact('pasien'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required',
            'tanggal_pemeriksaan' => 'required|date',
            'merokok' => 'required',
            'alkohol' => 'required',
            'kurang_aktivitas_fisik' => 'required',
            'puskesmas' => 'required',
        ]);

        FaktorResikoPTM::create([
            'pasien_id' => $request->pasien_id,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'merokok' => $request->merokok,
            'alkohol' => $request->alkohol,
            'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
            'puskesmas' => $request->puskesmas,
            'petugas_id' => Auth::id(),
        ]);

        return redirect()->route('petugas.faktor_resiko.index')->with('success', 'Data berhasil ditambahkan.');
    }

        public function edit($id)
{
    $faktor = \App\Models\FaktorResikoPtM::findOrFail($id);
    $pasien = \App\Models\Pasien::orderBy('nama_lengkap')->get();
    return view('petugas.faktor_resiko.edit', compact('faktor','pasien'));
}


public function update(Request $request, $id)
{
    $faktor = \App\Models\FaktorResikoPtM::findOrFail($id);

    $validated = $request->validate([
        'pasien_id' => 'required|exists:pasien,id',
        'tanggal_pemeriksaan' => 'required|date',
        'merokok' => 'required|in:Ya,Tidak',
        'alkohol' => 'required|in:Ya,Tidak',
        'kurang_aktivitas_fisik' => 'required|in:Ya,Tidak',
        'puskesmas' => 'required|string|max:150',
    ]);

    $faktor->update($validated);

    return redirect()->route('petugas.faktor_resiko.index')->with('success', 'Data faktor risiko berhasil diperbarui.');
}


    public function destroy($id)
    {
        $faktor = FaktorResikoPTM::findOrFail($id);
        $faktor->delete();

        return redirect()->route('petugas.faktor_resiko.index')->with('success', 'Data berhasil dihapus.');
    }
}
