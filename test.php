<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$u = App\Models\User::orderBy('id', 'desc')->first();
echo "Dibuat: " . $u->dibuat_pada . "\n";
echo "Diubah: " . $u->diubah_pada . "\n";
