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
            !auth()->user()->petugas &&
            !request()->routeIs('petugas.profil*')
        ) {
            return redirect()
                ->route('petugas.profil')
                ->with('warning', 'Lengkapi profil petugas terlebih dahulu');
        }

        return $next($request);
    }
}
