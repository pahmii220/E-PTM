<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\Petugas;

class PenggunaDashboardController extends Controller
{
    /**
     * Normalisasi nilai status_verifikasi mentah -> one of:
     *   'approved', 'rejected', 'pending'
     */
    protected function normalizeVerificationStatus($raw): string
    {
        $r = strtolower(trim((string) $raw));

        // Variasi literal umum (tambahkan jika dataset mu punya istilah lain)
        $approvedVariants = ['approved','approve','ya','ok','sudah','verified','verified_by_dinkes','approved_by_dinkes','approve_by_dinkes','terverifikasi'];
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
            $totalPeserta = class_exists(Peserta::class) ? Peserta::count() : 0;
            $totalDeteksi = class_exists(DeteksiDiniPTM::class) ? DeteksiDiniPTM::count() : 0;
            $totalFaktor  = class_exists(FaktorResikoPTM::class) ? FaktorResikoPTM::count() : 0;

            // --- Pending counts (safe: sum dari tabel yang ada) ---
            $pendingPeserta = (Schema::hasTable('peserta') && Schema::hasColumn('peserta','status_verifikasi'))
                ? Peserta::whereNotNull('status_verifikasi')->get()->filter(function($p){
                    return $this->normalizeVerificationStatus($p->status_verifikasi) === 'pending';
                })->count()
                : 0;

            $pendingDeteksi = (Schema::hasTable('deteksi_dini_ptm') && Schema::hasColumn('deteksi_dini_ptm','status_verifikasi'))
                ? DeteksiDiniPTM::whereNotNull('status_verifikasi')->get()->filter(function($d){
                    return $this->normalizeVerificationStatus($d->status_verifikasi) === 'pending';
                })->count()
                : 0;

            $pendingFaktor = (Schema::hasTable('faktor_resiko_ptm') && Schema::hasColumn('faktor_resiko_ptm','status_verifikasi'))
                ? FaktorResikoPTM::whereNotNull('status_verifikasi')->get()->filter(function($f){
                    return $this->normalizeVerificationStatus($f->status_verifikasi) === 'pending';
                })->count()
                : 0;

            $pendingTotal = $pendingPeserta + $pendingDeteksi + $pendingFaktor;

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
            $recentQuery = DeteksiDiniPTM::with(['peserta','petugas'])->orderBy('dibuat_pada','desc');
            if ($statusFilter) {
                // ambil lebih banyak lalu filter supaya variasi string tidak mengganggu
                $recentDeteksi = $recentQuery->limit(50)->get()->filter(function($d) use ($statusFilter) {
                    return $this->normalizeVerificationStatus($d->status_verifikasi) === $statusFilter;
                })->take(8);
            } else {
                $recentDeteksi = $recentQuery->limit(8)->get();
            }

            // Normalisasi property status_verifikasi pada setiap item supaya view konsisten
            $recentDeteksi = $recentDeteksi->map(function($item) {
                $item->status_verifikasi = $this->normalizeVerificationStatus($item->status_verifikasi);
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
            $with = ['peserta','petugas'];
            if (method_exists($deteksiModel,'faktor_resiko')) $with[] = 'faktor_resiko';
            if (method_exists($deteksiModel,'faktorResiko')) $with[] = 'faktorResiko'; // alternative naming
            $allDeteksi = DeteksiDiniPTM::with(array_unique($with))->orderBy('dibuat_pada','desc')->paginate(25);

            // -----------------------
            // verifCounts: ambil grouped raw values dari DB dan normalisasi ke 3 bucket
            // -----------------------
           $verifCounts = ['approved'=>0,'rejected'=>0,'pending'=>0];

$tables = [
    'deteksi_dini_ptm',
    'faktor_resiko_ptm',
    'peserta',
];

foreach ($tables as $tbl) {
    if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'status_verifikasi')) {
        $rows = DB::table($tbl)
            ->select('status_verifikasi', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status_verifikasi')
            ->get();

        foreach ($rows as $r) {
            $raw = strtolower(trim((string)$r->status_verifikasi));
            if (in_array($raw, ['approved','approve','ya','ok','sudah','verified','1','terverifikasi'], true)) {
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
                    $chartDeteksi[] = DeteksiDiniPTM::whereDate('dibuat_pada', $d->toDateString())->count();
                } catch (\Throwable $e) {
                    $chartDeteksi[] = 0;
                }
                try {
                    $chartFaktor[] = (Schema::hasTable('faktor_resiko_ptm')) ? FaktorResikoPTM::whereDate('dibuat_pada', $d->toDateString())->count() : 0;
                } catch (\Throwable $e) {
                    $chartFaktor[] = 0;
                }
            }

            $chartData = $chartDeteksi;
            $weeklyTotal = array_sum($chartDeteksi) + array_sum($chartFaktor);
            $avgPerDay = $weeklyTotal ? round($weeklyTotal / max(1, count($chartDeteksi)), 1) : 0;
            $lastUpdatedAt = DeteksiDiniPTM::orderBy('diubah_pada','desc')->value('diubah_pada') ?? Carbon::now();


            $rekapPuskesmas = DB::table('puskesmas')
    ->leftJoin('petugas', 'petugas.puskesmas_id', '=', 'puskesmas.id')
    ->leftJoin('deteksi_dini_ptm', 'deteksi_dini_ptm.petugas_id', '=', 'petugas.id')
    ->leftJoin('tindak_lanjut_ptm', 'tindak_lanjut_ptm.deteksi_dini_id', '=', 'deteksi_dini_ptm.id')
    ->select(
        'puskesmas.nama_puskesmas',
        DB::raw('COUNT(DISTINCT petugas.id) as total_petugas'),
        DB::raw('COUNT(DISTINCT deteksi_dini_ptm.id) as total_deteksi'),
        DB::raw('COUNT(DISTINCT tindak_lanjut_ptm.id) as total_tindak_lanjut')
    )
    ->groupBy('puskesmas.nama_puskesmas')
    ->orderBy('puskesmas.nama_puskesmas')
    ->get();

            // -----------------------
            // Kepatuhan Pelaporan Puskesmas (Dengan Filter)
            // -----------------------
            $filterKepatuhanBulan = $request->input('kepatuhan_bulan', Carbon::now()->format('m'));
            $filterKepatuhanTahun = $request->input('kepatuhan_tahun', Carbon::now()->format('Y'));

            // Konversi ke Carbon
            $waktuKepatuhan = Carbon::createFromDate($filterKepatuhanTahun, $filterKepatuhanBulan, 1);
            $startDateCur   = $waktuKepatuhan->copy()->startOfMonth()->toDateString();
            $endDateCur     = $waktuKepatuhan->copy()->endOfMonth()->toDateString();

            $rekapKepatuhan = DB::table('puskesmas')
                ->select(
                    'puskesmas.id',
                    'puskesmas.nama_puskesmas',
                    'puskesmas.kecamatan',
                    DB::raw("(
                        SELECT COUNT(*) 
                        FROM deteksi_dini_ptm 
                        WHERE deteksi_dini_ptm.puskesmas_id = puskesmas.id 
                        AND deteksi_dini_ptm.tanggal_pemeriksaan BETWEEN '{$startDateCur}' AND '{$endDateCur}'
                    ) as total_skrining_bulan_ini"),
                    DB::raw("(
                        SELECT COUNT(*) 
                        FROM deteksi_dini_ptm 
                        WHERE deteksi_dini_ptm.puskesmas_id = puskesmas.id 
                        AND deteksi_dini_ptm.tanggal_pemeriksaan BETWEEN '{$startDateCur}' AND '{$endDateCur}'
                        AND deteksi_dini_ptm.status_verifikasi IN ('pending', 'approved', 'rejected')
                    ) as total_dilaporkan_bulan_ini")
                )
                ->orderBy('puskesmas.nama_puskesmas')
                ->get();

            // Query untuk peta sebaran puskesmas
            $puskesmasList = \App\Models\Puskesmas::whereNotNull('latitude')->whereNotNull('longitude')->withCount(['peserta', 'deteksiDini'])->get();

            // Return view (semua variabel dipasok)
            return view('pengguna.dashboard', compact(
                'totalPeserta','totalDeteksi','totalFaktor',
                'pendingPeserta','pendingDeteksi','pendingFaktor','pendingTotal',
                'recentDeteksi','topPetugas','allDeteksi',
                'verifCounts','chartLabels','chartData','chartDeteksi','chartFaktor','avgPerDay','weeklyTotal','lastUpdatedAt',
                'statusFilter', 'rekapPuskesmas', 'rekapKepatuhan', 'filterKepatuhanBulan', 'filterKepatuhanTahun', 'waktuKepatuhan',
                'puskesmasList'
            ));
        } catch (\Exception $e) {
            Log::error('Pengguna dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return abort(500, 'Terjadi kesalahan saat memuat dashboard. Periksa log untuk detail.');
        }
    }

    /**
     * Send Reminder Notification to Petugas Puskesmas
     */
    public function sendReminder(Request $request)
    {
        $request->validate([
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'bulan_nama'   => 'required|string'
        ]);

        $puskesmasId = $request->puskesmas_id;
        $bulanNama   = $request->bulan_nama;
        $puskesmas   = \App\Models\Puskesmas::findOrFail($puskesmasId);

        // Ambil user petugas di puskesmas ini
        $petugasUsers = \App\Models\User::where('role_name', 'petugas')
            ->whereHas('petugas', function($q) use ($puskesmasId) {
                $q->where('puskesmas_id', $puskesmasId);
            })->get();

        if ($petugasUsers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengingat. Tidak ada Petugas terdaftar di ' . $puskesmas->nama_puskesmas
            ], 404);
        }

        foreach ($petugasUsers as $user) {
            // Simpan data notifikasi langsung ke DB untuk keandalan instan
            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\TagihanLaporanNotification',
                'data' => [
                    'title' => 'Tagihan Laporan PTM',
                    'message' => 'Peringatan Dinkes: Laporan Register PTM periode ' . $bulanNama . ' belum dikirimkan. Harap segera lengkapi & kirim.',
                    'url' => route('petugas.laporan.index'),
                    'type' => 'warning'
                ],
                'read_at' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '🔔 Berhasil mengirimkan pengingat laporan bulanan ke ' . $puskesmas->nama_puskesmas
        ]);
    }
}
