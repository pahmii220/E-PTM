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
    $data = [
        'totalPeserta' => \App\Models\Pasien::where('status_verifikasi', 'approved')->count(),
        'totalDeteksi' => \App\Models\DeteksiDiniPTM::count(),
        'totalRisiko'  => \App\Models\FaktorResikoPTM::where('status_verifikasi', 'approved')->count(),
        'totalPuskesmas' => \App\Models\Puskesmas::count(),
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