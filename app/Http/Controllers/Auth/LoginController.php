<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
public function login(Request $request)
{
    $request->validate([
        'Username' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('Username', 'password');

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();

        // 🔥 WAJIB: paksa ganti password jika hasil reset admin


        // Redirect sesuai role
        switch ($user->role_name) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Login berhasil!');

            case 'petugas':
                return redirect()->route('petugas.dashboard')
                    ->with('success', 'Login berhasil!');

            case 'operator':
                return redirect()->route('dashboard.operator')
                    ->with('success', 'Login berhasil!');

            case 'pengguna':
                return redirect()->route('pengguna.dashboard')
                    ->with('success', 'Login berhasil!');

            default:
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Role tidak dikenali.');
        }
    }

    return back()->with('error', 'Username atau password salah.');
}


    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
