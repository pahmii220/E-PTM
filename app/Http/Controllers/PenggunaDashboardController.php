<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\Petugas;

class PenggunaDashboardController extends Controller
{
    /**
     * Normalisasi nilai verification_status mentah -> one of:
     *   'approved', 'rejected', 'pending'
     */
    protected function normalizeVerificationStatus($raw): string
    {
        $r = strtolower(trim((string) $raw));

        // Variasi literal umum (tambahkan jika dataset mu punya istilah lain)
        $approvedVariants = ['approved','approve','ya','ok','sudah','verified','verified_by_dinkes','approved_by_dinkes','approve_by_dinkes'];
        $rejectedVariants  = ['rejected','reject','tolak','no','rejected_by_dinkes','rejected_by_admin'];
        $pendingVariants   = ['pending','wait','menunggu','waiting','baru','', null, 'null'];

        if (in_array($r, $approvedVariants, true)) return 'approved';
        if (in_array($r, $rejectedVariants, true)) return 'rejected';
        if (in_array($r, $pendingVariants, true)) return 'pending';

        // Toleransi substring: jika ada 'app' -> approved; 'rej'/'tolak' -> rejected
        if (strpos($r, 'app') !== false) return 'approved';
        if (strpos($r, 'rej') !== false || strpos($r, 'tolak') !== false) return 'rejected';

        // Default fallback
        return 'pending';
    }

    public function index(Request $request)
    {
        try {
            // --- Basic totals (defensive checks) ---
            $totalPasien  = class_exists(Pasien::class) ? Pasien::count() : 0;
            $totalDeteksi = class_exists(DeteksiDiniPTM::class) ? DeteksiDiniPTM::count() : 0;
            $totalFaktor  = class_exists(FaktorResikoPTM::class) ? FaktorResikoPTM::count() : 0;

            // --- Pending counts (safe: sum dari tabel yang ada) ---
            $pendingPasien = (Schema::hasTable('pasien') && Schema::hasColumn('pasien','verification_status'))
                ? Pasien::whereNotNull('verification_status')->get()->filter(function($p){
                    return $this->normalizeVerificationStatus($p->verification_status) === 'pending';
                })->count()
                : 0;

            $pendingDeteksi = (Schema::hasTable('deteksi_dini_ptm') && Schema::hasColumn('deteksi_dini_ptm','verification_status'))
                ? DeteksiDiniPTM::whereNotNull('verification_status')->get()->filter(function($d){
                    return $this->normalizeVerificationStatus($d->verification_status) === 'pending';
                })->count()
                : 0;

            $pendingFaktor = (Schema::hasTable('faktor_resiko_ptm') && Schema::hasColumn('faktor_resiko_ptm','verification_status'))
                ? FaktorResikoPTM::whereNotNull('verification_status')->get()->filter(function($f){
                    return $this->normalizeVerificationStatus($f->verification_status) === 'pending';
                })->count()
                : 0;

            $pendingTotal = $pendingPasien + $pendingDeteksi + $pendingFaktor;

            // -----------------------
            // Status filter (query param) - normalisasi input
            // -----------------------
            $statusRaw = $request->query('status');
            $statusFilter = null;
            if ($statusRaw) {
                $statusNorm = $this->normalizeVerificationStatus($statusRaw);
                if (in_array($statusNorm, ['approved','rejected','pending'])) {
                    $statusFilter = $statusNorm;
                }
            }

            // -----------------------
            // Recent Deteksi (ambil lebih banyak jika filter, lalu filter di PHP untuk toleransi)
            // -----------------------
            $recentQuery = DeteksiDiniPTM::with(['pasien','petugas'])->orderBy('created_at','desc');
            if ($statusFilter) {
                // ambil lebih banyak lalu filter supaya variasi string tidak mengganggu
                $recentDeteksi = $recentQuery->limit(50)->get()->filter(function($d) use ($statusFilter) {
                    return $this->normalizeVerificationStatus($d->verification_status) === $statusFilter;
                })->take(8);
            } else {
                $recentDeteksi = $recentQuery->limit(8)->get();
            }

            // Normalisasi property verification_status pada setiap item supaya view konsisten
            $recentDeteksi = $recentDeteksi->map(function($item) {
                $item->verification_status = $this->normalizeVerificationStatus($item->verification_status);
                return $item;
            });

            // -----------------------
            // Top petugas (defensive)
            // -----------------------
            $topPetugas = collect();
            try {
                if (class_exists(Petugas::class)) {
                    // asumsi relasi petugas->deteksiDiniPTM() ada; jika tidak, fallback aman
                    if (method_exists(Petugas::class, 'deteksiDiniPTM') || method_exists(Petugas::class, 'deteksi_dini_ptm')) {
                        $topPetugas = Petugas::withCount('deteksiDiniPTM')
                            ->orderByDesc('deteksi_dini_ptm_count')
                            ->limit(5)
                            ->get();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('topPetugas lookup failed: '.$e->getMessage());
                $topPetugas = collect();
            }

            // -----------------------
            // All Deteksi (paginate) dengan eager load
            // -----------------------
            $deteksiModel = new DeteksiDiniPTM;
            $with = ['pasien','petugas'];
            if (method_exists($deteksiModel,'faktor_resiko')) $with[] = 'faktor_resiko';
            if (method_exists($deteksiModel,'faktorResiko')) $with[] = 'faktorResiko'; // alternative naming
            $allDeteksi = DeteksiDiniPTM::with(array_unique($with))->orderBy('created_at','desc')->paginate(25);

            // -----------------------
            // verifCounts: ambil grouped raw values dari DB dan normalisasi ke 3 bucket
            // -----------------------
           $verifCounts = ['approved'=>0,'rejected'=>0,'pending'=>0];

$tables = [
    'deteksi_dini_ptm',
    'faktor_resiko_ptm',
    'pasien',
];

foreach ($tables as $tbl) {
    if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'verification_status')) {
        $rows = DB::table($tbl)
            ->select('verification_status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('verification_status')
            ->get();

        foreach ($rows as $r) {
            $raw = strtolower(trim((string)$r->verification_status));
            if (in_array($raw, ['approved','approve','ya','ok','sudah','verified','1'], true)) {
                $verifCounts['approved'] += (int)$r->cnt;
            } elseif (in_array($raw, ['rejected','reject','tolak','no','0'], true)) {
                $verifCounts['rejected'] += (int)$r->cnt;
            } else {
                $verifCounts['pending'] += (int)$r->cnt;
            }
        }
    }
}
$pendingTotal = $verifCounts['pending'];

            // -----------------------
            // Chart data last 7 days
            // -----------------------
            $chartLabels = [];
            $chartDeteksi = [];
            $chartFaktor  = [];
            $today = Carbon::now();
            for ($i = 6; $i >= 0; $i--) {
                $d = $today->copy()->subDays($i);
                $chartLabels[] = $d->format('d M');
                try {
                    $chartDeteksi[] = DeteksiDiniPTM::whereDate('created_at', $d->toDateString())->count();
                } catch (\Throwable $e) {
                    $chartDeteksi[] = 0;
                }
                try {
                    $chartFaktor[] = (Schema::hasTable('faktor_resiko_ptm')) ? FaktorResikoPTM::whereDate('created_at', $d->toDateString())->count() : 0;
                } catch (\Throwable $e) {
                    $chartFaktor[] = 0;
                }
            }

            $chartData = $chartDeteksi;
            $weeklyTotal = array_sum($chartDeteksi) + array_sum($chartFaktor);
            $avgPerDay = $weeklyTotal ? round($weeklyTotal / max(1, count($chartDeteksi)), 1) : 0;
            $lastUpdatedAt = DeteksiDiniPTM::orderBy('updated_at','desc')->value('updated_at') ?? Carbon::now();

            // -----------------------
            // Return view (semua variabel dipasok)
            // -----------------------
            return view('pengguna.dashboard', compact(
                'totalPasien','totalDeteksi','totalFaktor',
                'pendingPasien','pendingDeteksi','pendingFaktor','pendingTotal',
                'recentDeteksi','topPetugas','allDeteksi',
                'verifCounts','chartLabels','chartData','chartDeteksi','chartFaktor','avgPerDay','weeklyTotal','lastUpdatedAt',
                'statusFilter'
            ));
        } catch (\Exception $e) {
            Log::error('Pengguna dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return abort(500, 'Terjadi kesalahan saat memuat dashboard. Periksa log untuk detail.');
        }
    }
}
