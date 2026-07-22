<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$records = \App\Models\DeteksiDiniPtm::with('peserta')->orderBy('tanggal_pemeriksaan', 'asc')->get();
$seen = [];
foreach($records as $p) {
    if ($p->peserta) {
        $name = $p->peserta->nama_lengkap ?? $p->peserta->nama_pasien ?? '';
        if (isset($seen[$name])) {
            echo "Deleting duplicate DeteksiDiniPtm ID: " . $p->id . " for " . $name . "\n";
            $p->delete();
        } else {
            $seen[$name] = true;
        }
    }
}

$recordsT = \App\Models\TindakLanjutPtm::with('deteksiDini.peserta')->orderBy('tanggal_tindak_lanjut', 'asc')->get();
$seenT = [];
foreach($recordsT as $t) {
    if ($t->deteksiDini && $t->deteksiDini->peserta) {
        $name = $t->deteksiDini->peserta->nama_lengkap ?? $t->deteksiDini->peserta->nama_pasien ?? '';
        if (isset($seenT[$name])) {
            echo "Deleting duplicate TindakLanjutPtm ID: " . $t->id . " for " . $name . "\n";
            $t->delete();
        } else {
            $seenT[$name] = true;
        }
    }
}
