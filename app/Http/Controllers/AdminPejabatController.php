<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KepalaP2ptm;

class AdminPejabatController extends Controller
{
    // Menampilkan halaman kelola pejabat
    public function index()
    {
        $daftarPejabat = KepalaP2ptm::orderBy('dibuat_pada', 'desc')->get();
        return view('admin.pejabat.index', compact('daftarPejabat'));
    }

    // Menyimpan pejabat baru
   public function store(Request $request)
    {
        $request->validate([
            'nama_kepala' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:kepala_p2ptm,nip',
            'jabatan' => 'required|string|max:255'
        ]);

        KepalaP2ptm::create([
            'nama_kepala' => $request->nama_kepala,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'status' => 'nonaktif' // <--- Ubah di sini
        ]);

        return back()->with('success', 'Data pejabat berhasil ditambahkan.');
    }

    // Mengganti pejabat yang aktif secara dinamis (Hanya 1 yang boleh aktif)
    public function setAktif($id)
    {
        // 1. Nonaktifkan SEMUA pejabat terlebih dahulu (Ubah di sini)
        KepalaP2ptm::query()->update(['status' => 'nonaktif']);

        // 2. Aktifkan HANYA pejabat yang dipilih
        $pejabat = KepalaP2ptm::findOrFail($id);
        $pejabat->update(['status' => 'aktif']);

        return back()->with('success', 'Pejabat ' . $pejabat->nama_kepala . ' berhasil di-set sebagai Kepala Aktif. Semua laporan baru akan menggunakan pengesahan beliau.');
    }

    // Menampilkan halaman form edit
    public function edit($id)
    {
        $pejabat = KepalaP2ptm::findOrFail($id);
        return view('admin.pejabat.edit', compact('pejabat')); 
        // (Pastikan nama folder view-nya sesuai dengan lokasimu, ya!)
    }

    // Memproses update data ke database
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kepala' => 'required|string|max:255',
            'nip' => 'required|string|max:50',
            'jabatan' => 'required|string|max:255'
        ]);

        $pejabat = KepalaP2ptm::findOrFail($id);
        $pejabat->update([
            'nama_kepala' => $request->nama_kepala,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan
        ]);

        return redirect()->route('admin.pejabat.index')->with('success', 'Data pejabat berhasil diperbarui.');
    }

    // Menghapus data dari database
    public function destroy($id)
    {
        $pejabat = KepalaP2ptm::findOrFail($id);
        
        // Cek agar admin tidak menghapus pejabat yang sedang aktif
        if ($pejabat->status == 'aktif') {
            return back()->with('error', 'Pejabat yang sedang aktif tidak boleh dihapus. Nonaktifkan terlebih dahulu.');
        }

        $pejabat->delete();
        return back()->with('success', 'Data pejabat berhasil dihapus.');
    }

}