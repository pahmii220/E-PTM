<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===============================
        // STATISTIK DASHBOARD
        // ===============================
        $totalPengguna = User::count();
        $totalPetugas  = User::where('role_name', 'petugas')->count();
        $totalPasien   = DB::table('pasien')->count();
        $totalDeteksi  = DB::table('deteksi_dini_ptm')->count();

        // ===============================
        // TREN KASUS PTM (DARI PASIEN)
        // ===============================
        $trenPTM = DB::table('pasien')
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->get();

        // ===============================
        // FORMAT BULAN
        // ===============================
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = [];
        $data   = [];

        foreach ($namaBulan as $key => $bulan) {
            $labels[] = $bulan;
            $found = $trenPTM->firstWhere('bulan', $key);
            $data[] = $found ? $found->total : 0;
        }

        return view('admin.dashboard', compact(
            'totalPengguna',
            'totalPetugas',
            'totalPasien',
            'totalDeteksi',
            'labels',
            'data'
        ));
    }
}
