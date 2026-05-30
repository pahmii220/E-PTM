<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenPengesahan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PegawaiLaporanController extends Controller
{
    // 1. Menampilkan halaman riwayat pengajuan (berhasil disahkan atau masih menunggu)
    public function index()
    {
        $riwayatPengajuan = DokumenPengesahan::orderBy('created_at', 'desc')->get();
        return view('pengguna.laporan.index', compact('riwayatPengajuan'));
    }

    // 2. Menampilkan form pengajuan pengesahan ke Kepala P2PTM
    public function create()
    {
        // Menyediakan 8 pilihan laporan secara dinamis ke View
        $jenisLaporan = [
            'Laporan Data Peserta PTM',
            'Laporan Deteksi Dini PTM',
            'Laporan Faktor Risiko PTM',
            'Laporan Tindak Lanjut',
            'Laporan Rekap PTM per Puskesmas',
            'Laporan PTM Berdasarkan Kelompok Usia',
            'Laporan Rekapitulasi Hasil Skrining PTM',
            'Laporan Kegiatan PTM'
        ];

        // Menyediakan opsi bulan dan tahun
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Menampilkan tahun dari saat ini mundur ke 5 tahun ke belakang
        $tahun = range(date('Y'), date('Y') - 5); 

        return view('pengguna.laporan.create', compact('jenisLaporan', 'bulan', 'tahun'));
    }

    // 3. Proses menyimpan pengajuan ke database
    public function store(Request $request)
    {
        // Validasi agar pegawai tidak mengirim data kosong
        $request->validate([
            'jenis_laporan' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
        ], [
            'jenis_laporan.required' => 'Jenis laporan wajib dipilih.',
            'bulan.required' => 'Bulan wajib dipilih.',
            'tahun.required' => 'Tahun wajib dipilih.'
        ]);

        // Proteksi: Cek apakah laporan untuk bulan & tahun yang sama sudah pernah diajukan sebelumnya
        $cekDuplikat = DokumenPengesahan::where('jenis_laporan', $request->jenis_laporan)
                                        ->where('bulan', $request->bulan)
                                        ->where('tahun', $request->tahun)
                                        ->first();

        if ($cekDuplikat) {
            return back()->with('error', 'Gagal! Laporan tersebut untuk periode yang sama sudah ada di dalam sistem (berstatus ' . $cekDuplikat->status . ').');
        }

        // Jika lolos validasi, simpan ke database dengan status otomatis 'menunggu'
        DokumenPengesahan::create([
            'jenis_laporan' => $request->jenis_laporan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'status' => 'menunggu' // Sesuai default di database
        ]);

        return redirect()->route('pengguna.pengajuan.index')
                         ->with('success', 'Pengajuan laporan berhasil dikirim dan sedang menunggu pengesahan dari Kepala P2PTM.');
    }

    // 4. Fungsi Mencetak Lembar Pengesahan PDF
    public function cetakPdf($id)
    {
        $dokumen = DokumenPengesahan::with('kepalaP2ptm')->findOrFail($id);

        // Proteksi: Hanya bisa dicetak jika statusnya sudah disahkan
        if ($dokumen->status !== 'disahkan') {
            return back()->with('error', 'Gagal mencetak! Dokumen ini belum disahkan oleh Kepala/Kabid P2PTM.');
        }

        // Generate QR Code ke dalam format base64 agar bisa dibaca oleh PDF
        $qrCode = base64_encode(QrCode::format('svg')->size(100)->generate($dokumen->kode_validasi_qr));

        // Panggil view PDF dan kirim datanya
        $pdf = Pdf::loadView('pengguna.laporan.cetak', compact('dokumen', 'qrCode'));
        
        // Atur ukuran kertas (A4) dan orientasi (Portrait)
        $pdf->setPaper('A4', 'portrait');

        // Unduh otomatis file PDF-nya
        $namaFile = 'Lembar_Pengesahan_' . str_replace(' ', '_', $dokumen->jenis_laporan) . '_' . $dokumen->bulan . '_' . $dokumen->tahun . '.pdf';
        return $pdf->download($namaFile);
    }
}