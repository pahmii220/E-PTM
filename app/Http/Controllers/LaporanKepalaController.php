<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KepalaP2ptm;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\Kegiatan;
use App\Models\EvaluasiSus; // 👇 TAMBAHAN BARU UNTUK EVALUASI SISTEM

class LaporanKepalaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. LAPORAN PESERTA (MASTER DATA)
    |--------------------------------------------------------------------------
    */
    public function peserta(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DB::table('peserta')
                    ->join('puskesmas', 'peserta.puskesmas_id', '=', 'puskesmas.id')
                    ->select('peserta.*', 'puskesmas.nama_puskesmas')
                    ->where('peserta.status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('peserta.dibuat_pada', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);
        } else {
            $query->whereMonth('peserta.dibuat_pada', $bulan)
                  ->whereYear('peserta.dibuat_pada', $tahun);
        }

        $data = $query->orderBy('peserta.dibuat_pada', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.peserta', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakPeserta(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Peserta::with('puskesmas')
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('dibuat_pada', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);
        } else {
            $query->whereMonth('dibuat_pada', $bulan)
                  ->whereYear('dibuat_pada', $tahun);
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($request->input('filter_type') === 'tanggal') {
            $qrToken = "LAPORAN-PTM-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "LAPORAN-PTM-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

        return view('pengguna.verifikasi.cetak_peserta', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. LAPORAN DETEKSI DINI PTM (TEKANAN DARAH, GULA DARAH, IMT)
    |--------------------------------------------------------------------------
    */
    public function deteksiDini(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.deteksi_dini', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakDeteksiDini(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
            $isRange = true;
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
            $isRange = false;
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($isRange) {
            $qrToken = "DETEKSI-DINI-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "DETEKSI-DINI-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

       return view('pengguna.verifikasi.print.deteksi', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. LAPORAN FAKTOR RISIKO PTM
    |--------------------------------------------------------------------------
    */
    public function faktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = FaktorResikoPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.faktor_risiko', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakFaktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = FaktorResikoPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
            $isRange = true;
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
            $isRange = false;
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($isRange) {
            $qrToken = "FAKTOR-RESIKO-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "FAKTOR-RESIKO-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

        return view('pengguna.verifikasi.print.faktor', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. LAPORAN TINDAK LANJUT PTM
    |--------------------------------------------------------------------------
    */
    public function tindakLanjut(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = \App\Models\TindakLanjutPTM::with(['peserta', 'puskesmas']);

        $query->whereMonth('tanggal_tindak_lanjut', $bulan)
              ->whereYear('tanggal_tindak_lanjut', $tahun);

        $data = $query->orderBy('tanggal_tindak_lanjut', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.tindak_lanjut', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakTindakLanjut(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $items = \App\Models\TindakLanjutPTM::with(['peserta', 'puskesmas'])
                    ->whereMonth('tanggal_tindak_lanjut', $bulan)
                    ->whereYear('tanggal_tindak_lanjut', $tahun)
                    ->get();
        
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "TINDAK-LANJUT-" . $bulan . "-" . $tahun . "-" . uniqid();

        return view('pengguna.verifikasi.print.tindak_lanjut', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

   /*
    |--------------------------------------------------------------------------
    | 5. PUSAT LAPORAN EKSEKUTIF (GABUNGAN 5 LAPORAN BARU)
    |--------------------------------------------------------------------------
    */
    public function eksekutif(Request $request)
    {
        // --- DATA TAB 1: REKAP PUSKESMAS ---
        $dataPuskesmas = \App\Models\Puskesmas::withCount([
            'peserta as total_peserta',
            'deteksiDini as total_skrining', 
            'faktorResiko as total_risiko',
            'tindakLanjut as total_tindak_lanjut'
        ])->get();

        // --- DATA TAB 2: KELOMPOK USIA ---
        $dataUsia = [
            'remaja'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        // --- DATA TAB 3: SKRINING ---
        $dataSkrining = \App\Models\DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

        // --- DATA TAB 4: KEGIATAN ---
        $kegiatan = Kegiatan::with('puskesmas')->orderBy('tanggal', 'desc')->get();

        // 👇 TAMBAHAN BARU: DATA TAB 5 (EVALUASI SISTEM) 👇
        $semuaData = EvaluasiSus::with('user.pegawaiDinkes')->orderBy('created_at', 'desc')->get();
        
        $totalResponden = $semuaData->count();
        $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

        $predikat = 'Kurang Baik';
        if ($rataRataSkor >= 80.8) {
            $predikat = 'Excellent (Sangat Mudah)';
        } elseif ($rataRataSkor >= 71.4) {
            $predikat = 'Good (Mudah Digunakan)';
        } elseif ($rataRataSkor >= 50.9) {
            $predikat = 'Acceptable (Cukup Layak)';
        }

        // Memasukkan variabel tambahan ke dalam compact()
        return view('kepala_p2ptm.laporan.eksekutif', compact(
            'dataPuskesmas', 'dataUsia', 'dataSkrining', 'kegiatan',
            'semuaData', 'totalResponden', 'rataRataSkor', 'predikat'
        ));
    }

    public function cetakPuskesmas(Request $request)
    {
        $rekapPuskesmas = \App\Models\Puskesmas::withCount([
            'peserta as total_peserta',
            'deteksiDini as total_deteksi',
            'faktorResiko as total_faktor'
        ])->get();

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-FASKES-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.rekap_puskesmas.print', compact('rekapPuskesmas', 'qrToken', 'kepalaAktif'));
    }

    public function cetakUsia(Request $request)
    {
        $dataUsia = [
            'remaja'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => \App\Models\Peserta::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-USIA-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_kelompok_usia', compact('dataUsia', 'qrToken', 'kepalaAktif'));
    }

    public function cetakSkrining(Request $request)
    {
        $data = \App\Models\DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-SKRINING-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.status_ptm', compact('data', 'qrToken', 'kepalaAktif'));
    }


    /*
    |--------------------------------------------------------------------------
    | 6. CETAK LAPORAN KEGIATAN PTM
    |--------------------------------------------------------------------------
    */
    public function cetakKegiatan(Request $request)
    {
        $items = Kegiatan::orderBy('tanggal', 'desc')->get();
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "LAPORAN-KEGIATAN-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_kegiatan', compact('items', 'kepalaAktif', 'qrToken'));
    }
}