<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

$faker = Faker::create('id_ID');

$puskesmasId = 5; // Puskesmas ID from Petugas Muhammad Pahmi
$createdBy = 42; // User ID
$jumlahData = 10;

echo "Memulai generate $jumlahData data dummy untuk Puskesmas ID $puskesmasId...\n";

for ($i = 0; $i < $jumlahData; $i++) {
    // 1. Data Peserta
    $nik = $faker->nik;
    
    // Randomize age group
    $birthYear = rand(1950, 2005);
    $birthMonth = rand(1, 12);
    $birthDay = rand(1, 28);
    $tglLahir = "$birthYear-".sprintf('%02d', $birthMonth)."-".sprintf('%02d', $birthDay);
    $jk = $faker->randomElement(['Laki-laki', 'Perempuan']);
    
    $pesertaId = DB::table('peserta')->insertGetId([
        'puskesmas_id' => $puskesmasId,
        'nik' => $nik,
        'nama_lengkap' => $faker->name($jk == 'Laki-laki' ? 'male' : 'female'),
        'no_rekam_medis' => 'RM-DUMMY-' . rand(10000, 99999),
        'tempat_lahir' => $faker->city,
        'tanggal_lahir' => $tglLahir,
        'jenis_kelamin' => $jk,
        'pekerjaan' => $faker->randomElement(['PNS', 'Swasta', 'Wiraswasta', 'Pelajar', 'Buruh', 'IRT']),
        'alamat' => $faker->address,
        'kecamatan' => $faker->citySuffix,
        'kontak' => $faker->phoneNumber,
        'status_verifikasi' => $faker->randomElement(['pending', 'approved']),
        'dibuat_pada' => Carbon::now()->subDays(rand(0, 30)),
        'diubah_pada' => Carbon::now(),
    ]);

    // 2. Data Deteksi Dini
    $sistole = rand(110, 180);
    $diastole = rand(70, 110);
    $bb = rand(50, 100);
    $tb = rand(150, 180);
    $imt = round($bb / (($tb/100) * ($tb/100)), 2);
    $hasilSkrining = $imt > 25 ? 'Risiko Tinggi' : 'Normal';

    DB::table('deteksi_dini_ptm')->insert([
        'peserta_id' => $pesertaId,
        'puskesmas_id' => $puskesmasId,
        'tanggal_pemeriksaan' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
        'tekanan_darah' => "$sistole/$diastole",
        'gula_darah' => rand(80, 250),
        'kolesterol' => rand(150, 250),
        'berat_badan' => $bb,
        'tinggi_badan' => $tb,
        'imt' => $imt,
        'hasil_skrining' => $hasilSkrining,
        'petugas_id' => $createdBy,
        'dibuat_pada' => Carbon::now()->subDays(rand(0, 30)),
        'diubah_pada' => Carbon::now(),
    ]);

    // 3. Data Faktor Risiko
    DB::table('faktor_resiko_ptm')->insert([
        'peserta_id' => $pesertaId,
        'puskesmas_id' => $puskesmasId,
        'tanggal_pemeriksaan' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d'),
        'merokok' => $faker->randomElement(['Ya', 'Tidak']),
        'alkohol' => $faker->randomElement(['Ya', 'Tidak']),
        'kurang_aktivitas_fisik' => $faker->randomElement(['Ya', 'Tidak']),
        'petugas_id' => $createdBy,
        'dibuat_pada' => Carbon::now()->subDays(rand(0, 30)),
        'diubah_pada' => Carbon::now(),
    ]);
}

echo "Berhasil! 10 Data Peserta, Deteksi Dini, dan Faktor Risiko telah diinput ke database tanpa memicu email notifikasi.\n";
