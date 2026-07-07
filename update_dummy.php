<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$pesertas = DB::table('peserta')->where('no_rekam_medis', 'like', 'RM-DUMMY-%')->get();

foreach ($pesertas as $peserta) {
    // 1. Remove DUMMY from RM
    $newRm = str_replace('RM-DUMMY-', 'RM-', $peserta->no_rekam_medis);

    // 2. Make an Indonesian phone number
    $randomDigits = rand(100000000, 999999999);
    $newPhone = '08' . $randomDigits;

    DB::table('peserta')->where('id', $peserta->id)->update([
        'no_rekam_medis' => $newRm,
        'kontak' => $newPhone
    ]);
}

echo "Berhasil mengupdate " . count($pesertas) . " data. No RM sekarang menggunakan format 'RM-xxxxx' dan nomor telepon berawalan '08'.\n";
