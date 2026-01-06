<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika belum login, lanjutkan (biar middleware auth yang handle)
        if (!Auth::check()) {
            return $next($request);
        }

        // Jika akun dinonaktifkan oleh admin
        if (Auth::user()->is_active === false) {
            Auth::logout();

            // invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')
                ->withErrors([
                    'Akun Anda telah dinonaktifkan oleh administrator.'
                ]);
        }

        return $next($request);
    }
}
