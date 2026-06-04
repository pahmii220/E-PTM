<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;

class KegiatanPTMController extends Controller
{

    /**
     * Tampilkan daftar kegiatan PTM
     */
    public function index()
    {
        $user = Auth::user();

        $kegiatan = Kegiatan::latest()->paginate(20);

        return view('petugas.kegiatan.index', compact('kegiatan'));
    }


    /**
     * Form tambah kegiatan
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        return view('petugas.kegiatan.create');
    }


    /**
     * Simpan data kegiatan
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'jumlah_peserta' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal' => $request->tanggal,
            'lokasi' => $request->lokasi,
            'jumlah_peserta' => $request->jumlah_peserta,
            'keterangan' => $request->keterangan,
            'puskesmas_id' => $user->puskesmas_id,
        ]);

        return redirect()
            ->route('petugas.kegiatan.index')
            ->with('success', 'Data kegiatan berhasil ditambahkan.');
    }


    /**
     * Form edit kegiatan
     */
    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $kegiatan = Kegiatan::findOrFail($id);

        return view('petugas.kegiatan.edit', compact('kegiatan'));
    }


    /**
     * Update data kegiatan
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $kegiatan = Kegiatan::findOrFail($id);

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'jumlah_peserta' => 'nullable|integer',
            'keterangan' => 'nullable|string',
        ]);

        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal' => $request->tanggal,
            'lokasi' => $request->lokasi,
            'jumlah_peserta' => $request->jumlah_peserta,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('petugas.kegiatan.index')
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }


    /**
     * Hapus kegiatan
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $kegiatan = Kegiatan::findOrFail($id);

        $kegiatan->delete();

        return redirect()
            ->route('petugas.kegiatan.index')
            ->with('success', 'Data kegiatan berhasil dihapus.');
    }
}