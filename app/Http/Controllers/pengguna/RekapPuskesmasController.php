<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapPuskesmasController extends Controller
{
    /**
     * Constructor
     * Role: Pengguna (Dinkes)
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,pengguna']);
    }

    /**
     * ==============================
     * HALAMAN REKAP PER PUSKESMAS
     * ==============================
     */
public function index()
{
    $rekapPuskesmas = Puskesmas::withCount([
        'pasien as total_pasien',
        'deteksiDini as total_deteksi',
        'faktorResiko as total_faktor',
    ])
    ->orderBy('nama_puskesmas', 'asc')
    ->get();

    return view('pengguna.rekap_puskesmas.index', compact('rekapPuskesmas'));
}




    /**
     * ==============================
     * CETAK LAPORAN REKAP
     * ==============================
     */
public function print()
{
    $rekapPuskesmas = Puskesmas::withCount([
        'pasien as total_pasien',
        'deteksiDini as total_deteksi',
        'faktorResiko as total_faktor',
    ])
    ->orderBy('nama_puskesmas', 'asc')
    ->get();

    return view('pengguna.rekap_puskesmas.print', compact('rekapPuskesmas'));
}

}
