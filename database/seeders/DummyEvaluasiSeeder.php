<?php

use Illuminate\Support\Facades\DB;
use App\Models\EvaluasiSus;
use App\Models\User;

$userIds = [42, 44, 49, 50, 51];

foreach ($userIds as $id) {
    $q1 = rand(4, 5);
    $q2 = rand(1, 2);
    $q3 = rand(4, 5);
    $q4 = rand(1, 2);
    $q5 = rand(4, 5);
    $q6 = rand(1, 2);
    $q7 = rand(4, 5);
    $q8 = rand(1, 2);
    $q9 = rand(4, 5);
    $q10 = rand(1, 2);
    
    $skorGanjil = ($q1 - 1) + ($q3 - 1) + ($q5 - 1) + ($q7 - 1) + ($q9 - 1);
    $skorGenap = (5 - $q2) + (5 - $q4) + (5 - $q6) + (5 - $q8) + (5 - $q10);
    $totalSkorSUS = ($skorGanjil + $skorGenap) * 2.5;
    
    EvaluasiSus::create([
        'user_id' => $id,
        'q1' => $q1,
        'q2' => $q2,
        'q3' => $q3,
        'q4' => $q4,
        'q5' => $q5,
        'q6' => $q6,
        'q7' => $q7,
        'q8' => $q8,
        'q9' => $q9,
        'q10' => $q10,
        'skor_sus' => $totalSkorSUS,
        'saran' => 'Aplikasi sangat memudahkan pencatatan pasien PTM di puskesmas kami.'
    ]);
}

echo "5 data dummy Evaluasi SUS berhasil ditambahkan!\n";
