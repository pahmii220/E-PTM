<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DeteksiDiniPTM;

$ids = [456, 457, 458, 459, 460, 461];
$penyakitBaru = [
    "Diabetes Melitus",
    "Hipertensi",
    "Stroke",
    "Penyakit Jantung Koroner",
    "Gagal Ginjal Kronis",
    "PPOK (Penyakit Paru Obstruktif Kronis)"
];

foreach ($ids as $index => $id) {
    $penyakit = $penyakitBaru[$index];
    DeteksiDiniPTM::where('id', $id)->update([
        'hasil_skrining' => $penyakit,
        'diagnosa_penyakit' => $penyakit . ' Ringan'
    ]);
}

echo "Berhasil mengubah jenis penyakit untuk 6 data terakhir Teluk Dalam.\n";
