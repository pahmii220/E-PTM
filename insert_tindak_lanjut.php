<?php
use App\Models\DeteksiDiniPTM;
use App\Models\TindakLanjutPTM;
use App\Models\Puskesmas;
use App\Models\Petugas;

$puskesmasList = Puskesmas::all();
$totalInsert = 0;

foreach ($puskesmasList as $puskesmas) {
    $deteksiList = DeteksiDiniPTM::where('puskesmas_id', $puskesmas->id)
        ->where('status_verifikasi', 'approved')
        ->get();

    $totalPasien = $deteksiList->count();

    if ($totalPasien <= 1) {
        continue;
    }

    $limit = min(5, $totalPasien);
    $selectedDeteksi = $deteksiList->random($limit);

    $petugas = Petugas::where('puskesmas_id', $puskesmas->id)->first();
    if (!$petugas) {
        $petugas = Petugas::first();
    }

    foreach ($selectedDeteksi as $deteksi) {
        if (TindakLanjutPTM::where('deteksi_dini_id', $deteksi->id)->exists()) {
            continue;
        }

        $jenisTL = 'edukasi';
        $catatan = 'Diberikan penyuluhan tentang diet sehat dan pentingnya aktivitas fisik rutin.';
        
        if ($deteksi->hasil_skrining === 'Berisiko' || !empty($deteksi->diagnosa_penyakit)) {
            if (rand(0, 1) == 1) {
                $jenisTL = 'rujukan';
                $catatan = 'Pasien dirujuk ke faskes tingkat lanjut untuk penanganan medis lebih intensif terkait temuan ' . ($deteksi->diagnosa_penyakit ?: 'risiko PTM') . '.';
            } else {
                $jenisTL = 'monitoring';
                $catatan = 'Disarankan untuk melakukan pemantauan ulang secara rutin setiap minggu di puskesmas.';
            }
        } elseif (rand(0, 1) == 1) {
            $jenisTL = 'anjuran_gaya_hidup';
            $catatan = 'Anjuran untuk mengurangi konsumsi garam dan gula harian.';
        }

        TindakLanjutPTM::create([
            'peserta_id' => $deteksi->peserta_id,
            'deteksi_dini_id' => $deteksi->id,
            'petugas_id' => $petugas->id,
            'jenis_tindak_lanjut' => $jenisTL,
            'tanggal_tindak_lanjut' => date('Y-m-d', strtotime($deteksi->tanggal_pemeriksaan . ' + 1 days')),
            'catatan_petugas' => $catatan,
            'status_tindak_lanjut' => 'sudah'
        ]);
        
        $totalInsert++;
    }
}
echo "Berhasil menyisipkan $totalInsert data tindak lanjut.\n";
