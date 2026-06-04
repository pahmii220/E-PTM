<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaktorResikoPTM;
use App\Models\Pasien;
use App\Models\Puskesmas;
use Illuminate\Support\Facades\Auth;
use App\Notifications\DataPtmBaruNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FaktorResikoPTMController extends Controller
{
    /**
     * Tampilkan daftar data
     */
 public function index()
{
    $user = Auth::user();

    if (in_array($user->role_name, ['admin', 'pegawai'])) {
        // ADMIN & pegawai: lihat semua
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
    $user = Auth::user();

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    // ================= PASIEN =================
    if ($user->role_name === 'admin') {
        $pasien = Pasien::whereDoesntHave('faktorResikoPTM')
            ->orderBy('nama_lengkap')
            ->get();

        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
    } else {
        $pasien = Pasien::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->whereDoesntHave('faktorResikoPTM')
            ->orderBy('nama_lengkap')
            ->get();

        $puskesmas = []; // supaya blade tidak error
    }

    return view('petugas.faktor_resiko.create', compact(
        'pasien',
        'puskesmas'
    ));
}



    /**
     * Simpan data baru
     */
public function store(Request $request)
    {
        // ... (kode validasi dan pengecekan role tetap sama) ...

        $faktorBaru = FaktorResikoPTM::create([
            'pasien_id' => $request->pasien_id,
            'puskesmas_id' => Auth::user()->role_name === 'admin'
                ? Pasien::findOrFail($request->pasien_id)->puskesmas_id
                : Auth::user()->petugas->puskesmas_id,
            'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
            'merokok'                => $request->merokok,
            'alkohol'                => $request->alkohol,
            'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
            'petugas_id' => Auth::id(),
            'created_by' => Auth::id(),
        ]);

        // =======================================================
        // KODE NOTIFIKASI EMAIL KE DINKES
        // =======================================================
        try {
            $faktorBaru->load('pasien'); // Memuat relasi agar nama pasien bisa dibaca
            
            $usersDinkes = User::whereIn('role_name', ['admin', 'pegawai'])->get();
            
            if ($usersDinkes->isNotEmpty()) {
                Notification::send($usersDinkes, new DataPtmBaruNotification($faktorBaru));
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim email Faktor Risiko: ' . $e->getMessage());
        }
        // =======================================================

        return redirect()->route('petugas.faktor_resiko.index')
            ->with('success', 'Data faktor risiko berhasil ditambahkan.');
    }

    /**
     * Form edit
     */
public function edit($id)
{
    $user = Auth::user();

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    // 🔍 Ambil data faktor sesuai role
    $faktor = $user->role_name === 'admin'
        ? FaktorResikoPTM::findOrFail($id)
        : FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);

    // 🔒 Jika sudah approved → petugas terkunci
    if ($user->role_name !== 'admin' && $faktor->status_verifikasi === 'approved') {
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

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    $faktor = $user->role_name === 'admin'
        ? FaktorResikoPTM::findOrFail($id)
        : FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);

    if ($user->role_name !== 'admin' && $faktor->status_verifikasi === 'approved') {
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

    // 🔁 RESET STATUS JIKA SEBELUMNYA DITOLAK
    if ($faktor->status_verifikasi === 'rejected') {
        $faktor->status_verifikasi = 'pending';
        $faktor->catatan_verifikasi = null;
        $faktor->diverifikasi_oleh = null;
        $faktor->diverifikasi_pada = null;
    }

    // UPDATE DATA UTAMA
    $faktor->tanggal_pemeriksaan = $request->tanggal_pemeriksaan;
    $faktor->merokok = $request->merokok;
    $faktor->alkohol = $request->alkohol;
    $faktor->kurang_aktivitas_fisik = $request->kurang_aktivitas_fisik;

    // ✅ INI YANG SEBELUMNYA HILANG
    $faktor->save();

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

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    if ($user->role_name === 'admin') {
        $faktor = FaktorResikoPTM::findOrFail($id);
    } else {
        $faktor = FaktorResikoPTM::where('puskesmas_id', $user->petugas->puskesmas_id)
            ->findOrFail($id);
    }

    if ($user->role_name !== 'admin' && $faktor->status_verifikasi === 'approved') {
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
