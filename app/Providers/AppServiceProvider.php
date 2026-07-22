<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\DB;   // Tambahkan ini
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot()
    {
        // Gunakan View::composer agar variabel tersedia di layouts/master.blade.php
        View::composer('layouts.master', function ($view) {
            $jumlahPendingAdmin = 0;
            $role = null;

            if (Auth::check()) {
                $user = Auth::user();
                $role = $user->role_name;

                // Hanya hitung jika yang login adalah admin
                if ($role === 'admin') {
                    $jumlahPendingAdmin = DB::table('pengguna')
                                            ->where('role_name', 'petugas')
                                            ->where('status_aktif', 0)
                                            ->count();
                                            
                    $latestPendingIdAdmin = DB::table('pengguna')
                                            ->where('role_name', 'petugas')
                                            ->where('status_aktif', 0)
                                            ->orderBy('id', 'desc')
                                            ->value('id') ?? 0;
                }
            }

            // Kirim variabel ke view
            $view->with('jumlahPendingAdmin', $jumlahPendingAdmin);
            $view->with('latestPendingIdAdmin', $latestPendingIdAdmin ?? 0);
            $view->with('role', $role);
        });
    }

}
