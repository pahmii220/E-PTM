<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanHasilMonitoring;
use App\Models\PerlengkapanTugas;
use App\Models\PerlengkapanTugasItem;
use Illuminate\Support\Facades\Auth;

class PerlengkapanKegiatanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->pegawaiDinkes) {
            return redirect()->back()->with('error', 'Profil Pegawai Dinkes belum lengkap.');
        }

        // Ambil Laporan Hasil Monitoring yang statusnya disetujui (ACC Kepala) milik pegawai ini
        $laporanMonitoring = LaporanHasilMonitoring::with(['puskesmas', 'perlengkapan.items'])
            ->where('status_laporan', 'disetujui')
            ->where('pegawai_id', $user->pegawaiDinkes->id)
            ->orderBy('tanggal_disetujui', 'desc')
            ->paginate(10);

        return view('pengguna.perlengkapan_tugas.index', compact('laporanMonitoring'));
    }

    public function create($laporan_monitoring_id)
    {
        $laporan = LaporanHasilMonitoring::with(['puskesmas', 'perlengkapan.items'])->findOrFail($laporan_monitoring_id);
        
        // Pastikan hanya laporan yang disetujui oleh Kepala P2PTM
        if ($laporan->status_laporan !== 'disetujui') {
            return redirect()->route('pengguna.perlengkapan.index')->with('error', 'Laporan Hasil Monitoring belum disetujui oleh Kepala P2PTM.');
        }

        return view('pengguna.perlengkapan_tugas.create', compact('laporan'));
    }

    public function store(Request $request, $laporan_monitoring_id)
    {
        $request->validate([
            'nama_barang' => 'required|array',
            'nama_barang.*' => 'required|string',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        $laporan = LaporanHasilMonitoring::findOrFail($laporan_monitoring_id);

        $perlengkapan = PerlengkapanTugas::updateOrCreate(
            ['laporan_monitoring_id' => $laporan->id],
            ['status' => 'disiapkan', 'catatan' => $request->catatan]
        );

        // Hapus item lama (jika edit)
        $perlengkapan->items()->delete();

        // Insert item baru
        foreach ($request->nama_barang as $index => $nama) {
            if (!empty($nama) && !empty($request->jumlah[$index])) {
                PerlengkapanTugasItem::create([
                    'perlengkapan_tugas_id' => $perlengkapan->id,
                    'nama_barang' => $nama,
                    'jumlah' => $request->jumlah[$index],
                    'satuan' => $request->satuan[$index] ?? 'Unit'
                ]);
            }
        }

        return redirect()->route('pengguna.perlengkapan.index')
            ->with('success', 'Daftar Perlengkapan Logistik berhasil disimpan.');
    }

    public function print($id)
    {
        $perlengkapan = PerlengkapanTugas::with(['laporanMonitoring.pegawai', 'laporanMonitoring.puskesmas', 'suratTugas.pegawai', 'items'])->findOrFail($id);
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        
        return view('pengguna.perlengkapan_tugas.print', compact('perlengkapan', 'kepalaAktif'));
    }
}
