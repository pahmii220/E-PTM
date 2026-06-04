<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\Puskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetugasProfileController extends Controller
{
    /**
     * Tampilkan form profil petugas
     */
    public function edit()
    {
        // ambil data petugas berdasarkan user login
        $petugas = Petugas::where('user_id', auth()->id())->first();

        // ambil daftar puskesmas
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

        return view('petugas.profil.edit', compact('petugas', 'puskesmas'));
    }

    /**
     * Simpan / update profil petugas
     */
    public function update(Request $request)
    {
        // 1. Validasi semua input dari form profil mandiri
        $request->validate([
            'nip'           => 'nullable|string|max:50',
            'nama_pegawai'  => 'required|string|max:191',
            // Validasi email: wajib unik di tabel pengguna, KECUALI untuk ID milik petugas itu sendiri
            'email'         => 'required|email|unique:pengguna,email,' . auth()->id(), 
            'tanggal_lahir' => 'required|date',
            'telepon'       => 'required|string|max:30',
            'alamat'        => 'required|string',
            'jabatan'       => 'nullable|string|max:100',
            'bidang'        => 'nullable|string|max:100',
            'puskesmas_id'  => 'required|exists:puskesmas,id',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ==========================================
        // 2. UPDATE TABEL AKUN (pengguna)
        // ==========================================
        // Mengupdate nama lengkap dan email baru ke sistem login
        auth()->user()->update([
            'Nama_Lengkap' => $request->nama_pegawai,
            'email'        => $request->email,
        ]);

        // ==========================================
        // 3. UPDATE TABEL PROFIL (petugas)
        // ==========================================
        $petugas = Petugas::firstOrNew([
            'user_id' => auth()->id(),
        ]);

        $petugas->user_id       = auth()->id(); // ✅ WAJIB
        $petugas->nip           = $request->nip;
        $petugas->nama_pegawai  = $request->nama_pegawai;
        $petugas->tanggal_lahir = $request->tanggal_lahir;
        $petugas->telepon       = $request->telepon;
        $petugas->alamat        = $request->alamat;
        $petugas->jabatan       = $request->jabatan;
        $petugas->bidang        = $request->bidang;
        $petugas->puskesmas_id  = $request->puskesmas_id;

        // ==========================================
        // 4. LOGIKA UPLOAD & HAPUS FOTO
        // ==========================================
        if ($request->hasFile('foto')) {
            // Jika ada file foto yang diunggah, hapus foto lama jika ada
            if ($petugas->foto) {
                Storage::disk('public')->delete($petugas->foto);
            }
            // Simpan foto baru dan masukkan path-nya ke kolom foto
            $petugas->foto = $request->file('foto')->store('profil_petugas', 'public');
        } 
        elseif ($request->hapus_foto == '1') {
            // Jika user menekan tombol hapus foto di tampilan UI
            if ($petugas->foto) {
                Storage::disk('public')->delete($petugas->foto);
            }
            $petugas->foto = null;
        }

        $petugas->save(); // 🔥 Simpan data ke tabel petugas

        return redirect()
            ->route('petugas.dashboard')
            ->with('success', 'Profil petugas dan alamat email berhasil diperbarui!');
    }
}