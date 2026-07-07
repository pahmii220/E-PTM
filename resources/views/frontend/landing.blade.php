<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pemantauan PTM | Dinas Kesehatan</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper CSS untuk Slider -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        /* Custom Pagination Swiper */
        .swiper-pagination-bullet {
            background: white;
            opacity: 0.5;
            width: 10px;
            height: 10px;
        }

        .swiper-pagination-bullet-active {
            background: #34d399;
            opacity: 1;
            width: 24px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        /* Text Shadow persis seperti dinkeskalsel.id */
        .hero-text h2,
        .hero-text p,
        .hero-text span {
            text-shadow: 0 0 3px rgba(0, 0, 0, 0.9), 0 0 6px rgba(0, 0, 0, 0.7), 0 0 9px rgba(0, 0, 0, 0.6);
        }

        .hero-text h2 {
            border-left: 4px solid #34d399;
            /* Garis kiri emerald */
            padding-left: 14px;
        }

        .hero-text p {
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800" x-data="{ mobileMenuOpen: false, mobileProfilOpen: false }">

    <!-- 1. TOP BAR & NAVIGATION -->
    <header id="navbar"
        class="fixed w-full top-0 z-50 transition-all duration-300 bg-transparent py-4 border-b border-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo Dinas Kesehatan Kalsel"
                        class="h-12 md:h-14 w-auto drop-shadow-md bg-white/90 backdrop-blur-sm rounded-full p-1 border border-emerald-100"
                        style="max-height: 60px;">
                    <div class="border-l-2 border-emerald-400 pl-3">
                        <span id="nav-title"
                            class="font-extrabold text-base md:text-xl text-white block tracking-tight leading-tight uppercase drop-shadow-lg transition-colors duration-300">DINAS
                            KESEHATAN</span>
                        <span id="nav-subtitle"
                            class="text-[9px] md:text-xs font-semibold text-emerald-100 tracking-wider uppercase block drop-shadow-md transition-colors duration-300">Provinsi
                            Kalimantan Selatan</span>
                    </div>
                </div>

                <!-- Nav Menu Desktop -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#beranda"
                        class="text-white font-semibold hover:text-emerald-300 transition drop-shadow-md nav-link">Beranda</a>

                    <!-- MENU PROFIL DROPDOWN -->
                    <div class="relative group py-4">
                        <button
                            class="text-white font-semibold group-hover:text-emerald-300 transition drop-shadow-md nav-link flex items-center gap-1 focus:outline-none">
                            Profil
                            <i
                                class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>

                        <div
                            class="absolute left-0 top-full mt-[-10px] w-56 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible translate-y-4 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-in-out z-[60] overflow-hidden">
                            <div class="py-2 flex flex-col">
                                <a href="{{ route('frontend.profil') }}"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200">
                                    <i class="fa-regular fa-building w-5"></i> Tentang Kami
                                </a>
                                <a href="{{ route('frontend.profil') }}#visi-misi"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200">
                                    <i class="fa-solid fa-bullseye w-5"></i> Visi dan Misi
                                </a>
                                <a href="{{ route('frontend.struktur') }}"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200 border-t border-gray-50">
                                    <i class="fa-solid fa-sitemap w-5"></i> Struktur Organisasi
                                </a>
                                <a href="{{ route('frontend.profil') }}#kinerja"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200">
                                    <i class="fa-solid fa-file-contract w-5"></i> Perjanjian Kinerja
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="#layanan"
                        class="text-white font-semibold hover:text-emerald-300 transition drop-shadow-md nav-link">Layanan
                        PTM</a>
                    <a href="#edukasi"
                        class="text-white font-semibold hover:text-emerald-300 transition drop-shadow-md nav-link">Edukasi</a>

                    <a href="{{ route('login') }}"
                        class="bg-white/90 backdrop-blur-sm text-emerald-800 hover:bg-white px-5 py-2.5 rounded-full font-bold shadow-lg transition flex items-center space-x-2 border border-emerald-100/50">
                        <i class="fa-solid fa-right-to-bracket text-sm"></i>
                        <span>Login</span>
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white text-2xl drop-shadow-md focus:outline-none"
                    @click="mobileMenuOpen = !mobileMenuOpen">
                    <i class="fa-solid text-white" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer (Alpine.js) -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-full opacity-0"
            class="absolute top-full left-0 w-full bg-emerald-950/95 backdrop-blur-md border-b border-emerald-800 shadow-2xl py-6 px-6 md:hidden flex flex-col space-y-4 z-50"
            style="display: none;">

            <a href="#beranda" @click="mobileMenuOpen = false"
                class="text-white font-semibold text-lg hover:text-emerald-300 transition py-2 border-b border-white/10">Beranda</a>

            <!-- Profil Dropdown Mobile -->
            <div class="flex flex-col">
                <button @click="mobileProfilOpen = !mobileProfilOpen"
                    class="text-white font-semibold text-lg hover:text-emerald-300 transition py-2 border-b border-white/10 flex justify-between items-center focus:outline-none">
                    <span>Profil</span>
                    <i class="fa-solid fa-chevron-down text-sm transition-transform duration-300"
                        :class="mobileProfilOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="mobileProfilOpen"
                    class="pl-4 py-2 flex flex-col space-y-3 bg-emerald-900/50 rounded-lg mt-2">
                    <a href="{{ route('frontend.profil') }}" @click="mobileMenuOpen = false"
                        class="text-emerald-100 hover:text-white text-sm py-1">
                        <i class="fa-regular fa-building w-5"></i> Tentang Kami
                    </a>
                    <a href="{{ route('frontend.profil') }}#visi-misi" @click="mobileMenuOpen = false"
                        class="text-emerald-100 hover:text-white text-sm py-1">
                        <i class="fa-solid fa-bullseye w-5"></i> Visi dan Misi
                    </a>
                    <a href="{{ route('frontend.struktur') }}" @click="mobileMenuOpen = false"
                        class="text-emerald-100 hover:text-white text-sm py-1">
                        <i class="fa-solid fa-sitemap w-5"></i> Struktur Organisasi
                    </a>
                    <a href="{{ route('frontend.profil') }}#kinerja" @click="mobileMenuOpen = false"
                        class="text-emerald-100 hover:text-white text-sm py-1">
                        <i class="fa-solid fa-file-contract w-5"></i> Perjanjian Kinerja
                    </a>
                </div>
            </div>

            <a href="#layanan" @click="mobileMenuOpen = false"
                class="text-white font-semibold text-lg hover:text-emerald-300 transition py-2 border-b border-white/10">Layanan
                PTM</a>

            <a href="#edukasi" @click="mobileMenuOpen = false"
                class="text-white font-semibold text-lg hover:text-emerald-300 transition py-2 border-b border-white/10">Edukasi</a>

            <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                class="bg-emerald-500 hover:bg-emerald-400 text-white text-center py-3 rounded-xl font-bold shadow-lg transition flex items-center justify-center space-x-2 border border-emerald-400/30">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Login</span>
            </a>
        </div>
    </header>

    <!-- 2. HERO SECTION -->
    <section id="beranda" class="relative text-white flex items-center overflow-hidden"
        style="height:95vh; min-height:600px;">

        <!-- Background Layer -->
        <div class="absolute inset-0 z-[1]">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://static.gatra.com/foldershared/images/2022/rosyid/02-Feb/dinkes_kalsel.jpg');">
            </div>
            <!-- Overlay Gradasi Gelap -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-emerald-900/40 to-emerald-950/80"></div>
        </div>

        <!-- Swiper Slider Container -->
        <div class="swiper myHeroSwiper relative z-[5] w-full h-full pt-[10px]">
            <div class="swiper-wrapper">

                <!-- Slide 1 -->
                <div class="swiper-slide relative flex items-center h-full">
                    <!-- CATATAN PENTING: -mt-20 atau -mt-32 di bawah ini yang memaksa konten NAIK ke atas! -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full -mt-20 md:-mt-32">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                            <div class="hero-text text-white">
                                <span
                                    class="bg-white/20 border border-white/30 backdrop-blur-md px-3 py-1.5 rounded text-sm font-semibold inline-flex items-center gap-2 mb-4">
                                    <i class="fa-solid fa-house-medical"></i> Portal Pemantauan PTM
                                </span>
                                <h2 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight drop-shadow-xl text-white">
                                    Selamat Datang di Portal <span class="text-emerald-400">Aplikasi Manajemen Data dan
                                        Monitoring PTM</span></h2>
                                <p class="text-lg opacity-95 mb-8 drop-shadow-md leading-relaxed text-white">Aplikasi
                                    berbasis web untuk mendukung pengelolaan data, pemantauan, verifikasi, dan pelaporan
                                    Penyakit Tidak Menular
                                    (PTM) secara terintegrasi.</p>
                                <div class="flex gap-4">
                                    <a href="{{ route('frontend.profil') }}"
                                        class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition">Profil
                                        Instansi</a>
                                </div>
                            </div>
                            <div class="text-center hidden md:block">
                                <img src="https://dinkeskalsel.id/public/images/ketua.jpg" alt="Sambutan"
                                    class="h-64 w-64 lg:h-[22rem] lg:w-[22rem] rounded-2xl mx-auto shadow-2xl object-cover border-4 border-white/20 backdrop-blur-sm" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide relative flex items-center h-full">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full -mt-20 md:-mt-32">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                            <div class="hero-text text-white">
                                <span
                                    class="bg-white/20 border border-white/30 backdrop-blur-md px-3 py-1.5 rounded text-sm font-semibold inline-flex items-center gap-2 mb-4">
                                    <i class="fa-solid fa-shield-virus"></i> Program Unggulan
                                </span>
                                <h2 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight drop-shadow-xl text-white">
                                    Pencegahan &amp; Pengendalian Penyakit</h2>
                                <p class="text-lg opacity-95 mb-8 drop-shadow-md leading-relaxed text-white">Pemantauan
                                    Hipertensi, Diabetes Mellitus, Obesitas, serta pelaporan faktor risiko berbasis
                                    Puskesmas komunitas.</p>
                                <div class="flex gap-4">
                                    <a href="#layanan"
                                        class="bg-white text-emerald-800 hover:bg-gray-100 px-6 py-3 rounded-lg font-semibold shadow-lg transition inline-flex items-center">
                                        <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> Pelajari Layanan
                                    </a>
                                </div>
                            </div>
                            <div class="text-center hidden md:block">
                                <img src="https://dinkeskalsel.id/public/images/news/sakit.png"
                                    alt="Pencegahan Penyakit"
                                    class="h-64 w-64 lg:h-[22rem] lg:w-[22rem] rounded-2xl mx-auto shadow-2xl object-cover border-4 border-white/20 backdrop-blur-sm bg-white/10" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Swiper Navigation & Pagination -->
            <!-- Mengubah !bottom-20 menjadi !bottom-8 agar turun -->
            <div class="swiper-pagination !bottom-8 z-[30]"></div>

            <!-- Mengubah bottom-24 menjadi bottom-12 agar panah turun -->
            <div class="absolute right-4 md:right-10 bottom-12 z-[30] flex items-center gap-3">
                <button
                    class="hero-prev h-12 w-12 rounded-full bg-white/20 backdrop-blur border border-white/30 hover:bg-white/40 flex items-center justify-center text-white transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button
                    class="hero-next h-12 w-12 rounded-full bg-white/20 backdrop-blur border border-white/30 hover:bg-white/40 flex items-center justify-center text-white transition">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <!-- Wave Overlay Bottom diletakkan di dalam Swiper (menempel ke bawah absolute) -->
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none z-[6]">
                <svg class="relative block w-full h-[60px] md:h-[20px]" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M0,0 C400,90 800,30 1200,0 L1200,120 L0,120 Z" fill="#ffffff"></path>
                    <path d="M0,0 C400,50 800,100 1200,50 L1200,120 L0,120 Z" fill="#f8fafc" opacity="0.8"></path>
                </svg>
            </div>

        </div>
    </section>

    <!-- 3. PROFIL LEMBAGA -->
    <section id="profil-lembaga" class="py-16 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Gambar Profil -->
                <div class="relative">
                    <img src="https://smart.kalselprov.go.id/uploads/foto/KESEHATAN.jpeg" alt="Dinas Kesehatan Kalsel"
                        class="rounded-2xl shadow-2xl w-full h-[400px] object-cover border-4 border-emerald-50">
                    <!-- Aksen dekoratif -->
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-emerald-100 rounded-full -z-10"></div>
                </div>
                <!-- Teks Profil -->
                <div class="space-y-8">
                    <div>
                        <span
                            class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-3">Tentang
                            Kami</span>
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dinas Kesehatan Provinsi
                            Kalimantan Selatan</h2>
                        <div class="w-20 h-1.5 bg-yellow-400 rounded-full mt-4"></div>
                    </div>

                    <div class="space-y-6 text-gray-600 leading-relaxed text-lg">
                        <p>
                            Dinas Kesehatan Provinsi Kalimantan Selatan merupakan unsur pelaksana urusan pemerintahan di
                            bidang kesehatan yang
                            berkedudukan di bawah dan bertanggung jawab kepada Gubernur Kalimantan Selatan.
                        </p>
                        <p>
                            Kami berkomitmen penuh untuk menyelenggarakan pelayanan kesehatan yang bermutu, merata, dan
                            terjangkau bagi
                            seluruh lapisan masyarakat, dengan berfokus pada upaya promotif, preventif, serta deteksi
                            dini Penyakit
                            Tidak Menular (PTM).
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('frontend.profil') }}"
                            class="inline-flex items-center text-emerald-700 font-bold hover:text-emerald-800 transition duration-300 group text-lg">
                            Selengkapnya
                            <span class="ml-2 transition-transform duration-300 group-hover:translate-x-2">
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3.5 STATISTIK COUNTER SECTION -->
    <section id="statistik-counter"
        class="relative py-16 bg-gradient-to-br from-emerald-900 via-teal-950 to-emerald-950 text-white overflow-hidden">
        <!-- Dekorasi Latar Belakang Bulat -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl">
        </div>
        <div
            class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1 -->
                <div
                    class="bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:scale-105 hover:bg-white/10 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-hospital-user text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2">30+</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Puskesmas Terintegrasi
                    </p>
                    <div
                        class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300">
                    </div>
                </div>

                <!-- Card 2 -->
                <div
                    class="bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:scale-105 hover:bg-white/10 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-file-medical text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2">15.000+</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Skrining Deteksi Dini</p>
                    <div
                        class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300">
                    </div>
                </div>

                <!-- Card 3 -->
                <div
                    class="bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:scale-105 hover:bg-white/10 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-heart-circle-check text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2">85%</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Kepatuhan Rujukan</p>
                    <div
                        class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300">
                    </div>
                </div>

                <!-- Card 4 -->
                <div
                    class="bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:scale-105 hover:bg-white/10 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-clipboard-list text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2">6</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Program Prioritas PTM</p>
                    <div
                        class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. PROGRAM PRIORITAS KESEHATAN MASYARAKAT -->
    <section id="program-prioritas" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul Section -->
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Program Prioritas Kesehatan Masyarakat
                </h2>
                <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
            </div>

            <!-- Grid Program -->
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1: Gizi -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100 group">
                    <div class="h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=600&q=80"
                            alt="Perbaikan Gizi Masyarakat"
                            class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Perbaikan Gizi Masyarakat</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Intervensi gizi spesifik dan sensitif untuk pencegahan stunting, pemberian makanan tambahan,
                            serta edukasi gizi seimbang bagi ibu dan balita.
                        </p>
                    </div>
                </div>

                <!-- Card 2: Promosi Kesehatan -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100 group">
                    <div class="h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=600&q=80"
                            alt="Promosi Kesehatan"
                            class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Promosi Kesehatan</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Gerakan masyarakat hidup sehat melalui edukasi perilaku hidup bersih dan sehat, pemberdayaan
                            masyarakat, dan pengembangan desa/kelurahan siaga aktif.
                        </p>
                    </div>
                </div>

                <!-- Card 3: Kesehatan Lingkungan -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100 group">
                    <div class="h-56 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1447752875215-b2761acb3c5d?auto=format&fit=crop&w=600&q=80"
                            alt="Kesehatan Lingkungan"
                            class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Kesehatan Lingkungan</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">
                            Pengawasan kualitas air bersih, sanitasi, hygiene makanan minuman, dan pengelolaan limbah
                            untuk
                            menciptakan lingkungan sehat bebas penyakit.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 4.5 ALUR LAYANAN SECTION -->
    <section id="layanan" class="py-20 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul Section -->
            <div class="text-center mb-16">
                <span
                    class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-3">Layanan
                    PTM</span>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Alur Pelayanan Aplikasi PTM</h2>
                <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Mekanisme pelayanan terpadu satu data E-PTM Dinas
                    Kesehatan Provinsi Kalimantan Selatan untuk menjangkau setiap individu.</p>
            </div>

            <!-- Alur Pelayanan Stepper -->
            <div class="relative">
                <!-- Garis Penghubung (Hanya Desktop) -->
                <div class="hidden lg:block absolute top-1/2 left-4 right-4 h-0.5 bg-gray-200 -translate-y-12 z-0">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    <!-- Step 1 -->
                    <div
                        class="bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 hover:border-emerald-200 p-6 rounded-2xl shadow-sm transition duration-300 group text-center flex flex-col justify-between">
                        <div>
                            <div
                                class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                                <i class="fa-solid fa-user-check text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                01</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Pemeriksaan Awal</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Warga melakukan skrining kesehatan (tekanan
                                darah, gula darah, berat badan) di Posbindu PTM atau Puskesmas.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 hover:border-emerald-200 p-6 rounded-2xl shadow-sm transition duration-300 group text-center flex flex-col justify-between">
                        <div>
                            <div
                                class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                                <i class="fa-solid fa-laptop-medical text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                02</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Pencatatan PTM</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Petugas kesehatan menginput hasil
                                pemeriksaan secara real-time dan terstruktur ke dalam aplikasi PTM.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 hover:border-emerald-200 p-6 rounded-2xl shadow-sm transition duration-300 group text-center flex flex-col justify-between">
                        <div>
                            <div
                                class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                                <i class="fa-solid fa-user-shield text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                03</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Verifikasi Dinas</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Pegawai Dinas Kesehatan memvalidasi data
                                untuk memastikan akurasi data masukan dari setiap Puskesmas.</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div
                        class="bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 hover:border-emerald-200 p-6 rounded-2xl shadow-sm transition duration-300 group text-center flex flex-col justify-between">
                        <div>
                            <div
                                class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                                <i class="fa-solid fa-chart-line text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                04</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Monitoring & Intervensi</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Kepala P2PTM memantau laporan eksekutif
                                untuk mengambil tindakan preventif dan intervensi rujukan lanjutan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 5. TARGET DINAS KESEHATAN (Aesthetic Version) -->
    <section id="target-dinkes" class="py-24 relative overflow-hidden bg-white">
        <!-- Dekorasi Background -->
        <div
            class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Target Dinas Kesehatan</h2>
                <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
            </div>

            <div x-data="{ active: 1 }" class="flex flex-col md:flex-row items-center justify-center gap-16">

                <!-- Lingkaran Interaktif -->
                <div class="relative w-[320px] h-[320px] md:w-[400px] md:h-[400px] flex items-center justify-center">
                    <!-- Pusat Lingkaran (Pulsing Effect) -->
                    <div
                        class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-emerald-600 shadow-2xl shadow-emerald-500/50 flex items-center justify-center text-white font-bold text-xl animate-pulse">
                        PTM
                    </div>

                    <!-- Tombol Lingkaran (Menggunakan loop untuk posisi presisi) -->
                    <template x-for="(item, index) in [
                    {id: 1, title: 'Cek Kesehatan'}, {id: 2, title: 'Stunting'}, {id: 3, title: 'Tuberkulosis'},
                    {id: 4, title: 'Pengendalian PTM'}, {id: 5, title: 'Mutu Layanan'}, {id: 6, title: 'Kesehatan Kerja'}
                ]" :key="index">
                        <button @click="active = item.id"
                            class="absolute w-24 h-24 md:w-28 md:h-28 rounded-full border-4 border-white shadow-xl transition-all duration-700 ease-out flex items-center justify-center text-[10px] md:text-xs font-bold hover:scale-110"
                            :class="active === item.id ? 'bg-emerald-700 text-white z-20 scale-110' : 'bg-white text-emerald-900 z-10 hover:bg-emerald-50'"
                            :style="`transform: rotate(${index * 60}deg) translate(${window.innerWidth < 768 ? '140px' : '180px'}) rotate(-${index * 60}deg)`">
                            <span class="text-center px-2" x-text="item.title"></span>
                        </button>
                    </template>
                </div>

                <!-- Konten (Kanan) - Efek Glassmorphism -->
                <div class="w-full max-w-lg">
                    <div
                        class="relative p-10 rounded-[2rem] bg-white/50 backdrop-blur-xl border border-white shadow-2xl min-h-[320px] flex flex-col justify-center overflow-hidden">

                        <div x-show="active === 1" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 01</span>
                            <h3 class="text-3xl font-bold text-gray-900">Cek Kesehatan Gratis 46%</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Target cakupan Program Cek Kesehatan Gratis
                                tahun 2026 sebesar 46% dari total penduduk (136 juta). Fokus pada deteksi dini dan
                                pemberian
                                obat gratis selama 15 hari pertama.</p>
                        </div>

                        <div x-show="active === 2" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 02</span>
                            <h3 class="text-3xl font-bold text-gray-900">Penurunan Stunting</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Prioritas melalui penguatan edukasi,
                                perbaikan
                                sistem pendataan, dan akselerasi intervensi gizi pada 1000 Hari Pertama Kehidupan untuk
                                balita.</p>
                        </div>

                        <div x-show="active === 3" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 03</span>
                            <h3 class="text-3xl font-bold text-gray-900">Eliminasi Tuberkulosis</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Strategi peningkatan penemuan kasus,
                                pengobatan
                                lengkap, dan pemantauan hingga sembuh melalui sistem pelaporan yang terintegrasi.</p>
                        </div>

                        <div x-show="active === 4" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 04</span>
                            <h3 class="text-3xl font-bold text-gray-900">Pengendalian PTM</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Skrining massal, pengobatan rutin
                                hipertensi &
                                diabetes, serta edukasi gaya hidup sehat untuk menurunkan angka kematian dini.</p>
                        </div>

                        <div x-show="active === 5" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 05</span>
                            <h3 class="text-3xl font-bold text-gray-900">Mutu Pelayanan</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Penerapan standar pelayanan berbasis data,
                                akreditasi fasilitas kesehatan, dan penguatan kompetensi tenaga kesehatan untuk layanan
                                berkualitas.</p>
                        </div>

                        <div x-show="active === 6" x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs">Target 06</span>
                            <h3 class="text-3xl font-bold text-gray-900">Kesehatan Kerja</h3>
                            <p class="text-gray-600 text-lg leading-relaxed">Pemeriksaan kesehatan berkala pekerja,
                                pembentukan Pos UKK di tempat kerja informal, dan peningkatan pelayanan penyakit akibat
                                kerja.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. KAMPANYE EDUKASI PTM CERDIK SECTION -->
    <section id="edukasi" class="py-20 bg-gray-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul Section -->
            <div class="text-center mb-16">
                <span
                    class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-3">Edukasi
                    Kesehatan</span>
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kampanye Pencegahan PTM: Slogan
                    "CERDIK"</h2>
                <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Mari cegah Penyakit Tidak Menular dengan menerapkan gaya
                    hidup sehat secara konsisten melalui langkah CERDIK setiap hari.</p>
            </div>

            <!-- Grid CERDIK -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- C -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm animate-fade-in">
                        C
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Cek Kesehatan Berkala
                            <i class="fa-solid fa-stethoscope text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Lakukan pemeriksaan fisik secara rutin (tekanan
                            darah, gula darah, berat badan) minimal 1 bulan sekali untuk deteksi dini.</p>
                    </div>
                </div>

                <!-- E -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                        E
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Enyahkan Asap Rokok
                            <i class="fa-solid fa-ban-smoking text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Hindari rokok aktif maupun paparan asap rokok
                            (perokok pasif) untuk meminimalisir risiko penyakit jantung dan paru-paru.</p>
                    </div>
                </div>

                <!-- R -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                        R
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Rajin Aktivitas Fisik
                            <i class="fa-solid fa-person-running text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Lakukan olahraga atau aktivitas fisik
                            intensitas sedang minimal 30 menit sehari untuk membakar energi dan menjaga kebugaran.</p>
                    </div>
                </div>

                <!-- D -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                        D
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Diet Seimbang
                            <i class="fa-solid fa-apple-whole text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Batasi asupan gula, garam, dan lemak berlebih.
                            Perbanyak konsumsi serat melalui buah-buahan dan sayur-sayuran segar setiap hari.</p>
                    </div>
                </div>

                <!-- I -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                        I
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Istirahat Cukup
                            <i class="fa-solid fa-bed text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Penuhi waktu istirahat dan tidur malam
                            berkualitas selama 7-8 jam per hari untuk memulihkan kekebalan tubuh.</p>
                    </div>
                </div>

                <!-- K -->
                <div
                    class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group flex items-start space-x-5">
                    <div
                        class="flex-shrink-0 w-14 h-14 bg-emerald-100 text-emerald-700 font-extrabold text-2xl rounded-full flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                        K
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            Kelola Stres
                            <i class="fa-solid fa-brain text-emerald-500 text-sm"></i>
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed">Kendalikan kecemasan dengan meditasi, relaksasi
                            pikiran, berpikir positif, dan melakukan hobi yang menyenangkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tambahkan Alpine.js untuk fungsi interaktifnya (tambahkan sebelum penutup </body>) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- 4. FOOTER DENGAN PETA 4 KOLOM -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- Kolom 1: Profil -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo Dinas Kesehatan"
                        class="h-12 w-auto bg-white/90 backdrop-blur rounded-full p-1 border border-gray-600">
                    <span class="font-bold text-white text-lg leading-tight uppercase">Dinas
                        Kesehatan<br>Prov. Kalsel</span>
                </div>
                <p class="text-sm leading-relaxed">Mengabdi untuk mewujudkan derajat kesehatan masyarakat yang
                    setinggi-tingginya melalui pelayanan prima dan inovasi digitalisasi satu data.</p>
            </div>

            <!-- Kolom 2: Tautan -->
            <div>
                <h4 class="text-white font-bold mb-5 uppercase tracking-wider text-sm">Tautan Penting</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('frontend.profil') }}" class="hover:text-emerald-400 transition"><i
                                class="fa-solid fa-angle-right text-emerald-600 mr-2"></i> Profil Instansi</a></li>
                    <li><a href="#edukasi" class="hover:text-emerald-400 transition"><i
                                class="fa-solid fa-angle-right text-emerald-600 mr-2"></i> Edukasi PTM</a></li>
                    <li><a href="{{ route('login') }}"
                            class="hover:text-white text-emerald-500 font-semibold mt-2 inline-flex items-center"><i
                                class="fa-solid fa-lock mr-2"></i> Login Portal</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Kontak -->
            <div>
                <h4 class="text-white font-bold mb-5 uppercase tracking-wider text-sm">Kontak Kami</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start">
                        <i class="fa-solid fa-map-location-dot mt-1 mr-3 text-emerald-500"></i>
                        <span>Jl. Belitung Darat No.118, Belitung Utara, Kec. Banjarmasin Barat, Kota Banjarmasin
                            70116</span>
                    </li>
                    <li class="flex items-center"><i class="fa-solid fa-phone mr-3 text-emerald-500"></i> (0511) 3353
                        086</li>
                    <li class="flex items-center"><i class="fa-solid fa-envelope mr-3 text-emerald-500"></i>
                        dinkes@banjarmasinkota.go.id</li>
                </ul>
            </div>

            <!-- Kolom 4: Widget Peta Kecil -->
            <div class="space-y-5">
                <h4 class="text-white font-bold uppercase tracking-wider text-sm">Peta Lokasi</h4>
                <div class="bg-white/10 rounded-lg overflow-hidden shadow border border-gray-700">
                    <div class="relative w-full" style="padding-bottom:56.25%;">
                        <iframe class="absolute inset-0 w-full h-full"
                            src="https://maps.google.com/maps?q=Dinas+Kesehatan+Kota+Banjarmasin+Jl.+Belitung+Darat+No.118+Banjarmasin&t=&z=15&ie=UTF8&iwloc=&output=embed"
                            style="border:0;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            title="Peta Lokasi Dinas Kesehatan Banjarmasin">
                        </iframe>
                    </div>
                </div>
                <a href="https://maps.google.com/?q=Dinas+Kesehatan+Kota+Banjarmasin+Jl.+Belitung+Darat+No.118"
                    target="_blank"
                    class="text-xs text-emerald-400 hover:text-emerald-300 inline-flex items-center transition">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Buka di Maps
                </a>
            </div>

        </div>

        <!-- Copyright -->
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center text-xs">
            <p>&copy; {{ date('Y') }} Dinas Kesehatan Provinsi Kalimantan Selatan. Hak Cipta Dilindungi.</p>
            <div class="mt-4 md:mt-0 flex space-x-4">
                <span class="hover:text-white transition cursor-default"><i
                        class="fa-solid fa-shield-halved text-emerald-500 mr-1"></i> Aman & Terverifikasi</span>
            </div>
        </div>
    </footer>

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".myHeroSwiper", {
            spaceBetween: 50,
            speed: 800,
            loop: true,
            grabCursor: true,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".hero-next", prevEl: ".hero-prev" },
        });

        // Efek Scroll Navbar
        const navbar = document.getElementById('navbar');
        const navTitle = document.getElementById('nav-title');
        const navSubtitle = document.getElementById('nav-subtitle');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-emerald-900', 'shadow-lg', 'border-b-emerald-800');
                navbar.classList.remove('bg-transparent', 'border-transparent');
            } else {
                navbar.classList.add('bg-transparent', 'border-transparent');
                navbar.classList.remove('bg-emerald-900', 'shadow-lg', 'border-b-emerald-800');
            }
        });
    </script>
</body>

</html>
```