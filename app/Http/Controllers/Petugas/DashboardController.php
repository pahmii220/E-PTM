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
           DEFAULT VALUE (AMAN)
        ====================== */
        $totalPasien   = 0;
        $totalDeteksi  = 0;
        $totalFaktor   = 0;
        $highRiskCount = 0;

        // 🔥 DATA GRAFIK BULANAN
        $totalPeserta = 0;
        $monthLabels  = collect();
        $monthTotals  = collect();

        try {
            /* =====================
               PASIEN
            ====================== */
            if (class_exists(Pasien::class)) {
                $totalPasien   = Pasien::count();
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
               🔥 TREN BULANAN PESERTA (DATA ASLI)
            ====================== */
            $year = now()->year;

            if (class_exists(Pasien::class)) {
                $monthlyData = Pasien::select(
                        DB::raw('MONTH(created_at) as bulan'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->whereYear('created_at', $year)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get();

                // Label bulan Jan–Des
                $monthLabels = collect(range(1, 12))->map(function ($m) {
                    return Carbon::create()->month($m)->translatedFormat('F');
                });

                // Total peserta per bulan (default 0)
                $monthTotals = collect(range(1, 12))->map(function ($m) use ($monthlyData) {
                    return $monthlyData->firstWhere('bulan', $m)->total ?? 0;
                });
            }

        } catch (\Throwable $e) {
            Log::warning('Dashboard Petugas Error: ' . $e->getMessage());
        }

        return view('petugas.dashboard', compact(
            'totalPasien',
            'totalDeteksi',
            'totalFaktor',
            'highRiskCount',
            'totalPeserta',
            'monthLabels',
            'monthTotals'
        ));
    }
}
