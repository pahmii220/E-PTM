<?php

namespace App\Http\Controllers;
use App\Models\DokumenPengesahan;

class KepalaP2ptmController extends Controller
{
    /**
     * 1. Halaman Dashboard Utama Kepala P2PTM
     */
public function dashboard()
{
    $totalPuskesmas = \App\Models\Puskesmas::count();
    
    // UBAH 'created_at' MENJADI NAMA KOLOM TANGGAL YANG ADA DI DATABASE ANDA
    // Contoh: 'tanggal_pemeriksaan'
    $puskesmasAktif = \App\Models\DeteksiDiniPTM::whereMonth('tanggal_pemeriksaan', date('m'))
                                                ->distinct('puskesmas_id')
                                                ->count();
                                                
    $persentase = $totalPuskesmas > 0 ? ($puskesmasAktif / $totalPuskesmas) * 100 : 0;

    $data = [
        'totalPeserta'   => \App\Models\Pasien::count(),
        'totalDeteksi'   => \App\Models\DeteksiDiniPTM::count(),
        'totalRisiko'    => \App\Models\FaktorResikoPTM::count(),
        'totalPuskesmas' => $totalPuskesmas,
        'persentase'     => round($persentase, 1)
    ];
    
    return view('kepala_p2ptm.dashboard', compact('data'));
}
    /**
     * 8. Halaman Verifikasi Publik (Scan QR Code)
     */
    public function verifikasiPublik($token)
    {
        // Cari dokumen berdasarkan token URL yang digenerate di QR Code
        $urlPencarian = url('/verifikasi-dokumen/' . $token);
        $dokumen = DokumenPengesahan::with('kepalaP2ptm')->where('kode_validasi_qr', $urlPencarian)->firstOrFail();

        return view('verifikasi_publik', compact('dokumen'));
    }
}