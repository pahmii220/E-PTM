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
    // 1. Data Puskesmas
    $totalPuskesmas = \App\Models\Puskesmas::count();
    $puskesmasMelapor = \App\Models\DeteksiDiniPTM::whereMonth('tanggal_pemeriksaan', now()->month)
                            ->whereYear('tanggal_pemeriksaan', now()->year)
                            ->distinct('puskesmas_id') 
                            ->count('puskesmas_id');

    //                         dd([
    //     'Total Puskesmas Terdaftar' => $totalPuskesmas,
    //     'Puskesmas yang Melapor Bulan Juni' => $puskesmasMelapor
    // ]);
    
    $persentase = ($totalPuskesmas > 0) ? round(($puskesmasMelapor / $totalPuskesmas) * 100, 1) : 0;

    // 2. Data Chart
    $skNormal     = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai  = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko     = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() + 
                    \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();
    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;

    // 3. Array Data (Pastikan semua key ada di sini)
    $data = [
        'totalPeserta'   => \App\Models\Pasien::count(),
        'totalDeteksi'   => \App\Models\DeteksiDiniPTM::count(),
        'totalRisiko'    => \App\Models\FaktorResikoPTM::count(),
        'totalPuskesmas' => $totalPuskesmas,
        'persentase'     => $persentase,
        'sudah'          => $puskesmasMelapor, // Ini key yang menyebabkan error jika tidak ada
        'total'          => $totalPuskesmas,   // Ini key yang menyebabkan error jika tidak ada
        'pendingCount'   => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'pending')->count(),
        'approvedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'approved')->count(),
        'rejectedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'rejected')->count(),
    ];
    
    return view('kepala_p2ptm.dashboard', compact('data', 'skNormal', 'skDicurigai', 'skRisiko', 'totalSkrining'));
}
// Ganti bagian ini di KepalaP2ptmController.php

public function verifikasiPublik($id)
{
    // 1. Gunakan with('puskesmas') agar data puskesmas ikut terambil (mencegah error optional)
    // 2. Gunakan find($id) agar tidak otomatis memicu 404 jika kosong
    $dokumen = \App\Models\Pasien::with('puskesmas')->find($id);

    // 3. Jika data tidak ditemukan, arahkan ke halaman error yang cantik
    if (!$dokumen) {
        return view('verifikasi_publik_invalid', [
            'pesan' => 'Data pasien dengan ID tersebut tidak ditemukan dalam sistem.'
        ]);
    }

    // 4. Jika ketemu, kirim ke view
    return view('verifikasi_publik', compact('dokumen'));
}

public function cetak($id)
{
    // Ambil dokumen beserta tokennya
    $dokumen = DokumenPengesahan::findOrFail($id);

    // Kirim ke view cetak
    return view('cetak-dokumen', compact('dokumen'));
}

public function verifikasiLaporan(\Illuminate\Http\Request $request)
    {
        // Mengambil data dari URL (jika tidak ada, gunakan nilai default)
        $judul = $request->get('judul', 'Laporan P2PTM');
        $periode = $request->get('periode', date('F Y'));

        return view('verifikasi_publik', compact('judul', 'periode'));
    }


}