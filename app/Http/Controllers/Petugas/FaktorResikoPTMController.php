<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaktorResikoPTM;
use App\Models\Pasien;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;

class FaktorResikoPTMController extends Controller
{
    /**
     * Tampilkan daftar data
     */
 public function index()
{
    $user = Auth::user();

    if (in_array($user->role_name, ['admin', 'pengguna'])) {
        // ADMIN & PENGGUNA: lihat semua
        $faktor = FaktorResikoPTM::with(['pasien', 'puskesmas'])
            ->latest()
            ->get();
    } else {
        // PETUGAS: hanya puskesmas sendiri
        $puskesmasId = $user->petugas->puskesmas_id;

        $faktor = FaktorResikoPTM::with(['pasien', 'puskesmas'])
            ->where('puskesmas_id', $puskesmasId)
            ->latest()
            ->get();
    }

    return view('petugas.faktor_resiko.index', compact('faktor'));
}


    /**
     * Form tambah data
     */
public function create()
{
    if (Auth::user()->role_name === 'pengguna') {
        abort(403);
    }

    if (Auth::user()->role_name === 'admin') {
        // admin boleh lintas puskesmas
        $pasien = Pasien::whereDoesntHave('faktorResikoPTM')
            ->orderBy('nama_lengkap')
            ->get();
    } else {
        // petugas hanya pasien puskesmas sendiri
        $pasien = Pasien::where('puskesmas_id', Auth::user()->petugas->puskesmas_id)
            ->whereDoesntHave('faktorResikoPTM')
            ->orderBy('nama_lengkap')
            ->get();
    }

    return view('petugas.faktor_resiko.create', compact('pasien'));
}



    /**
     * Simpan data baru
     */
    public function store(Request $request)
{
    if (Auth::user()->role_name === 'pengguna') {
        abort(403);
    }

    $request->validate([
        'pasien_id'               => 'required|exists:pasien,id',
        'tanggal_pemeriksaan'     => 'required|date',
        'merokok'                 => 'required|in:Ya,Tidak',
        'alkohol'                 => 'required|in:Ya,Tidak',
        'kurang_aktivitas_fisik'  => 'required|in:Ya,Tidak',
    ]);

    FaktorResikoPTM::create([
        'pasien_id' => $request->pasien_id,
        'puskesmas_id' => Auth::user()->role_name === 'admin'
            ? Pasien::findOrFail($request->pasien_id)->puskesmas_id
            : Auth::user()->petugas->puskesmas_id,
        'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
        'merokok'                => $request->merokok,
        'alkohol'                => $request->alkohol,
        'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
        'petugas_id'             => Auth::user()->role_name === 'petugas'
                                    ? Auth::user()->petugas->id
                                    : null,
        'created_by'             => Auth::id(),
    ]);

    return redirect()
        ->route('petugas.faktor_resiko.index')
        ->with('success', 'Data faktor risiko berhasil ditambahkan.');
}


    /**
     * Form edit
     */
public function edit($id)
{
    $user = Auth::user();

    if ($user->role_name === 'pengguna') {
        abort(403);
    }

    // 🔍 Ambil data faktor sesuai role
    $faktor = $user->role_name === 'admin'
        ? FaktorResikoPTM::findOrFail($id)
        : FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);

    // 🔒 Jika sudah approved → petugas terkunci
    if ($user->role_name !== 'admin' && $faktor->verification_status === 'approved') {
        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat diedit.');
    }

    // ✅ PASIEN
    if ($user->role_name === 'admin') {
        $pasien = Pasien::orderBy('nama_lengkap')->get();
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
    } else {
        $pasien = Pasien::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->orderBy('nama_lengkap')
            ->get();

        // supaya blade aman
        $puskesmas = [];
    }

    return view('petugas.faktor_resiko.edit', compact(
        'faktor',
        'pasien',
        'puskesmas'
    ));
}



    /**
     * Update data
     */
    public function update(Request $request, $id)
{
    $user = Auth::user();

    if ($user->role_name === 'pengguna') {
        abort(403);
    }

    if ($user->role_name === 'admin') {
        $faktor = FaktorResikoPTM::findOrFail($id);
    } else {
        $faktor = FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);
    }

    if ($user->role_name !== 'admin' && $faktor->verification_status === 'approved') {
        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat diubah.');
    }

    $request->validate([
        'tanggal_pemeriksaan'     => 'required|date',
        'merokok'                 => 'required|in:Ya,Tidak',
        'alkohol'                 => 'required|in:Ya,Tidak',
        'kurang_aktivitas_fisik'  => 'required|in:Ya,Tidak',
    ]);

    $faktor->update($request->only([
        'tanggal_pemeriksaan',
        'merokok',
        'alkohol',
        'kurang_aktivitas_fisik',
    ]));

    return redirect()
        ->route('petugas.faktor_resiko.index')
        ->with('success', 'Data faktor risiko berhasil diperbarui.');
}

    /**
     * Hapus data
     */
    public function destroy($id)
{
    $user = Auth::user();

    if ($user->role_name === 'pengguna') {
        abort(403);
    }

    if ($user->role_name === 'admin') {
        $faktor = FaktorResikoPTM::findOrFail($id);
    } else {
        $faktor = FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);
    }

    if ($user->role_name !== 'admin' && $faktor->verification_status === 'approved') {
        return redirect()
            ->route('petugas.faktor_resiko.index')
            ->with('error', 'Data sudah diverifikasi dan tidak dapat dihapus.');
    }

    $faktor->delete();

    return redirect()
        ->route('petugas.faktor_resiko.index')
        ->with('success', 'Data faktor risiko berhasil dihapus.');
}

}
