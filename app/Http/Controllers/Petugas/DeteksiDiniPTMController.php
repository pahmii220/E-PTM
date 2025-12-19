<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeteksiDiniPTM;
use App\Models\Pasien;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;

class DeteksiDiniPTMController extends Controller
{
    /**
     * Menampilkan daftar data
     */
    public function index()
    {
        $deteksi = DeteksiDiniPTM::with(['pasien', 'puskesmas', 'petugas'])
            ->latest()
            ->get();

        return view('petugas.deteksi_dini.index', compact('deteksi'));
    }

    /**
     * Form tambah data
     */
    public function create()
    {
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.deteksi_dini.create', compact('pasien', 'puskesmas'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pasien_id'           => 'required|exists:pasien,id',
            'puskesmas_id'        => 'required|exists:puskesmas,id',
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

        // logika skrining
        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);

        if ($hipertensi || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
       } elseif ($imt !== null && $imt >= 25) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        DeteksiDiniPTM::create([
            'pasien_id'           => $request->pasien_id,
            'puskesmas_id'        => $request->puskesmas_id,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'tekanan_darah'       => $request->tekanan_darah,
            'gula_darah'          => $request->gula_darah,
            'kolesterol'          => $request->kolesterol,
            'berat_badan'         => $berat,
            'tinggi_badan'        => $tinggi_cm,
            'imt'                 => $imt,
            'hasil_skrining'      => $hasil,
            'petugas_id'          => Auth::id(),
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
        $deteksi = DeteksiDiniPTM::findOrFail($id);
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.deteksi_dini.edit', compact('deteksi', 'pasien', 'puskesmas'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
{
    $data = DeteksiDiniPTM::findOrFail($id);

    $request->validate([
        'pasien_id'            => 'required|exists:pasien,id',
        'puskesmas_id'         => 'required|exists:puskesmas,id',
        'tanggal_pemeriksaan'  => 'required|date',
        'tekanan_darah'        => 'nullable|string',
        'gula_darah'           => 'nullable|numeric',
        'kolesterol'           => 'nullable|numeric',
        'berat_badan'          => 'required|numeric',
        'tinggi_badan'         => 'required|numeric',
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
        'pasien_id'           => $request->pasien_id,
        'puskesmas_id'        => $request->puskesmas_id,
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
        DeteksiDiniPTM::findOrFail($id)->delete();

        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil dihapus.');
    }
}
