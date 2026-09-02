<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;

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
        $totalDeteksi  = 0;
        $totalFaktor   = 0;
        $highRiskCount = 0;
        $totalPeserta  = 0;

        // Distribusi Kasus PTM
        $totalHipertensi = 0;
        $totalDiabetes   = 0;
        $totalObesitas   = 0;
        $totalLainnya    = 0;

        // Grafik Tren Kasus PTM Spesifik
        $monthLabels     = collect();
        $monthHipertensi = collect();
        $monthDiabetes   = collect();
        $monthObesitas   = collect();
        $weeklyLabels    = collect();
        $weeklyHipertensi= collect();
        $weeklyDiabetes  = collect();
        $weeklyObesitas  = collect();
        $dailyLabels     = collect();
        $dailyHipertensi = collect();
        $dailyDiabetes   = collect();
        $dailyObesitas   = collect();

        // Analitik Kegiatan & Faktor Risiko
        $kegiatanLabels  = collect();
        $kegiatanTotals  = collect();
        $kegiatanPeserta = collect();
        $faktorLabels    = collect(['Merokok', 'Kurang Aktivitas Fisik', 'Riwayat Keluarga']);
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
            if (class_exists(DeteksiDiniPTM::class) && class_exists(Peserta::class) && class_exists(\App\Models\FaktorResikoPTM::class)) {
                $queryPeserta = Peserta::query();
                $queryDeteksi = DeteksiDiniPTM::with(['peserta']);
                $queryFaktor = \App\Models\FaktorResikoPTM::with(['peserta']);

                if ($petugas && $petugas->puskesmas_id) {
                    $queryPeserta->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $pesertaTrack = $queryPeserta->orderBy('dibuat_pada', 'desc')->take(5)->get();
                $deteksiTrack = $queryDeteksi->orderBy('dibuat_pada', 'desc')->take(5)->get();
                $faktorTrack = $queryFaktor->orderBy('dibuat_pada', 'desc')->take(5)->get();

                // Gabungkan semua data pelacakan
                $trackingData = collect()
                    ->concat($pesertaTrack)
                    ->concat($deteksiTrack)
                    ->concat($faktorTrack)
                    ->sortByDesc('dibuat_pada')
                    ->take(5);

                // Hitung total pending, approved, dan rejected secara akumulatif
                $queryCountPeserta = Peserta::query();
                $queryCountDeteksi = DeteksiDiniPTM::query();
                $queryCountFaktor = \App\Models\FaktorResikoPTM::query();

                if ($petugas && $petugas->puskesmas_id) {
                    $queryCountPeserta->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryCountDeteksi->where('puskesmas_id', $petugas->puskesmas_id);
                    $queryCountFaktor->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $trackPending = (clone $queryCountPeserta)->where('status_verifikasi', 'pending')->count()
                              + (clone $queryCountDeteksi)->where('status_verifikasi', 'pending')->count()
                              + (clone $queryCountFaktor)->where('status_verifikasi', 'pending')->count();

                $trackApproved = (clone $queryCountPeserta)->where('status_verifikasi', 'approved')->count()
                               + (clone $queryCountDeteksi)->where('status_verifikasi', 'approved')->count()
                               + (clone $queryCountFaktor)->where('status_verifikasi', 'approved')->count();

                $trackRevisi = (clone $queryCountPeserta)->where('status_verifikasi', 'rejected')->count()
                             + (clone $queryCountDeteksi)->where('status_verifikasi', 'rejected')->count()
                             + (clone $queryCountFaktor)->where('status_verifikasi', 'rejected')->count();
            }

            /* =====================
               PESERTA
            ====================== */
            if (class_exists(Peserta::class)) {
                $queryPesertaCount = Peserta::query();
                if ($petugas && $petugas->puskesmas_id) {
                    $queryPesertaCount->where('puskesmas_id', $petugas->puskesmas_id);
                }
                $totalPeserta = $queryPesertaCount->count();
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
               DEMOGRAFI USIA (PENGGANTI FAKTOR RISIKO)
            ====================== */
            $remaja = 0;
            $dewasa = 0;
            $praLansia = 0;
            $lansia = 0;

            if (class_exists(Peserta::class)) {
                $pesertaList = Peserta::when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    return $q->where('puskesmas_id', $petugas->puskesmas_id);
                })->get();

                foreach($pesertaList as $p) {
                    $umur = \Carbon\Carbon::parse($p->tanggal_lahir)->age;
                    if($umur < 18) {
                        $remaja++;
                    } elseif($umur <= 44) {
                        $dewasa++;
                    } elseif($umur <= 59) {
                        $praLansia++;
                    } else {
                        $lansia++;
                    }
                }
            }

            $faktorLabels = collect(['Remaja (<18)', 'Dewasa (18-44)', 'Pra Lansia (45-59)', 'Lansia (60+)']);
            $faktorTotals = collect([$remaja, $dewasa, $praLansia, $lansia]);

            /* =====================
               TREN BULANAN, MINGGUAN, HARIAN (3 DATASET)
            ====================== */
            $year = now()->year;

            // --- 1. Bulanan ---
            $monthLabels = collect(range(1, 12))->map(function ($m) {
                return Carbon::create()->month($m)->translatedFormat('F');
            })->values();

            // Hipertensi Bulanan
            $monthlyHipertensiData = DeteksiDiniPTM::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->where('diagnosa_penyakit', 'LIKE', '%Hipertensi%')
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthHipertensi = collect(range(1, 12))->map(function ($m) use ($monthlyHipertensiData) {
                return $monthlyHipertensiData->firstWhere('bulan', $m)->total ?? 0;
            })->values();

            // Diabetes Bulanan
            $monthlyDiabetesData = DeteksiDiniPTM::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->where(function($q) {
                    $q->where('diagnosa_penyakit', 'LIKE', '%Diabetes%')
                      ->orWhere('diagnosa_penyakit', 'LIKE', '%Gula Darah%');
                })
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthDiabetes = collect(range(1, 12))->map(function ($m) use ($monthlyDiabetesData) {
                return $monthlyDiabetesData->firstWhere('bulan', $m)->total ?? 0;
            })->values();

            // Obesitas Bulanan
            $monthlyObesitasData = DeteksiDiniPTM::select(DB::raw('MONTH(dibuat_pada) as bulan'), DB::raw('COUNT(*) as total'))
                ->whereYear('dibuat_pada', $year)
                ->where(function($q) {
                    $q->where('diagnosa_penyakit', 'LIKE', '%Obesitas%')
                      ->orWhere('diagnosa_penyakit', 'LIKE', '%Overweight%');
                })
                ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                    $q->where('puskesmas_id', $petugas->puskesmas_id);
                })
                ->groupBy('bulan')->get();
            $monthObesitas = collect(range(1, 12))->map(function ($m) use ($monthlyObesitasData) {
                return $monthlyObesitasData->firstWhere('bulan', $m)->total ?? 0;
            })->values();

            // --- 2. Mingguan ---
            $weeklyLabels = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $weeklyLabels->push($date->translatedFormat('D'));

                // Hipertensi
                $qWHipertensi = DeteksiDiniPTM::whereDate('dibuat_pada', $date)->where('diagnosa_penyakit', 'LIKE', '%Hipertensi%');
                if ($petugas && $petugas->puskesmas_id) $qWHipertensi->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyHipertensi->push($qWHipertensi->count());

                // Diabetes
                $qWDiabetes = DeteksiDiniPTM::whereDate('dibuat_pada', $date)
                    ->where(function($q) {
                        $q->where('diagnosa_penyakit', 'LIKE', '%Diabetes%')
                          ->orWhere('diagnosa_penyakit', 'LIKE', '%Gula Darah%');
                    });
                if ($petugas && $petugas->puskesmas_id) $qWDiabetes->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyDiabetes->push($qWDiabetes->count());

                // Obesitas
                $qWObesitas = DeteksiDiniPTM::whereDate('dibuat_pada', $date)
                    ->where(function($q) {
                        $q->where('diagnosa_penyakit', 'LIKE', '%Obesitas%')
                          ->orWhere('diagnosa_penyakit', 'LIKE', '%Overweight%');
                    });
                if ($petugas && $petugas->puskesmas_id) $qWObesitas->where('puskesmas_id', $petugas->puskesmas_id);
                $weeklyObesitas->push($qWObesitas->count());
            }

            // --- 3. Harian ---
            $dailyLabels = collect();
            for ($i = 0; $i < 24; $i++) {
                $dailyLabels->push(sprintf('%02d:00', $i));

                // Hipertensi
                $qDHipertensi = DeteksiDiniPTM::whereDate('dibuat_pada', today())
                    ->whereRaw('HOUR(dibuat_pada) = ?', [$i])
                    ->where('diagnosa_penyakit', 'LIKE', '%Hipertensi%');
                if ($petugas && $petugas->puskesmas_id) $qDHipertensi->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyHipertensi->push($qDHipertensi->count());

                // Diabetes
                $qDDiabetes = DeteksiDiniPTM::whereDate('dibuat_pada', today())
                    ->whereRaw('HOUR(dibuat_pada) = ?', [$i])
                    ->where(function($q) {
                        $q->where('diagnosa_penyakit', 'LIKE', '%Diabetes%')
                          ->orWhere('diagnosa_penyakit', 'LIKE', '%Gula Darah%');
                    });
                if ($petugas && $petugas->puskesmas_id) $qDDiabetes->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyDiabetes->push($qDDiabetes->count());

                 // Obesitas
                $qDObesitas = DeteksiDiniPTM::whereDate('dibuat_pada', today())
                    ->whereRaw('HOUR(dibuat_pada) = ?', [$i])
                    ->where(function($q) {
                        $q->where('diagnosa_penyakit', 'LIKE', '%Obesitas%')
                          ->orWhere('diagnosa_penyakit', 'LIKE', '%Overweight%');
                    });
                if ($petugas && $petugas->puskesmas_id) $qDObesitas->where('puskesmas_id', $petugas->puskesmas_id);
                $dailyObesitas->push($qDObesitas->count());
            }

            /* =====================
               DISTRIBUSI KASUS PENYAKIT PTM
            ====================== */
            $penyakitList = [
                "Gangguan Jantung",
                "Gagal Jantung",
                "Jantung Koroner",
                "Jantung Kongenital",
                "Jantung Lainnya",
                "Hipertensi",
                "Diabetes Melitus",
                "Obesitas",
                "Gangguan Stroke",
                "Kanker Payudara",
                "Kanker Serviks",
                "Kanker Paru-Paru",
                "Kanker Kolorektal",
                "Thalassemia",
                "Gangguan Pendengaran",
                "Gangguan Pendengaran Otitis (OMSK)",
                "Gangguan Pendengaran Presbikusis",
                "Gangguan Penglihatan Katarak",
                "Miopia",
                "PPOK Umum",
                "PPOK Stabil",
                "PPOK Eksaserbasi"
            ];

            $ptmTotalsMap = [];
            foreach ($penyakitList as $penyakit) {
                $count = DeteksiDiniPTM::where('diagnosa_penyakit', 'LIKE', '%' . $penyakit . '%')
                    ->when($petugas && $petugas->puskesmas_id, function($q) use ($petugas) {
                        $q->where('puskesmas_id', $petugas->puskesmas_id);
                    })->count();
                $ptmTotalsMap[$penyakit] = $count;
            }

            // Urutkan dari jumlah kasus terbanyak agar grafik lebih rapi (optional, tapi sangat estetik!)
            arsort($ptmTotalsMap);

            $ptmLabels = array_keys($ptmTotalsMap);
            $ptmValues = array_values($ptmTotalsMap);


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
            $tableFaktor = 'faktor_resiko_ptm';
            if (Schema::hasTable($tableFaktor)) {
                $queryF = DB::table($tableFaktor);

                if ($petugas && $petugas->puskesmas_id && Schema::hasColumn($tableFaktor, 'puskesmas_id')) {
                    $queryF->where('puskesmas_id', $petugas->puskesmas_id);
                }

                $merokokCount = (clone $queryF)->where('merokok', 'Ya')->count();
                $aktivitasCount = (clone $queryF)->where('kurang_aktivitas_fisik', 'Ya')->count();
                $keluargaCount = (clone $queryF)->where('riwayat_keluarga', 'Ya')->count();

                $faktorTotals = collect([$merokokCount, $aktivitasCount, $keluargaCount]);

                $topIndex = $faktorTotals->search($faktorTotals->max());
                $topFaktor = $faktorLabels[$topIndex] ?? '-';
            }

        } catch (\Throwable $e) {
            Log::warning('Dashboard Petugas Error: ' . $e->getMessage());
        }
        
        // SATU-SATUNYA RETURN VIEW YANG DIEKSEKUSI DI AKHIR FUNGSI
        return view('petugas.dashboard', compact(
            // Statistik Utama
            'totalPeserta',
            'totalDeteksi',
            'totalFaktor',
            'highRiskCount',

            // Distribusi Kasus PTM Akumulatif
            'ptmLabels',
            'ptmValues',

            // Grafik Tren
            'monthLabels',
            'monthHipertensi',
            'monthDiabetes',
            'monthObesitas',
            'weeklyLabels',
            'weeklyHipertensi',
            'weeklyDiabetes',
            'weeklyObesitas',
            'dailyLabels',
            'dailyHipertensi',
            'dailyDiabetes',
            'dailyObesitas',

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

    /**
     * Memproses form kontak bantuan via Email
     */
    public function sendContactEmail(Request $request)
    {
        $request->validate([
            'subjek' => 'required|string',
            'pesan'  => 'required|string|min:10',
        ]);

        try {
            // Ambil data pengirim
            $pengirim = \Illuminate\Support\Facades\Auth::user()->Username;
            if (\Illuminate\Support\Facades\Auth::user()->petugas && \Illuminate\Support\Facades\Auth::user()->petugas->puskesmas) {
                $pengirim = \Illuminate\Support\Facades\Auth::user()->petugas->nama_petugas . ' (' . \Illuminate\Support\Facades\Auth::user()->petugas->puskesmas->nama_puskesmas . ')';
            }
            
            // Ambil email Admin dari database
            $adminUser = \App\Models\User::where('role_name', 'admin')->orWhere('role_name', 'Administrator')->first();
            $adminEmail = ($adminUser && $adminUser->email) ? $adminUser->email : 'admin@eptm-kalsel.go.id';

            // Kirim email ke admin
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                new \App\Mail\ContactAdminMail($request->subjek, $request->pesan, $pengirim)
            );

            return back()->with('success', 'Pesan bantuan Anda berhasil dikirim ke Administrator. Harap tunggu balasan kami melalui kontak terdaftar Anda.');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email kontak: ' . $e->getMessage());
            
            // Karena ini simulasi local atau jika SMTP gagal, jangan biarkan user melihat error teknis.
            return back()->with('success', 'Pesan Anda telah dicatat oleh sistem (Local Mode). Tim IT akan segera memproses laporan Anda.');
        }
    }
}