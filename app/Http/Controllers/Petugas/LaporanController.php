<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman Laporan Register PTM untuk Petugas
     */
    public function index(Request $request)
    {
        $puskesmasId = Auth::user()->petugas->puskesmas_id;
        
        // Default filter bulan ini
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Mengambil data deteksi dini berdasarkan puskesmas petugas dan rentang tanggal
        $laporan = DeteksiDiniPTM::with(['peserta', 'tindakLanjut'])
            ->where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get()
            ->unique('peserta_id');

        // Ambil data faktor risiko dalam rentang waktu yang sama
        $faktorRisikoList = FaktorResikoPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->get();

        // Gabungkan secara manual di memory
        foreach ($laporan as $row) {
            $row->faktorRisiko = $faktorRisikoList
                ->where('peserta_id', $row->peserta_id)
                ->where('tanggal_pemeriksaan', $row->tanggal_pemeriksaan)
                ->first();
        }

        // Hitung statistik untuk summary/badge (optional)
        $totalDraft = $laporan->where('status_verifikasi', 'draft')->count();
        $totalPending = $laporan->where('status_verifikasi', 'pending')->count();
        $totalApproved = $laporan->whereIn('status_verifikasi', ['approved', 'terverifikasi'])->count();

        return view('petugas.laporan.index', compact(
            'laporan', 'startDate', 'endDate', 'totalDraft', 'totalPending', 'totalApproved'
        ));
    }

    /**
     * Mengajukan laporan (mengubah status 'draft' menjadi 'pending') secara massal
     */
    public function ajukan(Request $request)
    {
        $puskesmasId = Auth::user()->petugas->puskesmas_id;
        
        if ($request->has('deteksi_id')) {
            $deteksiDiniDraft = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->where('id', $request->deteksi_id)
                ->where('status_verifikasi', 'draft')
                ->get();
                
            $startDate = $deteksiDiniDraft->first()->tanggal_pemeriksaan ?? Carbon::now()->toDateString();
            $endDate = $deteksiDiniDraft->first()->tanggal_pemeriksaan ?? Carbon::now()->toDateString();
        } else {
            $request->validate([
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
            ]);
            
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // 1. Cari data yang berstatus 'draft' pada rentang tanggal tersebut
            $deteksiDiniDraft = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
                ->where('status_verifikasi', 'draft')
                ->get();
        }

        if ($deteksiDiniDraft->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data dengan status Draft yang bisa diajukan.');
        }

        $countApproved = 0;
        $countRejected = 0;
        $countPending  = 0;

        // 2. Lakukan Verifikasi Otomatis (Auto-Validation) per baris data
        foreach ($deteksiDiniDraft as $row) {
            $sbp = $dbp = null;
            if ($row->tekanan_darah && strpos($row->tekanan_darah, '/') !== false) {
                [$a, $b] = explode('/', $row->tekanan_darah);
                $sbp = is_numeric($a) ? (int)$a : null;
                $dbp = is_numeric($b) ? (int)$b : null;
            }

            $gula = $row->gula_darah;
            $kol  = $row->kolesterol;
            $imt  = $row->imt;
            $bb   = $row->berat_badan;
            $tb   = $row->tinggi_badan;
            // SEMUA DATA YANG DIKIRIM OLEH PUSKESMAS OTOMATIS DISAHKAN (AUTO-APPROVE)
            // Sesuai permintaan untuk menghilangkan tahap verifikasi manual Pegawai Dinkes
            $status = 'approved';
            $catatan = 'Dikirim dan disetujui secara otomatis oleh Sistem.';
            $countApproved++;

            // Update Deteksi Dini
            $row->update([
                'status_verifikasi'  => $status,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_pada'  => $status !== 'pending' ? Carbon::now() : null,
                'diverifikasi_oleh'  => null, // Sistem (Auto-approve)
            ]);

            // Update Faktor Risiko terkait agar statusnya sinkron
            FaktorResikoPTM::where('puskesmas_id', $puskesmasId)
                ->where('peserta_id', $row->peserta_id)
                ->where('tanggal_pemeriksaan', $row->tanggal_pemeriksaan)
                ->where('status_verifikasi', 'draft')
                ->update([
                    'status_verifikasi' => $status,
                    'diverifikasi_pada' => $status !== 'pending' ? Carbon::now() : null,
                    'diverifikasi_oleh' => null,
                ]);

            // Kirim notifikasi ke petugas jika otomatis ditolak oleh sistem
            if ($status === 'rejected') {
                $petugasUsers = User::where('role_name', 'petugas')
                    ->whereHas('petugas', function($q) use ($puskesmasId) {
                        $q->where('puskesmas_id', $puskesmasId);
                    })->get();
                if ($petugasUsers->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($petugasUsers, new \App\Notifications\DataPtmDitolakNotification($row));
                }
            }
        }

        // 4. Kirim Notifikasi Email & Database ke Dinkes
        try {
            $usersDinkes = User::where('role_name', 'pegawai')
                ->where('email', 'not like', '%@ptm.local')
                ->where('email', 'not like', '%@example.com')
                ->where('email', 'not like', '%@test.com')
                ->get();
            if ($usersDinkes->isNotEmpty() && $countApproved > 0) {
                $pkm = \App\Models\Puskesmas::find($puskesmasId);
                $pkmNama = $pkm ? $pkm->nama_puskesmas : 'Puskesmas';
                
                \Illuminate\Support\Facades\Notification::send(
                    $usersDinkes, 
                    new \App\Notifications\LaporanBulananMasukNotification($pkmNama, $countApproved, $startDate, $endDate, $puskesmasId)
                );
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim email notifikasi: ' . $e->getMessage());
        }

        $msg = "Pengiriman laporan berhasil! Laporan telah masuk ke database Dinkes. Total: {$countApproved} Data Pasien.";

        return redirect()->back()->with('success', $msg);
    }
}
