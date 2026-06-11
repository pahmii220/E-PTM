<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EvaluasiSus;
use Illuminate\Support\Facades\Auth;

class EvaluasiController extends Controller
{
    // 1. Tampilan Form Kuesioner untuk Role Pegawai
    public function tampilkanForm()
    {
        // Memastikan pegawai hanya bisa mengisi survei ini 1 kali saja
        $sudahIsi = EvaluasiSus::where('user_id', Auth::id())->exists();
        return view('evaluasi.form', compact('sudahIsi'));
    }

    // 2. Simpan Jawaban & Hitung Skor SUS Otomatis
    public function simpanJawaban(Request $request)
    {
        $request->validate([
            'q1'=>'required','q2'=>'required','q3'=>'required','q4'=>'required','q5'=>'required',
            'q6'=>'required','q7'=>'required','q8'=>'required','q9'=>'required','q10'=>'required',
        ]);

        // Implementasi RUMUS BAKU SUS:
        // Pertanyaan Ganjil (1,3,5,7,9): Nilai input dikurangi 1
        $skorGanjil = ($request->q1 - 1) + ($request->q3 - 1) + ($request->q5 - 1) + ($request->q7 - 1) + ($request->q9 - 1);
        
        // Pertanyaan Genap (2,4,6,8,10): 5 dikurangi nilai input
        $skorGenap = (5 - $request->q2) + (5 - $request->q4) + (5 - $request->q6) + (5 - $request->q8) + (5 - $request->q10);
        
        // Total skor digabungkan lalu dikalikan 2.5
        $totalSkorSUS = ($skorGanjil + $skorGenap) * 2.5;

        EvaluasiSus::create([
            'user_id' => Auth::id(),
            'q1' => $request->q1, 'q2' => $request->q2, 'q3' => $request->q3, 'q4' => $request->q4, 'q5' => $request->q5,
            'q6' => $request->q6, 'q7' => $request->q7, 'q8' => $request->q8, 'q9' => $request->q9, 'q10' => $request->q10,
            'skor_sus' => $totalSkorSUS,
            'saran' => $request->saran
        ]);

        return redirect()->route('pengguna.evaluasi.form')->with('success', 'Terima kasih banyak! Penilaian Anda berhasil disimpan.');
    }

    // 3. Tampilan Laporan Ke-10 (Summary Evaluasi untuk Admin / Kepala)
    public function laporanEvaluasi()
    {
        $semuaData = EvaluasiSus::with('user')->orderBy('created_at', 'desc')->get();
        $totalResponden = $semuaData->count();
        
        // Hitung rata-rata skor SUS dari seluruh responden
        $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

        // Klasifikasi Kelayakan Berdasarkan Standar Nilai SUS
        $predikat = 'Kurang Baik';
        if ($rataRataSkor >= 80.8) {
            $predikat = 'Excellent (Sangat Mudah)';
        } elseif ($rataRataSkor >= 71.4) {
            $predikat = 'Good (Mudah Digunakan)';
        } elseif ($rataRataSkor >= 50.9) {
            $predikat = 'Acceptable (Cukup Layak)';
        }

        return view('evaluasi.report', compact('semuaData', 'totalResponden', 'rataRataSkor', 'predikat'));
    }

    public function cetakLaporan()
{
    $semuaData = EvaluasiSus::with('user')->orderBy('created_at', 'desc')->get();
    $totalResponden = $semuaData->count();
    
    // Hitung rata-rata
    $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

    // Klasifikasi Kelayakan SUS
    $predikat = 'Kurang Baik';
    $keterangan = 'Sistem berada di bawah rata-rata kelayakan dan memerlukan evaluasi alur kerja.';
    
    if ($rataRataSkor >= 80.8) {
        $predikat = 'Excellent (Sangat Mudah)';
        $keterangan = 'Sistem sangat mudah digunakan dan diterima dengan sangat baik oleh pengguna.';
    } elseif ($rataRataSkor >= 71.4) {
        $predikat = 'Good (Mudah Digunakan)';
        $keterangan = 'Sistem memuaskan dan berfungsi dengan tingkat kemudahan yang baik.';
    } elseif ($rataRataSkor >= 50.9) {
        $predikat = 'Acceptable (Cukup Layak)';
        $keterangan = 'Sistem cukup layak digunakan namun memerlukan beberapa perbaikan minor.';
    }

   $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first(); 
    $qrToken = 'valid'; // Sesuaikan dengan logika QR Anda

    return view('evaluasi.cetak', compact('semuaData', 'totalResponden', 'rataRataSkor', 'predikat', 'keterangan', 'kepalaAktif', 'qrToken'));
}
}