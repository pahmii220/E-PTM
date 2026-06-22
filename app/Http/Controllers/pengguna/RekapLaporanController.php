<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapLaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,pegawai']);
    }

    public function index()
    {
        // 1. Data Rekap Puskesmas
        $rekapPuskesmas = Puskesmas::withCount([
            'pasien as total_pasien',
            'deteksiDini as total_deteksi',
            'faktorResiko as total_faktor',
        ])
        ->orderBy('nama_puskesmas', 'asc')
        ->get();

        // 2. Data Skrining PTM
        $skriningPtm = DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

        // 3. Data Kelompok Usia
        $pasien = Pasien::all();
        $kelompokUsia = [
            'remaja' => 0,
            'dewasa' => 0,
            'pra_lansia' => 0,
            'lansia' => 0
        ];

        foreach ($pasien as $p) {
            if (!$p->tanggal_lahir) continue;
            $umur = Carbon::parse($p->tanggal_lahir)->age;

            if ($umur < 18) {
                $kelompokUsia['remaja']++;
            } elseif ($umur <= 44) {
                $kelompokUsia['dewasa']++;
            } elseif ($umur <= 59) {
                $kelompokUsia['pra_lansia']++;
            } else {
                $kelompokUsia['lansia']++;
            }
        }

        // 4. Laporan Kegiatan PTM
        $kegiatan = Kegiatan::orderBy('tanggal', 'desc')->get();

        return view('pengguna.rekap.index', compact(
            'rekapPuskesmas',
            'skriningPtm',
            'kelompokUsia',
            'kegiatan'
        ));
    }
}
