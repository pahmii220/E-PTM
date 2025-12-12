<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in route/web: ->middleware('role:admin,petugas')
     * Laravel will pass parameters after colon as separate arguments.
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // jika belum login → redirect ke login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // normalisasi: jika parameter dilewatkan sebagai satu string "a|b" atau "a,b", split juga
        if (count($roles) === 1) {
            $raw = $roles[0];
            if (strpos($raw, '|') !== false) {
                $roles = explode('|', $raw);
            } elseif (strpos($raw, ',') !== false) {
                $roles = explode(',', $raw);
            }
        }

        // bersihkan spasi
        $roles = array_map('trim', $roles);

        // izinkan bila role user ada di daftar role yang diizinkan
        if (in_array(Auth::user()->role_name, $roles, true)) {
            return $next($request);
        }

        // jika tidak diizinkan → abort 403 atau redirect ke halaman sesuai kebijakan
        abort(403, 'Unauthorized');
        // atau jika ingin redirect ke halaman lain:
        // return redirect()->route('some.safe.route')->with('error', 'Anda tidak memiliki akses.');
    }
}
