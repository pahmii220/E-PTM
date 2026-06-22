<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPtM;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect jika admin salah masuk
        if (Auth::user() && Auth::user()->role_name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        /* =====================
            DEFAULT VALUE & INITIALIZATION
        ====================== */
        $totalPasien   = 0;
        $totalDeteksi  = 0;
        $totalFaktor   = 0;
        $highRiskCount = 0;
        $totalPeserta  = 0;

        // Grafik Tren
        $monthLabels   = collect();
        $monthPasien   = collect();
        $monthDeteksi  = collect();
        $monthFaktor   = collect();
        $weeklyLabels  = collect();
        $weeklyPasien  = collect();
        $weeklyDeteksi = collect();
        $weeklyFaktor  = collect();
        $dailyLabels   = collect();
        $dailyPasien   = collect();
        $dailyDeteksi  = collect();
        $dailyFaktor   = collect();

        // Analitik Kegiatan & Faktor Risiko
        $kegiatanLabels  = collect();
        $kegiatanTotals  = collect();
        $kegiatanPeserta = collect();
        $faktorLabels    = collect(['Merokok', 'Alkohol', 'Kurang Aktivitas Fisik']);
        $faktorTotals    = collect([0, 0, 0]);

        // Insight
        $topFaktor = '-';

        // Tracking Data (Inisialisasi awal agar aman dari undefined)
        $trackingData  = collect();
        $trackPending  = 0;
        $trackApproved = 0;
        $trackRevisi   = 0;

        try {
            // Ambil data petugas yang sedang login untuk filter wilayah kerja Puskesmas
            $petugas = Auth::user()->petugas;

            // ==========================================
            // KODE TRACKING DATA VERIFIKASI (MENGGABUNGKAN PASIEN, DETEKSI DINI, DAN FAKTOR RISIKO)
            // ==========================================
            if (class_exists(DeteksiDiniPTM::class) && class_exists(Pasien::class) && class_exists(\App\Models\FaktorResikoPTM::class)) {
                $queryPasien = Pasien::query();
                $queryDeteksi = DeteksiDiniPTM::with(['pasien']);
                $queryFaktor = \App\Models\FaktorResikoPTM::with(['pasien']);

                if ($petugas && $petugas->puskesmas_id) {
                    $queryPasien->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $pasienTrack = $queryPasien->orderBy('dibuat_pada', 'desc')->take(5)->get();
                $deteksiTrack = $queryDeteksi->orderBy('dibuat_pada', 'desc')->take(5)->get();
                $faktorTrack = $queryFaktor->orderBy('dibuat_pada', 'desc')->take(5)->get();

                // Gabungkan semua data pelacakan
                $trackingData = collect()
                    ->concat($pasienTrack)
                    ->concat($deteksiTrack)
                    ->concat($faktorTrack)
                    ->sortByDesc('dibuat_pada')
                    ->take(5);

                // Hitung total pending, approved, dan rejected secara akumulatif
                $queryCountPasien = Pasien::query();
                $queryCountDeteksi = DeteksiDiniPTM::query();
                $queryCountFaktor = \App\Models\FaktorResikoPTM::query();

                if ($petugas && $petugas->puskesmas_id) {
                    $queryCountPasien->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryCountDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryCountFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $trackPending = (clone $queryCountPasien)->where('status_verifikasi', 'pending')->count()
                              + (clone $queryCountDeteksi)->where('status_verifikasi', 'pending')->count()
                              + (clone $queryCountFaktor)->where('status_verifikasi', 'pending')->count();

                $trackApproved = (clone $queryCountPasien)->where('status_verifikasi', 'approved')->count()
                               + (clone $queryCountDeteksi)->where('status_verifikasi', 'approved')->count()
                               + (clone $queryCountFaktor)->where('status_verifikasi', 'approved')->count();

                $trackRevisi = (clone $queryCountPasien)->where('status_verifikasi', 'rejected')->count()
                             + (clone $queryCountDeteksi)->where('status_verifikasi', 'rejected')->count()
                             + (clone $queryCountFaktor)->where('status_verifikasi', 'rejected')->count();
            }

            /* =====================
               PASIEN
            ====================== */
            if (class_exists(Pasien::class)) {
                $queryPasienCount = Pasien::query();
                if ($petugas && $petugas->puskesmas_id) {
                    $queryPasienCount->where('puskesmas_id', $petugas->puskesmas_id);
                }
                $totalPasien  = $queryPasienCount->count();
                $totalPeserta = $totalPasien;
            }

            /* =====================
               DETEKSI DINI
            ====================== */
            if (class_exists(DeteksiDiniPTM::class)) {
                $queryDeteksiCount = DeteksiDiniPTM::query();
                if ($petugas && $petugas->puskesmas_id) {
                    $queryDeteksiCount->where('puskesmas_id', $petugas->puskesmas_id);
                }
                $totalDeteksi = $queryDeteksiCount->count();

                if (Schema::hasColumn('deteksi_dini_ptm', 'hasil_skrining')) {
                    $queryHighRisk = DeteksiDiniPTM::where('hasil_skrining', 'Risiko Tinggi');
                    if ($petugas && $petugas->puskesmas_id) {
                        $queryHighRisk->where('puskesmas_id', $petugas->puskesmas_id);
                    }
                    $highRiskCount = $queryHighRisk->count();
                }
            }

            /* =====================
               FAKTOR RISIKO (SINKRONISASI NAMA TABEL)
            ====================== */
            $tableFaktor = 'faktor_resiko_ptm';
            if (class_exists(FaktorResikoPtM::class)) {
                $tableFaktor = (new FaktorResikoPtM)->getTable();
            } elseif (Schema::hasTable('faktor_resiko_ptms')) {
                $tableFaktor = 'faktor_resiko_ptms';
            } elseif (Schema::hasTable('faktor_risiko_ptm')) {
                $tableFaktor = 'faktor_risiko_ptm';
            }

            if (Schema::hasTable($tableFaktor)) {
                $queryFaktorCount = DB::table($tableFaktor);
                if ($petugas && $petugas->puskesmas_id && Schema::hasColumn($tableFaktor, 'puskesmas_id')) {
                    $queryFaktorCount->where('puskesmas_id', $petugas->puskesmas_id);
                }
                $totalFaktor = $queryFaktorCount->count();
            }

            /* =====================
               TREN BULANAN, MINGGUAN, HARIAN (3 DATASET)
            ====================== */
            $year = now()->year;

            // --- 1. Bulanan ---
            $monthLabels = collect(range(1, 12))->map(function ($m) {
                return Carbon::create()->month($m)->translatedFormat('F');
            });

            // Pasien Bulanan
            $monthlyPasienData = Pasien::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthPasien = collect(range(1, 12))->map(function ($m) use ($monthlyPasienData) {
                return $monthlyPasienData->firstWhere('bulan', $m)->total ?? 0;
            });

            // Deteksi Dini Bulanan
            $monthlyDeteksiData = DeteksiDiniPTM::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthDeteksi = collect(range(1, 12))->map(function ($m) use ($monthlyDeteksiData) {
                return $monthlyDeteksiData->firstWhere('bulan', $m)->total ?? 0;
            });

            // Faktor Risiko Bulanan
            $monthlyFaktorData = \App\Models\FaktorResikoPTM::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthFaktor = collect(range(1, 12))->map(function ($m) use ($monthlyFaktorData) {
                return $monthlyFaktorData->firstWhere('bulan', $m)->total ?? 0;
            });

            // --- 2. Mingguan ---
            $weeklyLabels = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $weeklyLabels->push($date->translatedFormat('D'));

                // Pasien
                $qWPasien = Pasien::whereDate('dibuat_pada', $date);
                if ($petugas && $petugas->puskesmas_id) $qWPasien->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyPasien->push($qWPasien->count());

                // Deteksi Dini
                $qWDeteksi = DeteksiDiniPTM::whereDate('dibuat_pada', $date);
                if ($petugas && $petugas->puskesmas_id) $qWDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyDeteksi->push($qWDeteksi->count());

                // Faktor Risiko
                $qWFaktor = \App\Models\FaktorResikoPTM::whereDate('dibuat_pada', $date);
                if ($petugas && $petugas->puskesmas_id) $qWFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyFaktor->push($qWFaktor->count());
            }

            // --- 3. Harian ---
            $dailyLabels = collect();
            for ($i = 0; $i < 24; $i++) {
                $dailyLabels->push(sprintf('%02d:00', $i));

                // Pasien
                $qDPasien = Pasien::whereDate('dibuat_pada', today())->whereRaw('HOUR(dibuat_pada) = ?', [$i]);
                if ($petugas && $petugas->puskesmas_id) $qDPasien->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyPasien->push($qDPasien->count());

                // Deteksi Dini
                $qDDeteksi = DeteksiDiniPTM::whereDate('dibuat_pada', today())->whereRaw('HOUR(dibuat_pada) = ?', [$i]);
                if ($petugas && $petugas->puskesmas_id) $qDDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyDeteksi->push($qDDeteksi->count());

                // Faktor Risiko
                $qDFaktor = \App\Models\FaktorResikoPTM::whereDate('dibuat_pada', today())->whereRaw('HOUR(dibuat_pada) = ?', [$i]);
                if ($petugas && $petugas->puskesmas_id) $qDFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyFaktor->push($qDFaktor->count());
            }


            /* =====================
               ANALITIK KEGIATAN
            ====================== */
            $tableKegiatan = 'kegiatan'; 
            
            if (Schema::hasTable($tableKegiatan)) {
                $queryK = DB::table($tableKegiatan)
                    ->select(
                        'jenis_kegiatan', 
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(jumlah_peserta) as total_peserta') 
                    )
                    ->groupBy('jenis_kegiatan');

                if ($petugas && $petugas->puskesmas_id) {
                    $queryK->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $kegiatanData = $queryK->get();
                $kegiatanLabels  = $kegiatanData->pluck('jenis_kegiatan')->toArray();
                $kegiatanTotals  = $kegiatanData->pluck('total')->toArray();
                $kegiatanPeserta = $kegiatanData->pluck('total_peserta')->toArray(); 
            }

            /* =====================
               ANALITIK DISTRIBUSI FAKTOR RISIKO
            ====================== */
            if (Schema::hasTable($tableFaktor)) {
                $queryF = DB::table($tableFaktor);

                if ($petugas && $petugas->puskesmas_id && Schema::hasColumn($tableFaktor, 'puskesmas_id')) {
                    $queryF->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $merokokCount = (clone $queryF)->where('merokok', 'Ya')->count();
                $alkoholCount = (clone $queryF)->where('alkohol', 'Ya')->count();
                $aktivitasCount = (clone $queryF)->where('kurang_aktivitas_fisik', 'Ya')->count();

                $faktorTotals = collect([$merokokCount, $alkoholCount, $aktivitasCount]);

                $topIndex = $faktorTotals->search($faktorTotals->max());
                $topFaktor = $faktorLabels[$topIndex] ?? '-';
            }

        } catch (\Throwable $e) {
            Log::warning('Dashboard Petugas Error: ' . $e->getMessage());
        }
        
        // SATU-SATUNYA RETURN VIEW YANG DIEKSEKUSI DI AKHIR FUNGSI
        return view('petugas.dashboard', compact(
            // Statistik Utama
            'totalPasien',
            'totalDeteksi',
            'totalFaktor',
            'highRiskCount',
            'totalPeserta',

            // Grafik Tren
            'monthLabels',
            'monthPasien',
            'monthDeteksi',
            'monthFaktor',
            'weeklyLabels',
            'weeklyPasien',
            'weeklyDeteksi',
            'weeklyFaktor',
            'dailyLabels',
            'dailyPasien',
            'dailyDeteksi',
            'dailyFaktor',

            // Analitik Kegiatan
            'kegiatanLabels',
            'kegiatanTotals',
            'kegiatanPeserta', 

            // Analitik Faktor
            'faktorLabels',
            'faktorTotals',

            // Insight
            'topFaktor',
            
            // Tracking Verifikasi Baru
            'trackingData', 
            'trackPending', 
            'trackApproved', 
            'trackRevisi'
        ));
    }
}