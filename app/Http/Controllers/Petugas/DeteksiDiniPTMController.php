<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeteksiDiniPTM;
use App\Models\Pasien;

class DeteksiDiniPTMController extends Controller
{
    /**
     * Menampilkan daftar data
     */
    public function index()
    {
        // ADMIN: lihat semua data
        if (Auth::user()->role_name === 'admin') {
            $deteksi = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
                ->latest()
                ->get();
        } else {
            // PETUGAS: hanya puskesmas sendiri
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            $deteksi = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
                ->where('puskesmas_id', $puskesmasId)
                ->latest()
                ->get();
        }

        return view('petugas.deteksi_dini.index', compact('deteksi'));
    }

    /**
     * Form tambah data
     */
public function create()
{
    if (Auth::user()->role_name === 'admin') {
        // ADMIN: semua pasien yang BELUM punya deteksi dini
        $pasien = Pasien::whereDoesntHave('deteksiDiniPTM')
            ->orderBy('nama_lengkap')
            ->get();
    } else {
        // PETUGAS: pasien puskesmas sendiri & BELUM punya deteksi dini
        $puskesmasId = Auth::user()->petugas->puskesmas_id;

        $pasien = Pasien::where('puskesmas_id', $puskesmasId)
            ->whereDoesntHave('deteksiDiniPTM')
            ->orderBy('nama_lengkap')
            ->get();
    }

    return view('petugas.deteksi_dini.create', compact('pasien'));
}


    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pasien_id'           => 'required|exists:pasien,id',
            'tanggal_pemeriksaan' => 'required|date',
            'tekanan_darah'       => 'nullable|string',
            'gula_darah'          => 'nullable|numeric',
            'kolesterol'          => 'nullable|numeric',
            'berat_badan'         => 'required|numeric',
            'tinggi_badan'        => 'required|numeric',
        ]);

        // hitung IMT
        $berat = (float) $request->berat_badan;
        $tinggi_cm = (float) $request->tinggi_badan;
        $imt = $tinggi_cm > 0
            ? round($berat / pow($tinggi_cm / 100, 2), 2)
            : null;

        // parse tekanan darah (120/80)
        $sbp = $dbp = null;
        if ($request->tekanan_darah && str_contains($request->tekanan_darah, '/')) {
            [$a, $b] = explode('/', $request->tekanan_darah);
            $sbp = is_numeric($a) ? (int) trim($a) : null;
            $dbp = is_numeric($b) ? (int) trim($b) : null;
        }

        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);

        if ($hipertensi || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
        } elseif ($imt !== null && $imt >= 25) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        // Tentukan puskesmas
        $puskesmasId = Auth::user()->role_name === 'admin'
            ? Pasien::findOrFail($request->pasien_id)->puskesmas_id
            : Auth::user()->petugas->puskesmas_id;

       DeteksiDiniPTM::create([
    'pasien_id'           => $request->pasien_id,
    'petugas_id'          => Auth::user()->role_name === 'petugas'
                                ? Auth::user()->petugas->id
                                : null, // admin boleh null
    'puskesmas_id'        => $puskesmasId,
    'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
    'tekanan_darah'       => $request->tekanan_darah,
    'gula_darah'          => $request->gula_darah,
    'kolesterol'          => $request->kolesterol,
    'berat_badan'         => $berat,
    'tinggi_badan'        => $tinggi_cm,
    'imt'                 => $imt,
    'hasil_skrining'      => $hasil,
    'created_by'          => Auth::id(),
]);


        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil disimpan. Hasil Skrining: ' . $hasil);
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        if (Auth::user()->role_name === 'admin') {
            $deteksi = DeteksiDiniPTM::findOrFail($id);
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            $deteksi = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id);
        }

        return view('petugas.deteksi_dini.edit', compact('deteksi'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role_name === 'admin') {
            $data = DeteksiDiniPTM::findOrFail($id);
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            $data = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id);
        }

        $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'tekanan_darah'       => 'nullable|string',
            'gula_darah'          => 'nullable|numeric',
            'kolesterol'          => 'nullable|numeric',
            'berat_badan'         => 'required|numeric',
            'tinggi_badan'        => 'required|numeric',
        ]);

        $berat = (float) $request->berat_badan;
        $tinggi_cm = (float) $request->tinggi_badan;
        $imt = $tinggi_cm > 0
            ? round($berat / pow($tinggi_cm / 100, 2), 2)
            : null;

        $sbp = $dbp = null;
        if ($request->tekanan_darah && str_contains($request->tekanan_darah, '/')) {
            [$a, $b] = explode('/', $request->tekanan_darah);
            $sbp = is_numeric($a) ? (int) trim($a) : null;
            $dbp = is_numeric($b) ? (int) trim($b) : null;
        }

        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);

        if ($hipertensi || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
        } elseif ($imt !== null && $imt >= 25) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        $data->update([
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'tekanan_darah'       => $request->tekanan_darah,
            'gula_darah'          => $request->gula_darah,
            'kolesterol'          => $request->kolesterol,
            'berat_badan'         => $berat,
            'tinggi_badan'        => $tinggi_cm,
            'imt'                 => $imt,
            'hasil_skrining'      => $hasil,
        ]);

        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil diperbarui. Hasil Skrining: ' . $hasil);
    }

    /**
     * Hapus data
     */
    public function destroy($id)
    {
        if (Auth::user()->role_name === 'admin') {
            DeteksiDiniPTM::findOrFail($id)->delete();
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id)
                ->delete();
        }

        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
