<?php

use App\Models\User;
use App\Models\Petugas;
use App\Models\Puskesmas;

// Ambil semua data puskesmas yang tersedia
$puskesmasList = Puskesmas::pluck('id')->toArray();
if (empty($puskesmasList)) {
    echo "Error: Tidak ada data puskesmas di database.\n";
    exit;
}

$users = User::where('role_name', 'petugas')->get();
$fixedCount = 0;

foreach ($users as $user) {
    if (!$user->petugas) {
        // Buat record petugas baru
        Petugas::create([
            'user_id' => $user->id,
            'puskesmas_id' => $puskesmasList[array_rand($puskesmasList)],
        ]);
        $fixedCount++;
    } elseif (!$user->petugas->puskesmas_id) {
        // Update record petugas yang puskesmas_id nya null
        $user->petugas->update([
            'puskesmas_id' => $puskesmasList[array_rand($puskesmasList)],
        ]);
        $fixedCount++;
    }
}

echo json_encode([
    'total_petugas' => $users->count(),
    'diperbaiki' => $fixedCount,
    'status' => 'Berhasil memastikan seluruh petugas memiliki puskesmas'
]);
