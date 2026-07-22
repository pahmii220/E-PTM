<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$eval = \App\Models\EvaluasiSus::all();
foreach($eval as $e) {
    echo "Deleting Evaluasi ID: " . $e->id . " User ID: " . $e->user_id . "\n";
    $e->delete();
}
echo "Done.\n";
