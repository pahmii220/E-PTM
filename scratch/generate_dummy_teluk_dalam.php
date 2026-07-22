<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use Faker\Factory as Faker;
use Carbon\Carbon;

$faker = Faker::create('id_ID');

$puskesmas_id = 34; // Puskesmas Teluk Dalam
$petugas_id = 54;
$user_id = 75;

$penyakitList = [
    "Hipertensi", "Diabetes Melitus", "Obesitas", "Gangguan Stroke", "Kanker Paru-Paru",
    "Jantung Koroner", "Asma", "Asam Urat", "Kolesterol Tinggi"
];

$tambahBerapa = 6;
$count = 0;

for ($i = 0; $i < $tambahBerapa; $i++) {
    // 1. Buat Peserta
    $peserta = Peserta::create([
        'puskesmas_id' => $puskesmas_id,
        'nik' => $faker->nik,
        'nama_lengkap' => $faker->name,
        'no_rekam_medis' => 'RM-' . strtoupper($faker->bothify('???-###')),
        'tempat_lahir' => $faker->city,
        'tanggal_lahir' => $faker->date('Y-m-d', '2005-01-01'), // Usia acak
        'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
        'pekerjaan' => $faker->jobTitle,
        'alamat' => $faker->address,
        'kecamatan' => 'Banjarmasin Tengah',
        'kontak' => $faker->phoneNumber,
        'status_verifikasi' => 'draft', // Draft agar belum dikirim
        'created_by' => $user_id
    ]);

    // 2. Buat Deteksi Dini PTM
    $bb = $faker->randomFloat(1, 45, 90);
    $tb = $faker->randomFloat(1, 145, 180);
    $imt = round($bb / (($tb/100) * ($tb/100)), 2);

    $sistole = $faker->numberBetween(110, 160);
    $diastole = $faker->numberBetween(70, 100);
    
    $penyakit = $faker->randomElement($penyakitList);

    $deteksi = DeteksiDiniPTM::create([
        'peserta_id' => $peserta->id,
        'puskesmas_id' => $puskesmas_id,
        'petugas_id' => $petugas_id,
        'tanggal_pemeriksaan' => Carbon::now()->format('Y-m-d'),
        'tekanan_darah' => $sistole . '/' . $diastole,
        'gula_darah' => $faker->numberBetween(80, 200),
        'kolesterol' => $faker->numberBetween(150, 250),
        'berat_badan' => $bb,
        'tinggi_badan' => $tb,
        'imt' => $imt,
        'hasil_skrining' => $penyakit,
        'diagnosa_penyakit' => $penyakit . ' Ringan',
        'status_verifikasi' => 'draft',
        'created_by' => $user_id
    ]);

    // 3. Buat Faktor Resiko PTM
    FaktorResikoPTM::create([
        'peserta_id' => $peserta->id,
        'puskesmas_id' => $puskesmas_id,
        'petugas_id' => $petugas_id,
        'tanggal_pemeriksaan' => Carbon::now()->format('Y-m-d'),
        'merokok' => $faker->randomElement(['Ya', 'Tidak']),
        'kurang_sayur_buah' => $faker->randomElement(['Ya', 'Tidak']),
        'kurang_aktivitas_fisik' => $faker->randomElement(['Ya', 'Tidak']),
        'konsumsi_alkohol' => $faker->randomElement(['Ya', 'Tidak']),
        'stres' => $faker->randomElement(['Ya', 'Tidak']),
        'status_verifikasi' => 'draft',
        'created_by' => $user_id
    ]);

    $count++;
}

echo "Berhasil membuat $count data baru dengan status DRAFT (Belum diajukan) untuk Puskesmas Teluk Dalam.\n";
