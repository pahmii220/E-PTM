<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;

class MonitoringLaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);
        $startDate = "{$tahun}-{$bln}-01";
        $endDate   = Carbon::create($tahun, $bln)->endOfMonth()->format('Y-m-d');

        // Ambil semua puskesmas, kelompokkan per kota dan kecamatan
        $allPuskesmas = Puskesmas::orderBy('nama_kabupaten')->orderBy('kecamatan')->orderBy('nama_puskesmas')->get();

        // Ambil ID puskesmas yang sudah mengirimkan laporan (ada data pending/approved) di bulan ini
        $sudahLapor = DeteksiDiniPTM::whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->whereIn('status_verifikasi', ['pending', 'approved', 'terverifikasi'])
            ->distinct()
            ->pluck('puskesmas_id')
            ->toArray();

        // Puskesmas yang punya data tapi masih draft (belum diajukan)
        $adaDraftTapiBelumKirim = DeteksiDiniPTM::whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status_verifikasi', 'draft')
            ->whereNotIn('puskesmas_id', $sudahLapor)
            ->distinct()
            ->pluck('puskesmas_id')
            ->toArray();

        // Tambahkan status ke tiap puskesmas
        $allPuskesmas = $allPuskesmas->map(function ($pkm) use ($sudahLapor, $adaDraftTapiBelumKirim, $startDate, $endDate) {
            if (in_array($pkm->id, $sudahLapor)) {
                // Cek apakah sudah approved atau masih pending
                $approved = DeteksiDiniPTM::where('puskesmas_id', $pkm->id)
                    ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
                    ->whereIn('status_verifikasi', ['approved', 'terverifikasi'])
                    ->exists();
                $pkm->status_laporan = $approved ? 'approved' : 'pending';
            } elseif (in_array($pkm->id, $adaDraftTapiBelumKirim)) {
                $pkm->status_laporan = 'draft';
            } else {
                $pkm->status_laporan = 'belum';
            }

            // Hitung jumlah data
            $pkm->jumlah_data = DeteksiDiniPTM::where('puskesmas_id', $pkm->id)
                ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
                ->count();

            return $pkm;
        });

        // Kelompokkan per kota
        $perKota = $allPuskesmas->groupBy('nama_kabupaten');

        // Statistik global
        $totalPkm       = $allPuskesmas->count();
        $totalSudahKirim = $allPuskesmas->whereIn('status_laporan', ['pending','approved','terverifikasi'])->count();
        $totalApproved   = $allPuskesmas->whereIn('status_laporan', ['approved','terverifikasi'])->count();
        $totalDraft      = $allPuskesmas->where('status_laporan', 'draft')->count();
        $totalBelum      = $allPuskesmas->where('status_laporan', 'belum')->count();

        return view('pengguna.monitoring.index', compact(
            'perKota', 'bulan', 'startDate', 'endDate',
            'totalPkm', 'totalSudahKirim', 'totalApproved', 'totalDraft', 'totalBelum'
        ));
    }
}
