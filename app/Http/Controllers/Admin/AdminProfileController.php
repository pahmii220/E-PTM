<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminProfileController extends Controller
{
    /**
     * Tampilkan halaman profil admin
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.profil.index', compact('user'));
    }

    /**
     * Update profil admin (Nama, Username, Email, Password opsional)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'Nama_Lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:4|unique:pengguna,username,' . $user->id,
            'email'        => 'required|email|max:255',
        ];

        // Jika user mengisi password_lama, berarti dia ingin mengganti password
        if ($request->filled('password_lama') || $request->filled('password_baru')) {
            $rules['password_lama'] = 'required';
            $rules['password_baru'] = 'required|min:8|confirmed';
        }

        $request->validate($rules);

        // Update data utama
        $user->Nama_Lengkap = $request->Nama_Lengkap;
        $user->username = $request->username;
        $user->email = $request->email;

        // Update password jika diminta
        if ($request->filled('password_lama')) {
            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai!']);
            }
            $user->password = Hash::make($request->password_baru);
        }

        $user->save();

        // Jika password diganti, harus login ulang demi keamanan
        if ($request->filled('password_lama')) {
            Auth::logout();
            return redirect()->route('login')->with('success', 'Profil & Password berhasil diperbarui. Silakan login kembali.');
        }

        return back()->with('success', 'Profil Administrator berhasil diperbarui!');
    }
}
