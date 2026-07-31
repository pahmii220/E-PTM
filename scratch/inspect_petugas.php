<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$petugasList = App\Models\Petugas::with('user', 'puskesmas')->get();

foreach ($petugasList as $p) {
    echo "PETUGAS ID: {$p->id}\n";
    echo "  Nama Pegawai: {$p->nama_pegawai}\n";
    echo "  NIP: {$p->nip}\n";
    echo "  Tgl Lahir: " . ($p->tanggal_lahir ? $p->tanggal_lahir->format('Y-m-d') : '-') . "\n";
    echo "  Alamat: {$p->alamat}\n";
    echo "  Jabatan: {$p->jabatan}\n";
    echo "  Bidang (Poli/Program): {$p->bidang}\n";
    echo "  Telepon: {$p->telepon}\n";
    echo "  Puskesmas ID: {$p->puskesmas_id} (" . ($p->puskesmas ? $p->puskesmas->nama_puskesmas : '-') . ")\n";
    if ($p->user) {
        echo "  USER ID: {$p->user->id}\n";
        echo "    Nama_Lengkap: {$p->user->Nama_Lengkap}\n";
        echo "    Username: {$p->user->Username}\n";
        echo "    email: {$p->user->email}\n";
        echo "    role_name: {$p->user->role_name}\n";
        echo "    jenis_kelamin: {$p->user->jenis_kelamin}\n";
        echo "    status_aktif: {$p->user->status_aktif}\n";
    }
    echo str_repeat('-', 50) . "\n";
}
