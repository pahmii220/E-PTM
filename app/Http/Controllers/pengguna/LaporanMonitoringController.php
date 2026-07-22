<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanHasilMonitoring;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;

class LaporanMonitoringController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role_name === 'admin') {
            $laporan = LaporanHasilMonitoring::with(['puskesmas', 'pegawai'])->latest()->get();
        } else {
            $pegawai = $user->pegawaiDinkes;
            $laporan = LaporanHasilMonitoring::with('puskesmas')
                ->where('pegawai_id', optional($pegawai)->id)
                ->latest()
                ->get();
        }
            
        // Ambil puskesmas beserta relasi untuk menghitung penyakit dominan
        $puskesmasData = Puskesmas::with(['peserta.deteksiDinis'])->orderBy('kecamatan', 'asc')->orderBy('nama_puskesmas', 'asc')->get();

        $penyakitList = [
            'Hipertensi', 'Diabetes Melitus', 'Obesitas',
            'Penyakit Jantung', 'Stroke', 'Asma', 'Kanker', 'PPOK'
        ];

        foreach ($puskesmasData as $pkm) {
            $pkm->peserta_count = $pkm->peserta->count();
            
            $ptmCounts = array_fill_keys($penyakitList, 0);
            foreach ($pkm->peserta as $pst) {
                $patientDiseases = [];
                foreach ($pst->deteksiDinis as $d) {
                    $diagnosa = $d->diagnosa_penyakit ?? ($d->hasil_skrining ?? '');
                    if (empty($diagnosa)) continue;
                    foreach ($penyakitList as $p) {
                        if (stripos($diagnosa, $p) !== false) {
                            $patientDiseases[$p] = true;
                        }
                    }
                }
                foreach ($patientDiseases as $p => $val) {
                    $ptmCounts[$p]++;
                }
            }

            arsort($ptmCounts);
            $dominantPtm = key($ptmCounts);
            $dominantPtmCount = current($ptmCounts);

            $pkm->dominan_penyakit = $dominantPtmCount > 0 ? "$dominantPtm ($dominantPtmCount kasus)" : "Belum ada kasus tercatat";
        }

        $puskesmas = $puskesmasData;

        // --- LOGIKA REKOMENDASI CERDAS ---
        $bulanIni = \Carbon\Carbon::now()->month;
        $tahunIni = \Carbon\Carbon::now()->year;

        // Ambil list puskesmas_id yang sudah pernah/sedang dibuatkan Laporan Hasil Monitoring
        $puskesmasSudahLapor = LaporanHasilMonitoring::pluck('puskesmas_id')->unique()->toArray();

        $rekomendasiPuskesmas = \App\Models\DeteksiDiniPTM::with('puskesmas')
            ->whereMonth('tanggal_pemeriksaan', $bulanIni)
            ->whereYear('tanggal_pemeriksaan', $tahunIni)
            ->where('hasil_skrining', 'Risiko Tinggi')
            ->whereNotIn('puskesmas_id', $puskesmasSudahLapor)
            ->selectRaw('puskesmas_id, count(id) as total_kasus')
            ->groupBy('puskesmas_id')
            ->orderByDesc('total_kasus')
            ->take(2)
            ->get();

        return view('pengguna.laporan_monitoring.index', compact('laporan', 'puskesmas', 'rekomendasiPuskesmas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'tanggal_kunjungan' => 'nullable|date',
            'nomor_spt' => 'nullable|string|max:255',
            'kategori_temuan' => 'nullable|string|max:255',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_temuan' => 'required|string',
            'rekomendasi_tindakan' => 'required|string',
        ]);

        $pegawai = Auth::user()->pegawaiDinkes;
        $pegawaiId = $pegawai ? $pegawai->id : (\App\Models\PegawaiDinkes::first()->id ?? 1);

        LaporanHasilMonitoring::create([
            'pegawai_id' => $pegawaiId,
            'puskesmas_id' => $request->puskesmas_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan ?? date('Y-m-d'),
            'nomor_spt' => $request->nomor_spt,
            'kategori_temuan' => $request->kategori_temuan,
            'judul_laporan' => $request->judul_laporan,
            'deskripsi_temuan' => $request->deskripsi_temuan,
            'rekomendasi_tindakan' => $request->rekomendasi_tindakan,
            'status_laporan' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Laporan Hasil Monitoring berhasil dikirim ke Kepala P2PTM!');
    }

    public function cetak($id)
    {
        $laporan = LaporanHasilMonitoring::with(['puskesmas', 'pegawai'])->findOrFail($id);

        if ($laporan->status_laporan !== 'disetujui') {
            return redirect()->back()->with('error', 'Hanya laporan yang telah disetujui yang dapat dicetak.');
        }

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        return view('pengguna.laporan_monitoring.print', compact('laporan', 'kepalaAktif'));
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanHasilMonitoring::findOrFail($id);

        if (!in_array($laporan->status_laporan, ['pending', 'ditolak'])) {
            return redirect()->back()->with('error', 'Hanya laporan dengan status pending atau ditolak yang dapat diubah.');
        }

        $request->validate([
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'tanggal_kunjungan' => 'nullable|date',
            'nomor_spt' => 'nullable|string|max:255',
            'kategori_temuan' => 'nullable|string|max:255',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_temuan' => 'required|string',
            'rekomendasi_tindakan' => 'required|string',
        ]);

        $laporan->update([
            'puskesmas_id' => $request->puskesmas_id,
            'tanggal_kunjungan' => $request->tanggal_kunjungan ?? $laporan->tanggal_kunjungan ?? date('Y-m-d'),
            'nomor_spt' => $request->nomor_spt,
            'kategori_temuan' => $request->kategori_temuan,
            'judul_laporan' => $request->judul_laporan,
            'deskripsi_temuan' => $request->deskripsi_temuan,
            'rekomendasi_tindakan' => $request->rekomendasi_tindakan,
            'status_laporan' => 'pending', // Reset ke pending
            'catatan_kepala' => null // Hapus catatan penolakan jika ada
        ]);

        return redirect()->route('pengguna.laporan_monitoring.index')
            ->with('success', 'Laporan Monitoring berhasil diperbarui dan diajukan ulang.');
    }

    public function destroy($id)
    {
        $laporan = LaporanHasilMonitoring::findOrFail($id);
        
        $user = Auth::user();
        if ($user->role_name !== 'admin' && !in_array($laporan->status_laporan, ['pending', 'ditolak'])) {
            return redirect()->back()->with('error', 'Hanya laporan dengan status pending atau ditolak yang dapat dihapus.');
        }

        $laporan->delete();

        return redirect()->back()->with('success', 'Laporan Monitoring berhasil dihapus.');
    }
}
