<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanAkunController extends Controller
{
    public function index()
    {
        return view('petugas.pengaturan-akun');
    }

    // ===============================
    // GANTI USERNAME
    // ===============================
public function updateUsername(Request $request)
{
    // 1. Definisikan $user di atas agar bisa digunakan di validasi
    $user = Auth::user();

    // 2. Ganti 'users' jadi 'pengguna' dan tambahkan ID agar tidak duplikat dengan diri sendiri
    $request->validate([
        'username' => 'required|string|min:4|unique:pengguna,username,' . $user->id,
        'password' => 'required'
    ]);

    // 3. Lanjutkan sisa kodenya
    if (!Hash::check($request->password, $user->password)) {
        return back()->with('error', 'Password salah.');
    }

    $user->username = $request->username;
    $user->save();

    Auth::logout();

    return redirect('/login')
        ->with('success', 'Username berhasil diubah. Silakan login kembali.');
}

    // ===============================
    // GANTI PASSWORD
    // ===============================
public function updatePassword(Request $request)
{
    $request->validate([
        'password_lama' => 'required',
        'password_baru' => 'required|min:8|confirmed'
    ]);

    $user = Auth::user();

    if (!Hash::check($request->password_lama, $user->password)) {
        return back()->with('error', 'Password lama tidak sesuai.');
    }

    $user->password = Hash::make($request->password_baru);
    $user->save();

    Auth::logout();

    return redirect('/login')
        ->with('success', 'Password berhasil diganti. Silakan login kembali.');
}

}
