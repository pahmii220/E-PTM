<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPetugasProfile
{
    public function handle(Request $request, Closure $next)
    {
        if (
            auth()->check() &&
            auth()->user()->role_name === 'petugas' &&
            !auth()->user()->profilPetugasLengkap() && // 🔥 UBAH DI SINI
            !request()->routeIs('petugas.profil*')
        ) {
            return redirect()
                ->route('petugas.profil')
                ->with('warning', 'Harap lengkapi Alamat, Telepon, dan Tanggal Lahir Anda terlebih dahulu untuk dapat menggunakan fitur sistem.');
        }

        return $next($request);
    }
}