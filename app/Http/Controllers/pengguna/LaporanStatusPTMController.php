<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\DeteksiDiniPTM;

class LaporanStatusPTMController extends Controller
{

public function index()
{

$data = DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
        ->groupBy('hasil_skrining')
        ->get();

return view('pengguna.laporan.status_ptm', compact('data'));

}


}