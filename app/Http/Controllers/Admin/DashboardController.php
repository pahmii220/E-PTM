<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request; // Pastikan Request di-import untuk filter

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ===============================
        // 1. STATISTIK DASHBOARD UTAMA
        // ===============================
        $totalPengguna = User::count();
        $totalPetugas  = User::where('role_name', 'petugas')->count();
        $totalPeserta  = DB::table('peserta')->count();
        $totalDeteksi  = DB::table('deteksi_dini_ptm')->count();

        // ===============================
        // FILTER PERIODE (DARI REQUEST)
        // ===============================
        $periode = $request->input('periode', 'tahun_ini'); // Default: tahun ini
        $queryPeserta = DB::table('peserta');
        $queryDeteksi = DB::table('deteksi_dini_ptm');

        if ($periode == 'bulan_ini') {
            $queryPeserta->whereMonth('dibuat_pada', date('m'))->whereYear('dibuat_pada', date('Y'));
            $queryDeteksi->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
        } elseif ($periode == '3_bulan') {
            $queryPeserta->where('dibuat_pada', '>=', now()->subMonths(3));
            $queryDeteksi->where('created_at', '>=', now()->subMonths(3));
        } else {
            // tahun_ini
            $queryPeserta->whereYear('dibuat_pada', date('Y'));
            $queryDeteksi->whereYear('created_at', date('Y'));
        }

        // ===============================
        // 2. CHART 1: TREN KASUS (Berdasarkan Peserta Baru per Bulan)
        // ===============================
        // Kita clone query agar tidak mengganggu query aslinya
        $trenPTM = (clone $queryPeserta)
            ->select(
                DB::raw('MONTH(dibuat_pada) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('MONTH(dibuat_pada)'))
            ->orderBy('bulan')
            ->get();

        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = [];
        $data   = [];

        // Jika filter "bulan ini", mungkin kita hanya tampilkan 1 bulan atau mingguan (disini kita tetap pakai format bulan agar aman)
        foreach ($namaBulan as $key => $bulan) {
            $labels[] = $bulan;
            $found = $trenPTM->firstWhere('bulan', $key);
            $data[] = $found ? $found->total : 0;
        }

        // ===============================
        // 3. CHART 2: DEMOGRAFI GENDER
        // ===============================
        $genderStats = (clone $queryPeserta)
            ->select('jenis_kelamin', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get();

        $labelsGender = ['Laki-laki', 'Perempuan'];
        // Pastikan huruf besar/kecil 'L' dan 'P' sesuai dengan isi database Anda
        $countLaki = $genderStats->whereIn('jenis_kelamin', ['L', 'Laki-laki', 'laki-laki'])->sum('total');
        $countPerempuan = $genderStats->whereIn('jenis_kelamin', ['P', 'Perempuan', 'perempuan'])->sum('total');
        
        $dataGender = [$countLaki, $countPerempuan];

        // ===============================
        // 4. CHART 3: SEBARAN WILAYAH (Berdasarkan Puskesmas)
        // Asumsi: Tabel 'peserta' punya 'puskesmas_id' dan berelasi ke tabel 'puskesmas'
        // Jika deteksi dini yang dihitung, ganti query ke tabel deteksi dini.
        // ===============================
        $wilayahStats = DB::table('peserta')
            ->join('puskesmas', 'peserta.puskesmas_id', '=', 'puskesmas.id')
            ->select('puskesmas.nama_puskesmas', DB::raw('COUNT(peserta.id) as total'))
            ->groupBy('puskesmas.id', 'puskesmas.nama_puskesmas')
            ->orderByDesc('total')
            ->limit(5) // Ambil 5 Puskesmas terbanyak agar grafik tidak terlalu padat
            ->get();

        $labelsWilayah = $wilayahStats->pluck('nama_puskesmas')->toArray();
        $dataWilayah   = $wilayahStats->pluck('total')->toArray();

        // ===============================
        // 5. PETA SEBARAN, KEPADATAN & CLUSTERING PUSKESMAS
        // ===============================
        $mapPuskesmasData = \App\Models\Puskesmas::whereNotNull('latitude')->whereNotNull('longitude')->withCount(['peserta', 'deteksiDini'])->get();
        $mapAnalytics = \App\Services\MapVisualizationService::getMapData(
            request('trend_bulan', null),
            request('trend_tahun', null)
        );

        return view('admin.dashboard', compact(
            'totalPengguna',
            'totalPetugas',
            'totalPeserta',
            'totalDeteksi',
            'labels',
            'data',
            'labelsGender',
            'dataGender',
            'labelsWilayah',
            'dataWilayah',
            'mapPuskesmasData',
            'mapAnalytics'
        ));
    }
}