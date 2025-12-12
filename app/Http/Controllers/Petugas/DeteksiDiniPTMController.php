<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeteksiDiniPTM;
use App\Models\Pasien;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SkriningSimple;

class DeteksiDiniPTMController extends Controller
{

    // Menampilkan daftar data
    public function index()
    {
        $deteksi = DeteksiDiniPTM::with(['pasien','petugas'])->latest()->get();
        return view('petugas.deteksi_dini.index', compact('deteksi'));
        
    }

    // Form tambah data
    public function create()
    {
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        return view('petugas.deteksi_dini.create', compact('pasien'));
    }

    // Simpan data baru
public function store(Request $request)
{
    $request->validate([
        'pasien_id' => 'required|exists:pasien,id',
        'tanggal_pemeriksaan' => 'required|date',
        'tekanan_darah' => 'nullable|string',
        'gula_darah' => 'nullable|numeric',
        'kolesterol' => 'nullable|numeric',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
        'puskesmas' => 'required|string',
    ]);

    // hitung IMT
    $berat = (float) $request->berat_badan;
    $tinggi_cm = (float) $request->tinggi_badan;
    $imt = null;
    if ($tinggi_cm > 0) {
        $t_m = $tinggi_cm / 100;
        $imt = round($berat / ($t_m * $t_m), 2);
    }

    // parse tekanan darah (spt "120/80")
    $sbp = null; $dbp = null;
    if ($request->tekanan_darah && strpos($request->tekanan_darah, '/') !== false) {
        [$a, $b] = explode('/', $request->tekanan_darah);
        $sbp = is_numeric($a) ? (int) trim($a) : null;
        $dbp = is_numeric($b) ? (int) trim($b) : null;
    }

    // LOGIKA SEDERHANA -> MAP KE ENUM DB: 'Normal','Dicurigai PTM','Risiko Tinggi'
    // Aturan:
    // - 'Risiko Tinggi' jika hipertensi (>=140/90) OR IMT >= 30
    // - 'Dicurigai PTM' jika 25 <= IMT < 30
    // - 'Normal' selainnya
    $hipertensi = false;
    if (($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90)) $hipertensi = true;

    if ($hipertensi || ($imt !== null && $imt >= 30)) {
        $hasilSkriningDb = 'Risiko Tinggi';
    } elseif ($imt !== null && $imt >= 25) {
        $hasilSkriningDb = 'Dicurigai PTM';
    } else {
        $hasilSkriningDb = 'Normal';
    }

    // simpan (hanya kolom yang ada)
    $model = new \App\Models\DeteksiDiniPTM();
    $model->pasien_id = $request->pasien_id;
    $model->tanggal_pemeriksaan = $request->tanggal_pemeriksaan;
    $model->tekanan_darah = $request->tekanan_darah;
    $model->gula_darah = $request->gula_darah;
    $model->kolesterol = $request->kolesterol;
    $model->berat_badan = $berat;
    $model->tinggi_badan = $tinggi_cm;
    $model->imt = $imt;
    $model->hasil_skrining = $hasilSkriningDb; // <- penting: sesuai enum DB
    $model->puskesmas = $request->puskesmas;
    $model->petugas_id = Auth::id();
    $saved = $model->save();

    \Log::info('DET_SAVED_FINAL', [
        'id' => $model->id ?? null,
        'saved' => $saved,
        'imt' => $model->imt,
        'hasil_skrining' => $model->hasil_skrining,
        'gula_darah' => $model->gula_darah,
        'kolesterol' => $model->kolesterol
    ]);

    return redirect()->route('petugas.deteksi_dini.index')->with('success', 'Data berhasil disimpan. Hasil Skrining: '.$model->hasil_skrining);
}


// Update data
public function update(Request $request, $id)
{
    $data = \App\Models\DeteksiDiniPTM::findOrFail($id);

    $request->validate([
        'tanggal_pemeriksaan' => 'required|date',
        'tekanan_darah' => 'nullable|string',
        'gula_darah' => 'nullable|numeric',
        'kolesterol' => 'nullable|numeric',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
        'puskesmas' => 'required|string',
    ]);

    $berat = (float) $request->berat_badan;
    $tinggi_cm = (float) $request->tinggi_badan;
    $imt = null;
    if ($tinggi_cm > 0) {
        $t_m = $tinggi_cm / 100;
        $imt = round($berat / ($t_m * $t_m), 2);
    }

    $sbp = $dbp = null;
    if ($request->tekanan_darah && strpos($request->tekanan_darah, '/') !== false) {
        [$a,$b] = explode('/', $request->tekanan_darah);
        $sbp = is_numeric($a) ? (int) trim($a) : null;
        $dbp = is_numeric($b) ? (int) trim($b) : null;
    }

    $hipertensi = false;
    if (($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90)) $hipertensi = true;

    if ($hipertensi || ($imt !== null && $imt >= 30)) $hasilSkriningDb = 'Risiko Tinggi';
    elseif ($imt !== null && $imt >= 25) $hasilSkriningDb = 'Dicurigai PTM';
    else $hasilSkriningDb = 'Normal';

    $data->tanggal_pemeriksaan = $request->tanggal_pemeriksaan;
    $data->tekanan_darah = $request->tekanan_darah;
    $data->gula_darah = $request->gula_darah;
    $data->kolesterol = $request->kolesterol;
    $data->berat_badan = $berat;
    $data->tinggi_badan = $tinggi_cm;
    $data->imt = $imt;
    $data->hasil_skrining = $hasilSkriningDb;
    $data->puskesmas = $request->puskesmas;
    $data->save();

    \Log::info('DET_UPDATED_FINAL', ['id'=>$data->id,'hasil_skrining'=>$data->hasil_skrining]);

    return redirect()->route('petugas.deteksi_dini.index')->with('success','Data berhasil diperbarui. Hasil Skrining: '.$data->hasil_skrining);
}


    public function edit($id)
{
    $deteksi = DeteksiDiniPTM::findOrFail($id);
    $pasien = Pasien::orderBy('nama_lengkap')->get();
    return view('petugas.deteksi_dini.edit', compact('deteksi','pasien'));
}


    // (opsional) hapus data
    public function destroy($id)
    {
        $data = DeteksiDiniPTM::findOrFail($id);
        $data->delete();

        return redirect()->route('petugas.deteksi_dini.index')->with('success', 'Data berhasil dihapus.');
    }



    
}
