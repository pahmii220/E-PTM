<?php
use App\Models\Petugas;

$petugas = Petugas::where('nama_pegawai', 'like', '%Mad%')->orWhere('nama_pegawai', 'like', '%Rehan%')->get();

foreach ($petugas as $p) {
    if (str_contains(strtolower($p->nama_pegawai), 'rehan')) {
        $p->update(['nama_pegawai' => 'Ahmad Rizal, A.Md.Kep']);
    } else {
        $p->update(['nama_pegawai' => 'Ns. Fitriani, S.Kep']);
    }
}
echo "Update nama petugas berhasil.\n";
