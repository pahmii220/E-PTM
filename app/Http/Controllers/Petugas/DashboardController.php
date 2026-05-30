<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\Pasien;
use App\Models\Rujukan;
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
           DEFAULT VALUE
        ====================== */
        $totalPasien   = 0;
        $totalDeteksi  = 0;
        $totalFaktor   = 0;
        $highRiskCount = 0;

        // Statistik
        $totalPeserta = 0;

        // Grafik
        $monthLabels  = collect();
        $monthTotals  = collect();

        $weeklyLabels = collect();
        $weeklyTotals = collect();

        $dailyLabels  = collect();
        $dailyTotals  = collect();

        // Analitik
        $puskesmasLabels = collect();
        $puskesmasTotals = collect();

        $faktorLabels = collect();
        $faktorTotals = collect();

        // Insight
        $topPuskesmas = '-';
        $topFaktor    = '-';

        try {

            /* =====================
               PASIEN
            ====================== */
            if (class_exists(Pasien::class)) {

                $totalPasien  = Pasien::count();
                $totalPeserta = $totalPasien;
            }

            /* =====================
               DETEKSI DINI
            ====================== */
            if (class_exists(DeteksiDiniPTM::class)) {

                $totalDeteksi = DeteksiDiniPTM::count();

                if (Schema::hasColumn('deteksi_dini_ptm', 'hasil_skrining')) {

                    $highRiskCount = DeteksiDiniPTM::where(
                        'hasil_skrining',
                        'Risiko Tinggi'
                    )->count();
                }
            }

            /* =====================
               FAKTOR RISIKO
            ====================== */
            if (class_exists(FaktorResikoPtM::class)) {

                $totalFaktor = FaktorResikoPtM::count();

            } elseif (Schema::hasTable('faktor_resiko_ptm')) {

                $totalFaktor = DB::table('faktor_resiko_ptm')->count();
            }

            /* =====================
               TREN BULANAN
            ====================== */
            $year = now()->year;

            if (class_exists(Pasien::class)) {

                $monthlyData = Pasien::select(
                        DB::raw('MONTH(dibuat_pada) as bulan'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->whereYear('dibuat_pada', $year)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get();

                // Label bulan
                $monthLabels = collect(range(1, 12))->map(function ($m) {
                    return Carbon::create()->month($m)->translatedFormat('F');
                });

                // Total per bulan
                $monthTotals = collect(range(1, 12))->map(function ($m) use ($monthlyData) {
                    return $monthlyData->firstWhere('bulan', $m)->total ?? 0;
                });
            }

            /* =====================
               DATA MINGGUAN
            ====================== */
            if (class_exists(Pasien::class)) {

                for ($i = 6; $i >= 0; $i--) {

                    $date = Carbon::today()->subDays($i);

                    $weeklyLabels->push(
                        $date->translatedFormat('D')
                    );

                    $weeklyTotals->push(
                        Pasien::whereDate('dibuat_pada', $date)->count()
                    );
                }
            }

            /* =====================
               DATA HARIAN
            ====================== */
            for ($i = 0; $i < 24; $i++) {

                $dailyLabels->push(sprintf('%02d:00', $i));

                $dailyTotals->push(
                    Pasien::whereDate('dibuat_pada', today())
                        ->whereRaw('HOUR(dibuat_pada) = ?', [$i])
                        ->count()
                );
            }

            /* =====================
               ANALITIK PUSKESMAS
            ====================== */
            if (
                Schema::hasTable('deteksi_dini_ptm') &&
                Schema::hasTable('puskesmas')
            ) {

                $puskesmasData = DB::table('deteksi_dini_ptm')

                    ->join(
                        'puskesmas',
                        'deteksi_dini_ptm.puskesmas_id',
                        '=',
                        'puskesmas.id'
                    )

                    ->select(
                        'puskesmas.nama_puskesmas',
                        DB::raw('COUNT(*) as total')
                    )

                    ->groupBy('puskesmas.nama_puskesmas')

                    ->get();

                $puskesmasLabels = $puskesmasData->pluck('nama_puskesmas');

                $puskesmasTotals = $puskesmasData->pluck('total');

                // Insight
                $topPuskesmas = optional(
                    $puskesmasData->sortByDesc('total')->first()
                )->nama_puskesmas ?? '-';
            }

            /* =====================
               ANALITIK FAKTOR RISIKO
            ====================== */
            if (Schema::hasTable('faktor_resiko_ptm')) {

                $faktorLabels = collect([
                    'Merokok',
                    'Alkohol',
                    'Kurang Aktivitas Fisik'
                ]);

                $faktorTotals = collect([

                    DB::table('faktor_resiko_ptm')
                        ->where('merokok', 'Ya')
                        ->count(),

                    DB::table('faktor_resiko_ptm')
                        ->where('alkohol', 'Ya')
                        ->count(),

                    DB::table('faktor_resiko_ptm')
                        ->where('kurang_aktivitas_fisik', 'Ya')
                        ->count()

                ]);

                // Insight faktor terbesar
                $topIndex = $faktorTotals->search(
                    $faktorTotals->max()
                );

                $topFaktor = $faktorLabels[$topIndex] ?? '-';
            }

        } catch (\Throwable $e) {

            Log::warning(
                'Dashboard Petugas Error: ' . $e->getMessage()
            );
        }

        return view('petugas.dashboard', compact(

            // Statistik
            'totalPasien',
            'totalDeteksi',
            'totalFaktor',
            'highRiskCount',
            'totalPeserta',

            // Grafik
            'monthLabels',
            'monthTotals',

            'weeklyLabels',
            'weeklyTotals',

            'dailyLabels',
            'dailyTotals',

            // Analitik
            'puskesmasLabels',
            'puskesmasTotals',

            'faktorLabels',
            'faktorTotals',

            // Insight
            'topPuskesmas',
            'topFaktor'
        ));
    }
}