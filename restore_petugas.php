<?php
use App\Models\Petugas;
use App\Models\User;

// 1. Restore all Petugas names from User's Nama_Lengkap
$allPetugas = Petugas::with('user')->get();
foreach ($allPetugas as $p) {
    if ($p->user) {
        $p->update(['nama_pegawai' => $p->user->Nama_Lengkap]);
    }
}

// 2. Restore "Madiyan" and "Rehan" in User table, then Petugas table
$users = User::whereIn('Nama_Lengkap', ['Ns. Fitriani, S.Kep', 'Ahmad Rizal, A.Md.Kep'])->get();
foreach ($users as $u) {
    if ($u->Nama_Lengkap === 'Ns. Fitriani, S.Kep') {
        $u->update(['Nama_Lengkap' => 'Madiyan']);
        if ($u->petugas) {
            $u->petugas->update(['nama_pegawai' => 'Madiyan']);
        }
    } elseif ($u->Nama_Lengkap === 'Ahmad Rizal, A.Md.Kep') {
        $u->update(['Nama_Lengkap' => 'Rehan']);
        if ($u->petugas) {
            $u->petugas->update(['nama_pegawai' => 'Rehan']);
        }
    }
}

echo "Semua nama telah dikembalikan ke data aslinya.\n";
