<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratTugasLuar;
use App\Models\Puskesmas;
use App\Models\KepalaP2ptm;
use App\Models\User;
use App\Notifications\PengajuanSuratTugasNotification;
use Illuminate\Support\Facades\Auth;

class SuratTugasPegawaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->pegawaiDinkes) {
            return redirect()->back()->with('error', 'Profil Pegawai Dinkes belum lengkap.');
        }

        $suratTugas = SuratTugasLuar::with(['puskesmas', 'pengikut', 'perlengkapan'])
            ->where('pegawai_id', $user->pegawaiDinkes->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $puskesmasList = Puskesmas::all();
        $pegawaiList = \App\Models\PegawaiDinkes::where('id', '!=', $user->pegawaiDinkes->id)->get();

        $laporanMonitoringList = \App\Models\LaporanHasilMonitoring::where('pegawai_id', $user->pegawaiDinkes->id)
            ->where('status_laporan', 'disetujui')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pengguna.surat_tugas.index', compact('suratTugas', 'puskesmasList', 'pegawaiList', 'laporanMonitoringList'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->pegawaiDinkes) {
            return redirect()->back()->with('error', 'Profil Pegawai Dinkes belum lengkap.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'maksud_tujuan' => 'required|string',
        ]);

        $surat = SuratTugasLuar::create([
            'pegawai_id' => $user->pegawaiDinkes->id,
            'puskesmas_id' => $request->puskesmas_id,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'maksud_tujuan' => $request->maksud_tujuan,
            'laporan_monitoring_id' => $request->laporan_monitoring_id,
            'status_persetujuan' => 'pending'
        ]);

        if ($request->has('pengikut_ids') && is_array($request->pengikut_ids)) {
            $surat->pengikut()->sync($request->pengikut_ids);
        }

        // Kirim notifikasi lonceng ke semua akun Kepala P2PTM
        $namaPegawai = $user->pegawaiDinkes->nama_pegawai ?? $user->Nama_Lengkap ?? 'Pegawai';
        $kepalaUsers = User::where('role_name', 'kepala_p2ptm')->get();
        foreach ($kepalaUsers as $kepala) {
            $kepala->notify(new PengajuanSuratTugasNotification($surat, $namaPegawai));
        }

        return redirect()->route('pengguna.surat_tugas.index')
            ->with('success', 'Pengajuan Surat Tugas Luar berhasil dibuat dan menunggu persetujuan.');
    }

    public function print($id)
    {
        $user = Auth::user();
        $surat = SuratTugasLuar::with(['pegawai', 'puskesmas'])->findOrFail($id);

        if ($user->role_name !== 'kepala_p2ptm' && $user->role_name !== 'admin') {
            if (!$user->pegawaiDinkes || $surat->pegawai_id !== $user->pegawaiDinkes->id) {
                abort(403, 'Anda tidak berhak mencetak surat tugas ini.');
            }
        }

        if ($surat->status_persetujuan !== 'disetujui') {
            return redirect()->back()->with('error', 'Surat Tugas belum disetujui.');
        }

        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        
        $qrToken = "SPT-" . str_replace('/', '', $surat->nomor_surat) . "-" . uniqid();

        return view('pengguna.surat_tugas.print', compact('surat', 'kepalaAktif', 'qrToken'));
    }

    public function update(Request $request, $id)
    {
        $surat = SuratTugasLuar::findOrFail($id);
        
        if (!in_array($surat->status_persetujuan, ['pending', 'ditolak'])) {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending atau ditolak yang dapat diubah.');
        }

        $surat->update([
            'status_persetujuan' => 'pending', // Kembalikan ke pending jika diperbaiki
            'catatan_kepala' => null, // Hapus catatan penolakan sebelumnya
            'puskesmas_id' => $request->puskesmas_id,
            'lokasi_tujuan' => $request->lokasi_tujuan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'maksud_tujuan' => $request->maksud_tujuan,
            'laporan_monitoring_id' => $request->laporan_monitoring_id,
        ]);

        if ($request->has('pengikut_ids') && is_array($request->pengikut_ids)) {
            $surat->pengikut()->sync($request->pengikut_ids);
        } else {
            $surat->pengikut()->detach();
        }

        return redirect()->route('pengguna.surat_tugas.index')
            ->with('success', 'Pengajuan Surat Tugas Luar berhasil diperbarui dan diajukan ulang.');
    }

    public function destroy($id)
    {
        $surat = SuratTugasLuar::findOrFail($id);
        
        if (!in_array($surat->status_persetujuan, ['pending', 'ditolak'])) {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status pending atau ditolak yang dapat dihapus.');
        }

        $surat->delete();

        return redirect()->route('pengguna.surat_tugas.index')
            ->with('success', 'Pengajuan Surat Tugas Luar berhasil dihapus.');
    }
}
