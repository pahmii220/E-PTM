<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Petugas; // Pastikan model Petugas di-import

class CheckPetugasProfile
{
    public function handle(Request $request, Closure $next)
    {
        $pengguna = Auth::user();

        // 1. BYPASS UNTUK ADMIN: Jika yang masuk Admin, langsung loloskan saja
        if ($pengguna && $pengguna->role_name === 'admin') {
            return $next($request);
        }

        // 2. CEK UNTUK PETUGAS: Jika yang masuk Petugas, wajib cek kelengkapan profil
        if ($pengguna && $pengguna->role_name === 'petugas') {
            
            // Cari data petugas berdasarkan ID pengguna yang login
            $profilPetugas = Petugas::where('user_id', $pengguna->id)->first();
            
            // Jika profil belum ada, ATAU puskesmas belum dipilih
            if (!$profilPetugas || empty($profilPetugas->puskesmas_id)) {
                
                // Jangan redirect jika posisinya sudah berada di halaman profil (mencegah loop)
                if (!$request->routeIs('petugas.profil') && !$request->routeIs('petugas.profil.update')) {
                    return redirect()->route('petugas.profil')
                        ->with('error', 'Mohon lengkapi data profil dan asal Puskesmas Anda terlebih dahulu sebelum menginput data PTM.');
                }
            }
        }

        return $next($request);
    }
}