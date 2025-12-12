<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data total dari database
        $totalPengguna = User::count();
        $totalPetugas = User::where('role_name', 'petugas')->count();

        // Kirim ke view
        return view('admin.dashboard', compact('totalPengguna',  'totalPetugas'));
    }
}
