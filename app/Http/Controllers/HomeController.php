<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Peserta;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Depan Dinas Kesehatan (Landing Page)
     */
    public function index()
    {
        $totalPuskesmas = \App\Models\Puskesmas::count();
        $totalSkrining  = \App\Models\DeteksiDiniPTM::count();
        $totalPeserta   = \App\Models\Peserta::count();

        return view('frontend.landing', compact('totalPuskesmas', 'totalSkrining', 'totalPeserta'));
    }

    /**
     * Menampilkan Halaman Profil / Tentang Kami
     */
    public function profil()
    {
        return view('frontend.profil');
    }

    public function struktur()
    {
        return view('frontend.struktur');
    }

    /**
     * Cek Hasil Skrining & Riwayat PTM Pasien (Public / Tanpa Login)
     */
    public function cekRiwayatPTM(Request $request)
    {
        // 1. Jika pengguna memilih via 3 select terpisah (tgl_lahir, bln_lahir, thn_lahir)
        if ($request->filled('tgl_lahir') && $request->filled('bln_lahir') && $request->filled('thn_lahir')) {
            $tanggalLahir = sprintf('%04d-%02d-%02d', $request->thn_lahir, $request->bln_lahir, $request->tgl_lahir);
            $request->merge(['tanggal_lahir' => $tanggalLahir]);
        } else {
            // 2. Parsing format tanggal lahir (DD/MM/YYYY, DD-MM-YYYY, atau YYYY-MM-DD)
            $inputTanggal = trim($request->tanggal_lahir ?? '');
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $inputTanggal, $matches)) {
                $day = (int)$matches[1];
                $month = (int)$matches[2];
                $year = (int)$matches[3];
                if (checkdate($month, $day, $year)) {
                    $request->merge(['tanggal_lahir' => sprintf('%04d-%02d-%02d', $year, $month, $day)]);
                }
            } elseif (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $inputTanggal, $matches)) {
                $year = (int)$matches[1];
                $month = (int)$matches[2];
                $day = (int)$matches[3];
                if (checkdate($month, $day, $year)) {
                    $request->merge(['tanggal_lahir' => sprintf('%04d-%02d-%02d', $year, $month, $day)]);
                }
            }
        }

        $request->validate([
            'nik' => 'required|numeric|digits:16',
            'tanggal_lahir' => 'required|date',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.digits' => 'NIK harus berjumlah persis 16 digit sesuai KTP.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $peserta = Peserta::where('nik', trim($request->nik))
            ->whereDate('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$peserta) {
            return redirect()->to(route('frontend.home') . '#cek-ptm')
                ->withInput()
                ->with('status_pencarian', 'not_found');
        }

        // Simpan sesi pasien
        session(['pasien_id' => $peserta->id]);
        return redirect()->route('frontend.pasien.dashboard');
    }

    /**
     * Cetak Laporan Deteksi Dini PTM Pasien (Public / PDF)
     */
    public function cetakSkriningPublic($id, Request $request)
    {
        $peserta = Peserta::with(['puskesmas', 'deteksiDinis.tindakLanjut', 'deteksiDinis.petugas'])->findOrFail($id);

        $deteksiId = $request->query('deteksi_id');
        $deteksi = null;
        if ($deteksiId) {
            $deteksi = $peserta->deteksiDinis->where('id', $deteksiId)->first();
        }
        if (!$deteksi) {
            $deteksi = $peserta->deteksiDinis->first();
        }

        $tindakLanjut = $deteksi?->tindakLanjut;

        if (!$tindakLanjut) {
            $tindakLanjut = new \App\Models\TindakLanjutPTM([
                'peserta_id' => $peserta->id,
                'deteksi_dini_id' => $deteksi?->id,
                'diagnosa' => $deteksi?->status_risiko ?? ($deteksi?->hasil_skrining ?? 'Dalam Pemantauan'),
                'saran' => 'Pertahankan pola hidup sehat CERDIK dan lakukan pemeriksaan rutin setiap bulan.',
                'created_at' => $deteksi?->created_at ?? now(),
            ]);
            $tindakLanjut->setRelation('peserta', $peserta);
            $tindakLanjut->setRelation('deteksiDini', $deteksi);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('petugas.tindak_lanjut.cetak', compact('tindakLanjut'));
        $pdf->setPaper('a4', 'portrait');

        $nama = \Illuminate\Support\Str::slug($peserta->nama_lengkap);
        $tgl = $deteksi ? \Carbon\Carbon::parse($deteksi->tanggal_pemeriksaan)->format('d-m-Y') : date('d-m-Y');
        return $pdf->stream('Laporan-Hasil-Skrining-PTM-' . $nama . '-' . $tgl . '.pdf');
    }

    /**
     * Cetak Lembar Kartu Riwayat Semua Pemeriksaan Pasien (Public)
     */
    public function cetakRiwayatPublic($id)
    {
        $peserta = Peserta::with('puskesmas')->findOrFail($id);

        // Ambil semua riwayat deteksi dini & tindak lanjut terikat secara kronologis
        $riwayatKunjungan = \App\Models\DeteksiDiniPTM::with(['tindakLanjut', 'petugas'])
            ->where('peserta_id', $id)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();

        // Cari faktor risiko untuk tiap kunjungan
        foreach ($riwayatKunjungan as $rk) {
            $rk->faktor_risiko = \App\Models\FaktorResikoPTM::where('peserta_id', $id)
                ->where('tanggal_pemeriksaan', $rk->tanggal_pemeriksaan)
                ->first();
        }

        // Cari Petugas Pemeriksa PTM
        $petugasPemeriksa = null;
        $lastKunjungan = $riwayatKunjungan->last();
        if ($lastKunjungan && $lastKunjungan->petugas) {
            $petugasPemeriksa = $lastKunjungan->petugas;
        } else {
            $petugasPemeriksa = \App\Models\Petugas::where('puskesmas_id', $peserta->puskesmas_id)->first();
        }

        return view('petugas.peserta.print_riwayat', compact('peserta', 'riwayatKunjungan', 'petugasPemeriksa'));
    }

    /**
     * Dashboard Pasien
     */
    public function dashboardPasien()
    {
        $pasien_id = session('pasien_id');
        if (!$pasien_id) {
            return redirect()->to(route('frontend.home') . '#cek-ptm')->with('status_pencarian', 'session_expired');
        }

        $peserta = Peserta::with([
            'puskesmas',
            'deteksiDinis' => function($q) {
                $q->orderBy('tanggal_pemeriksaan', 'desc');
            },
            'deteksiDinis.petugas',
            'deteksiDinis.tindakLanjut',
            'faktorResikoPTM'
        ])->find($pasien_id);

        if (!$peserta) {
            session()->forget('pasien_id');
            return redirect()->to(route('frontend.home') . '#cek-ptm')->with('status_pencarian', 'not_found');
        }

        return view('frontend.dashboard_pasien', compact('peserta'));
    }

    /**
     * Logout Pasien
     */
    public function logoutPasien()
    {
        session()->forget('pasien_id');
        return redirect()->to(route('frontend.home') . '#cek-ptm');
    }
}