<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$puskesmas = App\Models\Puskesmas::where('nama_puskesmas', 'LIKE', '%Terminal%')->first();
if (!$puskesmas) { echo "Puskesmas Terminal not found\n"; exit; }
$petugas = App\Models\Petugas::where('puskesmas_id', $puskesmas->id)->first();
if (!$petugas) { echo "Petugas for Puskesmas Terminal not found\n"; exit; }

$peserta = App\Models\Peserta::create([
    'nik' => '6371010101010001', 
    'no_rekam_medis' => 'RM-123456',
    'nama_lengkap' => 'Budi Santoso', 
    'tanggal_lahir' => '1980-05-15', 
    'jenis_kelamin' => 'Laki-laki', 
    'alamat' => 'Jl. Pramuka No. 10', 
    'no_telepon' => '081234567890', 
    'puskesmas_id' => $puskesmas->id, 
    'status_verifikasi' => 'draft', 
    'created_by' => $petugas->user_id
]);

$deteksi = App\Models\DeteksiDiniPTM::create([
    'peserta_id' => $peserta->id, 
    'petugas_id' => $petugas->id, 
    'puskesmas_id' => $puskesmas->id, 
    'tanggal_pemeriksaan' => date('Y-m-d'), 
    'tekanan_darah' => '150/95', 
    'gula_darah' => 210, 
    'kolesterol' => 220, 
    'berat_badan' => 75, 
    'tinggi_badan' => 165, 
    'imt' => round(75/pow(165/100, 2), 2), 
    'hasil_skrining' => 'Risiko Tinggi', 
    'diagnosa_penyakit' => 'Hipertensi, Diabetes Melitus', 
    'status_verifikasi' => 'draft', 
    'created_by' => $petugas->user_id
]);

App\Models\FaktorResikoPTM::create([
    'peserta_id' => $peserta->id, 
    'tanggal_pemeriksaan' => date('Y-m-d'), 
    'puskesmas_id' => $puskesmas->id, 
    'merokok' => 'Ya', 
    'alkohol' => 'Tidak', 
    'kurang_aktivitas_fisik' => 'Ya', 
    'riwayat_keluarga' => 'Ya', 
    'petugas_id' => $petugas->user_id, 
    'status_verifikasi' => 'draft', 
    'created_by' => $petugas->user_id
]);

App\Models\TindakLanjutPTM::create([
    'peserta_id' => $peserta->id,
    'petugas_id' => $petugas->id,
    'puskesmas_id' => $puskesmas->id,
    'deteksi_dini_id' => $deteksi->id, 
    'jenis_tindak_lanjut' => 'rujukan', 
    'catatan_petugas' => 'Pasien dirujuk karena tensi dan gula sangat tinggi', 
    'created_by' => $petugas->user_id
]);

echo "Sukses input data Budi Santoso\n";
