<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KepalaP2ptm;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;

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
}