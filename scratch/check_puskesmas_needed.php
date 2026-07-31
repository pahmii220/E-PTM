<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$allPuskesmas = App\Models\Puskesmas::orderBy('id')->get();

echo "DAFTAR PUSKESMAS & STATUS PETUGAS:\n";
echo str_repeat('-', 65) . "\n";
foreach ($allPuskesmas as $p) {
    $petugas = App\Models\Petugas::where('puskesmas_id', $p->id)->first();
    $status = $petugas ? "SUDAH ADA (Petugas ID: {$petugas->id}, Nama: {$petugas->nama_pegawai})" : "BELUM ADA [PERLU DIBUAT]";
    echo "ID: {$p->id} | {$p->nama_puskesmas} -> {$status}\n";
}
