<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AktivasiAkunPetugas;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'Nama_Lengkap' => 'required|string|max:255',
            'Username' => 'required|string|max:255|unique:pengguna,Username',
            'nip' => 'required|string|max:50|unique:pengguna,nip',
            'jenis_kelamin' => 'required|string',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Buat user baru
        $user = User::create([
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'Username' => $request->Username,
            'nip' => $request->nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_name' => 'petugas', // default role
            
            // 👇 UBAH KE 0 (0 = Menunggu verifikasi admin, 1 = Aktif)
            'status_aktif' => 0, 
        ]);

        \App\Models\Petugas::create([
            'user_id'      => $user->id,              // Sambungkan ID pengguna
            'nama_pegawai' => $user->Nama_Lengkap,    // Copy nama
            'nip'          => $user->nip,     
            'jabatan'      => 'Belum diisi', 
            'bidang'       => 'Belum diisi',
            'alamat'       => '-',
            'telepon'      => '-',        // Copy NIP
            // Kolom lain (puskesmas_id, jabatan, dll) akan otomatis null/kosong
        ]);

        // =========================================================
        // FITUR AUTO-LOGIN DIHAPUS AGAR TIDAK LANGSUNG MASUK
        // =========================================================
        // Auth::login($user);
        // switch ($user->role_name) { ... } (Blok switch case dihapus)

        // =========================================================
        // REDIRECT KE HALAMAN LOGIN DENGAN PESAN SUKSES
        // =========================================================
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh Admin.');
    }
}