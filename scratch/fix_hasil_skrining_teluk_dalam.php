<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DeteksiDiniPTM;

$ids = [456, 457, 458, 459, 460, 461];
$hasilBaru = [
    "Dicurigai PTM",
    "Risiko Tinggi",
    "Risiko Tinggi",
    "Risiko Tinggi",
    "Risiko Tinggi",
    "Dicurigai PTM"
];

foreach ($ids as $index => $id) {
    DeteksiDiniPTM::where('id', $id)->update([
        'hasil_skrining' => $hasilBaru[$index]
    ]);
}

echo "Berhasil memperbaiki hasil_skrining untuk 6 data terakhir Teluk Dalam.\n";
