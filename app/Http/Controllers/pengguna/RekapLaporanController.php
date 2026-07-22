<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\Peserta;
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

    public function index(Request $request)
    {
        // 1. Inisialisasi Filter Bulan
        $filterBulan = $request->input('bulan', Carbon::now()->format('Y-m'));

        // 2. Data Rekap Puskesmas Tradisional (Overview Umum)
        $rekapPuskesmas = Puskesmas::withCount([
            'peserta as total_peserta',
            'deteksiDini as total_deteksi',
            'faktorResiko as total_faktor',
        ])
        ->orderBy('nama_puskesmas', 'asc')
        ->get();

        // 3. Algoritma Matriks Laporan Eksekutif (Cross-Tabulation)
        $penyakitList = [
            "Gangguan Jantung", "Gagal Jantung", "Jantung Koroner", "Jantung Kongenital", "Jantung Lainnya",
            "Hipertensi", "Diabetes Melitus", "Obesitas", "Gangguan Stroke", "Kanker Payudara",
            "Kanker Serviks", "Kanker Paru-Paru", "Kanker Kolorektal", "Thalassemia",
            "Gangguan Pendengaran", "Gangguan Pendengaran Otitis (OMSK)", "Gangguan Pendengaran Presbikusis",
            "Gangguan Penglihatan Katarak", "Miopia", "PPOK Umum", "PPOK Stabil", "PPOK Eksaserbasi"
        ];

        $puskesmasList = Puskesmas::with(['peserta.deteksiDinis'])->orderBy('nama_puskesmas', 'asc')->get();
        $matriksLaporan = collect();

        foreach($puskesmasList as $pkm) {
            $remaja = 0; $dewasa = 0; $pra_lansia = 0; $lansia = 0;
            $ptmCounts = array_fill_keys($penyakitList, 0);
            $hasAnyData = false;

            foreach($pkm->peserta as $pst) {
                // Filter data skrining di bulan yang dipilih menggunakan relasi hasMany 'deteksiDinis'
                $deteksiList = collect($pst->deteksiDinis)->filter(function($d) use ($filterBulan) {
                    return str_starts_with($d->tanggal_pemeriksaan ?? $d->dibuat_pada, $filterBulan);
                });

                if($deteksiList->count() > 0) {
                    $hasAnyData = true;
                    // Hitung kelompok usia (hanya 1 kali per pasien yang diskrining)
                    if ($pst->tanggal_lahir) {
                        $umur = Carbon::parse($pst->tanggal_lahir)->age;
                        if ($umur < 18) $remaja++;
                        elseif ($umur <= 44) $dewasa++;
                        elseif ($umur <= 59) $pra_lansia++;
                        else $lansia++;
                    }

                    // Hitung sebaran penyakit dari seluruh skrining pasien di bulan tsb
                    foreach($deteksiList as $d) {
                        $diagnosa = $d->diagnosa_penyakit ?? ($d->hasil_skrining ?? '');
                        
                        // Default Fallback
                        if(empty($diagnosa)) continue;

                        foreach($penyakitList as $p) {
                            if(stripos($diagnosa, $p) !== false) {
                                $ptmCounts[$p]++;
                            }
                        }
                    }
                }
            }

            // Tampilkan puskesmas walaupun 0? User minta tampilkan SEMUANYA.
            // Kita akan selalu push puskesmas ke array agar puskesmas yang 0 tetap terlihat.
            $matriksLaporan->push([
                'puskesmas' => $pkm->nama_puskesmas,
                'remaja' => $remaja,
                'dewasa' => $dewasa,
                'pra_lansia' => $pra_lansia,
                'lansia' => $lansia,
                'ptm' => $ptmCounts,
                'total_pasien' => $remaja + $dewasa + $pra_lansia + $lansia
            ]);
        }

        // 4. Data Kelompok Usia Global & Skrining (Untuk Tab Klasik)
        $skriningPtm = DeteksiDiniPTM::where('tanggal_pemeriksaan', 'like', $filterBulan.'%')
            ->selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

        $kelompokUsia = ['remaja' => 0, 'dewasa' => 0, 'pra_lansia' => 0, 'lansia' => 0];
        foreach ($matriksLaporan as $row) {
            $kelompokUsia['remaja'] += $row['remaja'];
            $kelompokUsia['dewasa'] += $row['dewasa'];
            $kelompokUsia['pra_lansia'] += $row['pra_lansia'];
            $kelompokUsia['lansia'] += $row['lansia'];
        }

        // 5. Laporan Kegiatan PTM
        $kegiatan = Kegiatan::where('tanggal', 'like', $filterBulan.'%')
                    ->orderBy('tanggal', 'desc')->get();

        return view('pengguna.rekap.index', compact(
            'rekapPuskesmas',
            'skriningPtm',
            'kelompokUsia',
            'kegiatan',
            'matriksLaporan',
            'filterBulan',
            'penyakitList'
        ));
    }
}
