<?php

namespace App\Http\Controllers;
use App\Models\DokumenPengesahan;
use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;
use App\Models\Pasien;

class KepalaP2ptmController extends Controller
{
public function dashboard()
{
    $totalPuskesmas = \App\Models\Puskesmas::count();
    
    // Logika Persentase
    $puskesmasAktif = \App\Models\DeteksiDiniPTM::whereMonth('tanggal_pemeriksaan', date('m'))
                                                ->distinct('puskesmas_id')
                                                ->count();
    $persentase = $totalPuskesmas > 0 ? ($puskesmasAktif / $totalPuskesmas) * 100 : 0;

    // --- LOGIKA HITUNG SKRINING (PENTING UNTUK CHART) ---
    $skNormal    = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko    = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() + 
                   \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();
    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;

    $data = [
        'totalPeserta'   => \App\Models\Pasien::count(),
        'totalDeteksi'   => \App\Models\DeteksiDiniPTM::count(),
        'totalRisiko'    => \App\Models\FaktorResikoPTM::count(),
        'totalPuskesmas' => $totalPuskesmas,
        'persentase'     => round($persentase, 1),
        'pendingCount'   => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'pending')->count(),
        'approvedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'approved')->count(),
        'rejectedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'rejected')->count(),
    ];
    
    // TAMBAHKAN variabel-variabel ini ke dalam compact()
    return view('kepala_p2ptm.dashboard', compact('data', 'skNormal', 'skDicurigai', 'skRisiko', 'totalSkrining'));
}
    public function verifikasiPublik($token)
    {
        $urlPencarian = url('/verifikasi-dokumen/' . $token);
        $dokumen = DokumenPengesahan::with('kepalaP2ptm')->where('kode_validasi_qr', $urlPencarian)->firstOrFail();

        return view('verifikasi_publik', compact('dokumen'));
    }
}