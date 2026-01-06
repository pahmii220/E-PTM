<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'Username' => 'required|string|max:255|unique:users,Username',
            'nip' => 'required|string|max:50|unique:users,nip',
            'jenis_kelamin' => 'required|string',
            'email' => 'required|email|unique:users,email',
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
            'role_name' => 'pengguna', // default role
            'is_active' => 1, // 👈 WAJIB (menunggu verifikasi admin)
        ]);

        // Login otomatis
        Auth::login($user);

        // Arahkan sesuai role
        switch ($user->role_name) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'petugas':
                return redirect()->route('petugas.dashboard');
            case 'operator':
                return redirect()->route('dashboard.operator');
            default:

            $request->session()->flash('success', 'Register berhasil! Silahkan Login.');
                return redirect('/login');
        }
    }
}
