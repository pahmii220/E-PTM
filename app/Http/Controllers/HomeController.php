<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Depan Dinas Kesehatan (Landing Page)
     */
    public function index()
    {
        return view('frontend.landing');
    }

    /**
     * Menampilkan Halaman Profil / Tentang Kami
     */
    public function profil()
    {
        return view('frontend.profil');
    }

public function struktur()
{
    return view('frontend.struktur');
}
}