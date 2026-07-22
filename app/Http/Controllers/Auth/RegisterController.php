<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AktivasiAkunPetugas;

use App\Models\Puskesmas;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        $puskesmas = Puskesmas::orderBy('kecamatan')->orderBy('nama_puskesmas')->get();
        return view('auth.register', compact('puskesmas'));
    }

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'Nama_Lengkap'  => 'required|string|max:255',
            'Username'      => 'required|string|max:255|unique:pengguna,Username',
            'nip'           => 'required|string|max:50|unique:pengguna,nip',
            'puskesmas_id'  => 'required|exists:puskesmas,id',
            'jenis_kelamin' => 'required|string',
            'email'         => 'required|email|unique:pengguna,email',
            'password'      => 'required|min:6|confirmed',
            'jabatan'       => 'required|string|max:255',
            'bidang'        => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'telepon'       => 'required|string|max:20',
            'alamat'        => 'required|string',
        ]);

        // =========================================================
        // VERIFIKASI & PENCARIAN PROFIL PETUGAS
        // =========================================================
        $petugas = \App\Models\Petugas::where('nip', $request->nip)
            ->whereNotNull('nip')
            ->where('nip', '!=', '')
            ->first();

        // Cek apakah NIP ini sudah memiliki akun terdaftar
        if ($petugas && !empty($petugas->user_id)) {
            return back()->withErrors(['nip' => 'NIP/NIK Anda sudah memiliki akun terdaftar. Silakan hubungi Administrator jika terjadi kendala.'])->withInput();
        }

        // Buat user baru (non-aktif, menunggu verifikasi)
        $user = User::create([
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'Username'     => $request->Username,
            'nip'          => $request->nip,
            'jenis_kelamin'=> $request->jenis_kelamin,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role_name'    => 'petugas',
            'status_aktif' => 0,
        ]);

        if ($petugas) {
            // Skenario A: Jika NIP sudah di-whitelist oleh Admin, update datanya
            $petugas->update([
                'user_id'      => $user->id,
                'nama_pegawai' => $user->Nama_Lengkap,
                'puskesmas_id' => $request->puskesmas_id,
                'jabatan'      => $request->jabatan,
                'bidang'       => $request->bidang,
                'alamat'       => $request->alamat,
                'telepon'      => $request->telepon,
                'tanggal_lahir'=> $request->tanggal_lahir,
            ]);
        } else {
            // Skenario B: Jika NIP belum ada, buat profil petugas baru
            \App\Models\Petugas::create([
                'user_id'      => $user->id,
                'nip'          => $request->nip,
                'nama_pegawai' => $user->Nama_Lengkap,
                'puskesmas_id' => $request->puskesmas_id,
                'jabatan'      => $request->jabatan,
                'bidang'       => $request->bidang,
                'alamat'       => $request->alamat,
                'telepon'      => $request->telepon,
                'tanggal_lahir'=> $request->tanggal_lahir,
            ]);
        }

        // =========================================================
        // KIRIM EMAIL NOTIFIKASI KE ADMIN (TRY-CATCH AGAR REGISTER TIDAK ERROR BILA SMTP DOWN)
        // =========================================================
        try {
            $adminEmails = \App\Models\User::where('role_name', 'admin')->pluck('email')->toArray();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new \App\Mail\NotifikasiRegistrasiBaru($user));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal mengirim email registrasi baru: " . $e->getMessage());
        }

        // =========================================================
        // REDIRECT KE HALAMAN LOGIN DENGAN PESAN SUKSES
        // =========================================================
        return redirect()->route('login')->with('success', 'Registrasi berhasil dilakukan. Mohon menunggu proses verifikasi oleh Administrator. Informasi persetujuan dan detail akun akan dikirimkan melalui email Anda yang terdaftar.');
    }
}