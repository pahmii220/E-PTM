<?php

namespace App\Http\Controllers;
use App\Models\DokumenPengesahan;
use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;
use App\Models\Peserta;
use App\Models\FaktorResikoPTM;
use App\Models\LaporanHasilMonitoring;
use Illuminate\Http\Request;

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
        'totalPeserta'   => \App\Models\Peserta::count(),
        'totalDeteksi'   => \App\Models\DeteksiDiniPTM::count(),
        'totalRisiko'    => $skRisiko,
        'totalPuskesmas' => $totalPuskesmas,
        'persentase'     => $persentase,
        'sudah'          => $puskesmasMelapor, // Ini key yang menyebabkan error jika tidak ada
        'total'          => $totalPuskesmas,   // Ini key yang menyebabkan error jika tidak ada
        'pendingCount'   => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'pending')->count(),
        'approvedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'approved')->count(),
        'rejectedCount'  => \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'rejected')->count(),
    ];
    
    // 4. Data Peta Sebaran Puskesmas
    $mapPuskesmasData = \App\Models\Puskesmas::whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->withCount(['peserta', 'deteksiDini'])
                        ->get();

    return view('kepala_p2ptm.dashboard', compact('data', 'skNormal', 'skDicurigai', 'skRisiko', 'totalSkrining', 'mapPuskesmasData'));
}

public function printStatistik()
{
    $tahun = date('Y');
    $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $statistikBulanan = [];
    $pesertaBulanan = [];
    $deteksiBulanan = [];
    $faktorBulanan = [];

    for ($m = 1; $m <= 12; $m++) {
        $pCount = Peserta::where('status_verifikasi', 'approved')
            ->whereYear('dibuat_pada', $tahun)
            ->whereMonth('dibuat_pada', $m)
            ->count();
            
        $dCount = DeteksiDiniPTM::where('status_verifikasi', 'approved')
            ->whereYear('tanggal_pemeriksaan', $tahun)
            ->whereMonth('tanggal_pemeriksaan', $m)
            ->count();
            
        $fCount = FaktorResikoPTM::where('status_verifikasi', 'approved')
            ->whereYear('tanggal_pemeriksaan', $tahun)
            ->whereMonth('tanggal_pemeriksaan', $m)
            ->count();

        $statistikBulanan[] = [
            'nama_bulan' => $bulanIndo[$m],
            'total_peserta' => $pCount,
            'total_deteksi' => $dCount,
            'total_faktor' => $fCount,
        ];

        $pesertaBulanan[] = $pCount;
        $deteksiBulanan[] = $dCount;
        $faktorBulanan[] = $fCount;
    }

    $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

    $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
    $qrToken = "STATISTIK-" . date('m-Y') . "-" . strtoupper(uniqid());

    return view('kepala_p2ptm.laporan.print_statistik', compact(
        'statistikBulanan',
        'bulanLabels',
        'pesertaBulanan',
        'deteksiBulanan',
        'faktorBulanan',
        'kepalaAktif',
        'qrToken',
        'tahun'
    ));
}


// Ganti bagian ini di KepalaP2ptmController.php

public function verifikasiPublik($id)
{
    // 1. Gunakan with('puskesmas') agar data puskesmas ikut terambil (mencegah error optional)
    // 2. Gunakan find($id) agar tidak otomatis memicu 404 jika kosong
    $dokumen = \App\Models\Peserta::with('puskesmas')->find($id);

    // 3. Jika data tidak ditemukan, arahkan ke halaman error yang cantik
    if (!$dokumen) {
        return view('verifikasi_publik_invalid', [
            'pesan' => 'Data peserta dengan ID tersebut tidak ditemukan dalam sistem.'
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

    public function tinjauLaporanMonitoring(Request $request)
    {
        $query = LaporanHasilMonitoring::with(['pegawai.user', 'puskesmas']);

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $bulan     = $request->input('bulan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }

        $laporan = $query->latest()->get();

        return view('kepala_p2ptm.laporan_monitoring.index', compact('laporan', 'startDate', 'endDate', 'bulan'));
    }

    public function accLaporanMonitoring(Request $request, $id)
    {
        $laporan = LaporanHasilMonitoring::findOrFail($id);
        
        $request->validate([
            'status_laporan' => 'required|in:disetujui,ditolak',
            'catatan_kepala' => 'nullable|string'
        ]);

        $laporan->update([
            'status_laporan' => $request->status_laporan,
            'catatan_kepala' => $request->catatan_kepala,
            'tanggal_disetujui' => $request->status_laporan === 'disetujui' ? now() : null
        ]);

        $statusText = $request->status_laporan === 'disetujui' ? 'disetujui' : 'ditolak';
        return redirect()->back()->with('success', "Laporan hasil monitoring berhasil $statusText.");
    }

    public function cetakLaporanMonitoring($id)
    {
        $laporan = \App\Models\LaporanHasilMonitoring::with(['pegawai.user', 'puskesmas'])->findOrFail($id);
        
        \Carbon\Carbon::setLocale('id');
        $tanggal = \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('l, d F Y');

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        return view('kepala_p2ptm.laporan_monitoring.print', compact('laporan', 'tanggal', 'kepalaAktif'));
    }

    public function cetakSemuaLaporanMonitoring(Request $request)
    {
        $query = LaporanHasilMonitoring::with(['pegawai.user', 'puskesmas']);

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $bulan     = $request->input('bulan');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        if ($bulan) {
            $query->whereMonth('created_at', $bulan);
        }

        $laporan = $query->latest()->get();

        \Carbon\Carbon::setLocale('id');
        $tanggal = \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('l, d F Y');

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        return view('kepala_p2ptm.laporan_monitoring.print_semua', compact('laporan', 'startDate', 'endDate', 'bulan', 'tanggal', 'kepalaAktif'));
    }
}