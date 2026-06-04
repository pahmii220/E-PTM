<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KepalaP2ptm;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\Kegiatan;

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

        $query = DB::table('pasien')
                    ->join('puskesmas', 'pasien.puskesmas_id', '=', 'puskesmas.id')
                    ->select('pasien.*', 'puskesmas.nama_puskesmas')
                    ->where('pasien.status_verifikasi', 'approved');

        $query->whereMonth('pasien.dibuat_pada', $bulan)
              ->whereYear('pasien.dibuat_pada', $tahun);

        $data = $query->orderBy('pasien.dibuat_pada', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.peserta', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakPeserta(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $items = Pasien::with('puskesmas')
                    ->where('status_verifikasi', 'approved')
                    ->whereMonth('dibuat_pada', $bulan)
                    ->whereYear('dibuat_pada', $tahun)
                    ->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "LAPORAN-PTM-" . $bulan . "-" . $tahun . "-" . uniqid();

        return view('pengguna.verifikasi.cetak_pasien', [
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

        $query = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        $query->whereMonth('tanggal_pemeriksaan', $bulan)
              ->whereYear('tanggal_pemeriksaan', $tahun);

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.deteksi_dini', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakDeteksiDini(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $items = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
                    ->where('status_verifikasi', 'approved')
                    ->whereMonth('tanggal_pemeriksaan', $bulan)
                    ->whereYear('tanggal_pemeriksaan', $tahun)
                    ->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "DETEKSI-DINI-" . $bulan . "-" . $tahun . "-" . uniqid();

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
    | 2. LAPORAN FAKTOR RISIKO PTM
    |--------------------------------------------------------------------------
    */
    public function faktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Menggunakan model FaktorResikoPTM
        $query = FaktorResikoPTM::with(['pasien', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        $query->whereMonth('tanggal_pemeriksaan', $bulan)
              ->whereYear('tanggal_pemeriksaan', $tahun);

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.faktor_risiko', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakFaktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $items = FaktorResikoPTM::with(['pasien', 'puskesmas'])
                    ->where('status_verifikasi', 'approved')
                    ->whereMonth('tanggal_pemeriksaan', $bulan)
                    ->whereYear('tanggal_pemeriksaan', $tahun)
                    ->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "FAKTOR-RESIKO-" . $bulan . "-" . $tahun . "-" . uniqid();

        // Arahkan ke view cetak yang akan kita buat
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

        // Langsung tarik semua data tanpa filter whereHas
        $query = \App\Models\TindakLanjutPTM::with(['pasien', 'puskesmas']);

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

        // Langsung tarik semua data untuk dicetak
        $items = \App\Models\TindakLanjutPTM::with(['pasien', 'puskesmas'])
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
    | 5. PUSAT LAPORAN EKSEKUTIF (GABUNGAN 4 LAPORAN BARU)
    |--------------------------------------------------------------------------
    */
    public function eksekutif(Request $request)
    {
        // --- DATA TAB 1: REKAP PUSKESMAS ---
        $dataPuskesmas = \App\Models\Puskesmas::withCount([
            'pasien as total_peserta',
            'deteksiDini as total_skrining', 
            'faktorResiko as total_risiko',
            'tindakLanjut as total_tindak_lanjut'
        ])->get();

        // --- DATA TAB 2: KELOMPOK USIA ---
        $dataUsia = [
            'remaja'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        $dataSkrining = \App\Models\DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

        $kegiatan = Kegiatan::with('puskesmas')->orderBy('tanggal', 'desc')->get();

        // 👇 UTAMAKAN BAGIAN INI: Tambahkan 'kegiatan' di dalam compact
        return view('kepala_p2ptm.laporan.eksekutif', compact('dataPuskesmas', 'dataUsia', 'dataSkrining', 'kegiatan'));
    }

    public function cetakPuskesmas(Request $request)
    {
        // 1. Ambil data rekapitulasi
        $rekapPuskesmas = \App\Models\Puskesmas::withCount([
            'pasien as total_pasien',
            'deteksiDini as total_deteksi',
            'faktorResiko as total_faktor'
        ])->get();

        // 2. Ambil data Kepala P2PTM
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        // 3. Generate Token QR Code
        $qrToken = "REKAP-FASKES-" . date('m-Y') . "-" . strtoupper(uniqid());

        // 4. Return View Cetak Puskesmas
        return view('pengguna.rekap_puskesmas.print', compact('rekapPuskesmas', 'qrToken', 'kepalaAktif'));
    }

    // 👇 TAMBAHKAN METHOD INI KHUSUS UNTUK TOMBOL CETAK USIA
    public function cetakUsia(Request $request)
    {
        // 1. Hitung ulang data usia
        $dataUsia = [
            'remaja'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => \App\Models\Pasien::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        // 2. Ambil data Kepala P2PTM 
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        
        // 3. Buat QR Token (Ini yang menyebabkan error undefined variable)
        $qrToken = "REKAP-USIA-" . date('m-Y') . "-" . strtoupper(uniqid());

        // 4. MENGARAH KE: resources/views/pengguna/laporan/print_kelompok_usia.blade.php
        return view('pengguna.laporan.print_kelompok_usia', compact('dataUsia', 'qrToken', 'kepalaAktif'));
    }

public function cetakSkrining(Request $request)
{
    // Ubah nama variabel dari $dataSkrining menjadi $data
    $data = \App\Models\DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
        ->groupBy('hasil_skrining')
        ->get();

    $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
    $qrToken = "REKAP-SKRINING-" . date('m-Y') . "-" . strtoupper(uniqid());

    // Sekarang compact('data') akan cocok dengan @foreach($data...) di Blade
    return view('pengguna.laporan.status_ptm', compact('data', 'qrToken', 'kepalaAktif'));
}


/*
    |--------------------------------------------------------------------------
    | 6. CETAK LAPORAN KEGIATAN PTM
    |--------------------------------------------------------------------------
    */
    public function cetakKegiatan(Request $request)
    {
        // 1. Tarik semua data kegiatan, urutkan dari yang terbaru
        $items = Kegiatan::orderBy('tanggal', 'desc')->get();

        // 2. Ambil data Kepala P2PTM untuk tanda tangan
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        $qrToken = "LAPORAN-KEGIATAN-" . date('m-Y') . "-" . strtoupper(uniqid());

        // 3. Arahkan ke file blade cetakan (sesuaikan dengan nama dan lokasi file cetakmu)
        return view('pengguna.laporan.print_kegiatan', compact('items', 'kepalaAktif', 'qrToken'));
    }
}