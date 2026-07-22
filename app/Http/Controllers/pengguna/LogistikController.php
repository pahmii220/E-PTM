<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogistikPuskesmas;
use App\Models\Puskesmas;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LogistikController extends Controller
{
    public function index(Request $request)
    {
        $logistikData = LogistikPuskesmas::with('puskesmas')->get();
        return view('pengguna.logistik.index', compact('logistikData'));
    }

    public function cetakBast($id)
    {
        $logistik = LogistikPuskesmas::with('puskesmas')->findOrFail($id);
        
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        // Data untuk PDF
        $data = [
            'logistik' => $logistik,
            'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            'dinkes_name' => 'Dinas Kesehatan Kota/Kabupaten', // Sesuaikan
            'kepalaAktif' => $kepalaAktif
        ];

        $pdf = Pdf::loadView('pengguna.logistik.pdf', $data);
        return $pdf->stream('BAST_Logistik_' . $logistik->puskesmas->nama_puskesmas . '.pdf');
    }

    public function create()
    {
        $puskesmasList = Puskesmas::orderBy('nama_puskesmas', 'asc')->get();
        return view('pengguna.logistik.form', compact('puskesmasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'strip_gula' => 'required|integer|min:0',
            'strip_kolesterol' => 'required|integer|min:0',
            'strip_asam_urat' => 'required|integer|min:0',
            'lancet' => 'required|integer|min:0',
            'kapas_alkohol' => 'required|integer|min:0',
        ]);

        // Cek duplikasi
        $exists = LogistikPuskesmas::where('puskesmas_id', $request->puskesmas_id)->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data logistik untuk Puskesmas ini sudah ada. Silakan edit data yang sudah ada.');
        }

        LogistikPuskesmas::create($request->all());

        return redirect()->route('pengguna.logistik.index')->with('success', 'Data logistik berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $logistik = LogistikPuskesmas::findOrFail($id);
        $puskesmasList = Puskesmas::orderBy('nama_puskesmas', 'asc')->get();
        return view('pengguna.logistik.form', compact('logistik', 'puskesmasList'));
    }

    public function update(Request $request, $id)
    {
        $logistik = LogistikPuskesmas::findOrFail($id);
        
        $request->validate([
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'strip_gula' => 'required|integer|min:0',
            'strip_kolesterol' => 'required|integer|min:0',
            'strip_asam_urat' => 'required|integer|min:0',
            'lancet' => 'required|integer|min:0',
            'kapas_alkohol' => 'required|integer|min:0',
        ]);

        // Cek duplikasi jika mengubah puskesmas_id
        if ($logistik->puskesmas_id != $request->puskesmas_id) {
            $exists = LogistikPuskesmas::where('puskesmas_id', $request->puskesmas_id)->exists();
            if ($exists) {
                return back()->withInput()->with('error', 'Data logistik untuk Puskesmas tersebut sudah ada.');
            }
        }

        $logistik->update($request->all());

        return redirect()->route('pengguna.logistik.index')->with('success', 'Data logistik berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $logistik = LogistikPuskesmas::findOrFail($id);
        $logistik->delete();
        
        return redirect()->route('pengguna.logistik.index')->with('success', 'Data logistik berhasil dihapus!');
    }
}
