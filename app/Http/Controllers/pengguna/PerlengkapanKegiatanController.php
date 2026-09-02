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
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->pegawaiDinkes) {
            return redirect()->back()->with('error', 'Profil Pegawai Dinkes belum lengkap.');
        }

        $bulanInput = $request->input('bulan', \Carbon\Carbon::now()->format('m'));
        $tahunInput = \Carbon\Carbon::now()->format('Y');

        if (strpos($bulanInput, '-') !== false) {
            $parts = explode('-', $bulanInput);
            $tahunInput = $parts[0];
            $bulanInput = $parts[1];
        }
        if ($bulanInput !== 'semua') {
            $bulanInput = str_pad($bulanInput, 2, '0', STR_PAD_LEFT);
        }

        $listBulanIndo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Ambil Laporan Hasil Monitoring yang statusnya disetujui (ACC Kepala) milik pegawai ini
        $query = LaporanHasilMonitoring::with(['puskesmas', 'perlengkapan.items'])
            ->where('status_laporan', 'disetujui')
            ->where('pegawai_id', $user->pegawaiDinkes->id);

        if ($bulanInput !== 'semua') {
            $query->where(function($q) use ($bulanInput, $tahunInput) {
                $q->where(function($sub) use ($bulanInput, $tahunInput) {
                    $sub->whereMonth('tanggal_kunjungan', $bulanInput)
                        ->whereYear('tanggal_kunjungan', $tahunInput);
                })->orWhere(function($sub) use ($bulanInput, $tahunInput) {
                    $sub->whereNull('tanggal_kunjungan')
                        ->whereMonth('created_at', $bulanInput)
                        ->whereYear('created_at', $tahunInput);
                });
            });
        }

        $laporanMonitoring = $query->orderBy('tanggal_disetujui', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('pengguna.perlengkapan_tugas.index', compact('laporanMonitoring', 'bulanInput', 'tahunInput', 'listBulanIndo'));
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
