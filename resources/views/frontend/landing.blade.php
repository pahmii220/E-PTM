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
    <!-- Flatpickr CSS (Format dd/mm/yyyy) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #e5e7eb !important;
        }
        .flatpickr-day.selected, .flatpickr-day.selected:hover {
            background: #059669 !important;
            border-color: #059669 !important;
        }
    </style>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        /* ============================================
           GLOBAL ANIMATIONS — LANDING PAGE
        ============================================ */

        /* Scroll Reveal */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.7s cubic-bezier(.4,0,.2,1), transform 0.7s cubic-bezier(.4,0,.2,1);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .reveal-left.visible { opacity: 1; transform: translateX(0); }
        .reveal-right {
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .reveal-right.visible { opacity: 1; transform: translateX(0); }

        /* Animated Text Gradient */
        .anim-gradient-text {
            background: linear-gradient(90deg, #34d399, #10b981, #6ee7b7, #059669, #34d399);
            background-size: 300% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: anim-text-flow 4s linear infinite;
        }
        @keyframes anim-text-flow {
            0% { background-position: 0% center; }
            100% { background-position: 300% center; }
        }

        /* Hero floating orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            animation: hero-orb-float 10s ease-in-out infinite;
        }
        @keyframes hero-orb-float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-40px) scale(1.08); }
        }

        /* Hero badge shimmer */
        .hero-badge {
            position: relative;
            overflow: hidden;
        }
        .hero-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 50%, transparent 100%);
            transform: translateX(-100%);
            animation: badge-shine 2.5s ease infinite;
        }
        @keyframes badge-shine {
            0% { transform: translateX(-100%); }
            60%, 100% { transform: translateX(100%); }
        }

        /* Animated heading underline */
        .anim-underline {
            position: relative;
            display: inline-block;
        }
        .anim-underline::after {
            content: '';
            position: absolute;
            left: 0; bottom: -4px;
            height: 3px;
            width: 0;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 9999px;
            transition: width 0.8s ease;
        }
        .anim-underline.visible::after { width: 100%; }

        /* Counter number animation */
        .counter-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .counter-card:hover {
            transform: translateY(-8px) scale(1.03);
        }

        /* Section divider wave pulse */
        @keyframes wave-pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
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

        /* Text Shadow */
        .hero-text h2,
        .hero-text p,
        .hero-text span {
            text-shadow: 0 0 3px rgba(0,0,0,0.9), 0 0 6px rgba(0,0,0,0.7), 0 0 9px rgba(0,0,0,0.6);
        }
        .hero-text h2 {
            border-left: 4px solid #34d399;
            padding-left: 14px;
        }
        .hero-text p { font-weight: 500; }

        /* Hero CTA button pulse */
        .hero-cta-btn {
            animation: hero-btn-pulse 3s ease-in-out infinite;
        }
        @keyframes hero-btn-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.5); }
            50% { box-shadow: 0 0 0 12px rgba(52, 211, 153, 0); }
        }

        /* Floating card animation on hero image */
        .hero-img-wrapper {
            animation: hero-img-float 5s ease-in-out infinite;
        }
        @keyframes hero-img-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* Program cards */
        .program-card {
            transition: transform 0.4s cubic-bezier(.4,0,.2,1), box-shadow 0.4s ease;
        }
        .program-card:hover {
            transform: translateY(-10px) scale(1.01);
            box-shadow: 0 20px 60px rgba(16,185,129,0.15);
        }
        .program-card:hover .program-card-img {
            transform: scale(1.1);
        }
        .program-card-img {
            transition: transform 0.6s ease;
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
                                 <a href="{{ route('frontend.profil') }}#tugas-fungsi"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200">
                                    <i class="fa-solid fa-list-check w-5"></i> Perjanjian kinerja
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="#cek-ptm"
                        class="text-white font-semibold hover:text-emerald-300 transition drop-shadow-md nav-link">Cek Hasil PTM</a>
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
                    <a href="{{ route('frontend.profil') }}#tugas-fungsi" @click="mobileMenuOpen = false"
                        class="text-emerald-100 hover:text-white text-sm py-1">
                        <i class="fa-solid fa-list-check w-5"></i> Perjanjian Kinerja
                    </a>
                </div>
            </div>

            <a href="#cek-ptm" @click="mobileMenuOpen = false"
                class="text-white font-semibold text-lg hover:text-emerald-300 transition py-2 border-b border-white/10">Cek Hasil PTM</a>

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

        <!-- Animated Hero Orbs -->
        <div class="hero-orb" style="width:500px;height:500px;background:radial-gradient(circle,rgba(52,211,153,0.25),transparent);top:-100px;left:-100px;animation-duration:12s;"></div>
        <div class="hero-orb" style="width:350px;height:350px;background:radial-gradient(circle,rgba(16,185,129,0.2),transparent);bottom:0;right:0;animation-duration:9s;animation-delay:-4s;"></div>

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
                                <span class="hero-badge bg-white/20 border border-white/30 backdrop-blur-md px-3 py-1.5 rounded text-sm font-semibold inline-flex items-center gap-2 mb-4">
                                    <i class="fa-solid fa-house-medical"></i> Portal Pemantauan PTM
                                </span>
                                <h2 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight drop-shadow-xl text-white">
                                    Selamat Datang di Portal <span class="text-white">Aplikasi Manajemen Data dan Monitoring PTM</span>
                                </h2>
                                <p class="text-lg opacity-95 mb-8 drop-shadow-md leading-relaxed text-white">Aplikasi berbasis web untuk mendukung pengelolaan data, pemantauan, evaluasi, dan monitoring Penyakit Tidak Menular (PTM) secara terintegrasi.</p>
                                <div class="flex gap-4">
                                    <a href="{{ route('frontend.profil') }}" class="hero-cta-btn bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition">Profil Instansi</a>
                                    <a href="#cek-ptm" class="bg-white/20 hover:bg-white/30 backdrop-blur border border-white/30 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center gap-2">
                                        <i class="fa-solid fa-notes-medical"></i> Cek Hasil PTM
                                    </a>
                                </div>
                            </div>
                            <div class="text-center hidden md:block">
                                <div class="hero-img-wrapper">
                                    <img src="https://dinkeskalsel.id/public/images/ketua.jpg" alt="Sambutan"
                                        class="h-64 w-64 lg:h-[22rem] lg:w-[22rem] rounded-2xl mx-auto shadow-2xl object-cover border-4 border-white/20 backdrop-blur-sm" />
                                </div>
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

    <!-- SECTION CEK HASIL SKRINING PTM PASIEN (PUBLIC) -->
    <section id="cek-ptm" class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdfa 40%, #f8fafc 100%);">
        
        <!-- Animated Background Orbs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="ptm-orb ptm-orb-1"></div>
            <div class="ptm-orb ptm-orb-2"></div>
            <div class="ptm-orb ptm-orb-3"></div>
            <!-- Floating particles -->
            <div class="ptm-particle" style="left:10%;top:20%;animation-delay:0s;"></div>
            <div class="ptm-particle" style="left:85%;top:15%;animation-delay:0.8s;"></div>
            <div class="ptm-particle" style="left:25%;top:75%;animation-delay:1.5s;"></div>
            <div class="ptm-particle" style="left:70%;top:70%;animation-delay:0.4s;"></div>
            <div class="ptm-particle" style="left:50%;top:10%;animation-delay:2s;"></div>
            <div class="ptm-particle" style="left:90%;top:50%;animation-delay:1.2s;"></div>
        </div>

        <style>
            /* ANIMATED ORBS */
            .ptm-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(60px);
                opacity: 0.35;
                animation: ptm-float 8s ease-in-out infinite;
            }
            .ptm-orb-1 {
                width: 400px; height: 400px;
                background: radial-gradient(circle, #6ee7b7, #34d399);
                top: -100px; left: -100px;
                animation-duration: 9s;
            }
            .ptm-orb-2 {
                width: 300px; height: 300px;
                background: radial-gradient(circle, #a7f3d0, #059669);
                bottom: -80px; right: -80px;
                animation-duration: 11s;
                animation-delay: -3s;
            }
            .ptm-orb-3 {
                width: 200px; height: 200px;
                background: radial-gradient(circle, #d1fae5, #10b981);
                top: 50%; left: 55%;
                animation-duration: 7s;
                animation-delay: -1.5s;
            }
            @keyframes ptm-float {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-30px) scale(1.05); }
            }

            /* FLOATING PARTICLES */
            .ptm-particle {
                position: absolute;
                width: 8px; height: 8px;
                background: #10b981;
                border-radius: 50%;
                opacity: 0.4;
                animation: ptm-particle-float 4s ease-in-out infinite;
            }
            @keyframes ptm-particle-float {
                0%, 100% { transform: translateY(0); opacity: 0.4; }
                50% { transform: translateY(-20px); opacity: 0.8; }
            }

            /* ANIMATED GRADIENT BORDER CARD */
            .ptm-card-wrapper {
                position: relative;
                border-radius: 1.5rem;
                padding: 3px;
                background: linear-gradient(135deg, #10b981, #34d399, #059669, #0d9488, #10b981);
                background-size: 300% 300%;
                animation: ptm-border-spin 4s linear infinite;
                box-shadow: 0 0 40px rgba(16, 185, 129, 0.3), 0 25px 50px rgba(0,0,0,0.1);
            }
            @keyframes ptm-border-spin {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .ptm-card-inner {
                background: white;
                border-radius: calc(1.5rem - 3px);
                padding: 2rem 2.5rem;
                position: relative;
                overflow: hidden;
            }
            @media (max-width: 768px) {
                .ptm-card-inner { padding: 1.5rem; }
            }

            /* GLOW PULSE on the wrapper */
            .ptm-card-wrapper::after {
                content: '';
                position: absolute;
                inset: -4px;
                border-radius: 1.6rem;
                background: inherit;
                filter: blur(12px);
                opacity: 0.5;
                z-index: -1;
                animation: ptm-border-spin 4s linear infinite;
            }

            /* BADGE ANIMATION */
            .ptm-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 16px;
                background: linear-gradient(135deg, #d1fae5, #a7f3d0);
                color: #065f46;
                font-weight: 800;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                border-radius: 999px;
                border: 1px solid #6ee7b7;
                animation: ptm-badge-pulse 2.5s ease-in-out infinite;
                margin-bottom: 0.75rem;
            }
            @keyframes ptm-badge-pulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
                50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            }
            .ptm-badge .ptm-dot {
                width: 8px; height: 8px;
                background: #10b981;
                border-radius: 50%;
                animation: ptm-dot-blink 1.2s ease-in-out infinite;
            }
            @keyframes ptm-dot-blink {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.3; transform: scale(0.6); }
            }

            /* SUBMIT BUTTON */
            .ptm-btn {
                width: 100%;
                background: linear-gradient(135deg, #059669, #10b981, #0d9488);
                background-size: 200% 200%;
                color: white;
                font-weight: 800;
                padding: 1rem 2rem;
                border-radius: 0.875rem;
                border: none;
                cursor: pointer;
                font-size: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                transition: all 0.3s;
                animation: ptm-btn-gradient 3s ease infinite;
                box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
                position: relative;
                overflow: hidden;
            }
            .ptm-btn::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transform: translateX(-100%);
                animation: ptm-btn-shine 2.5s ease infinite;
            }
            @keyframes ptm-btn-shine {
                0% { transform: translateX(-100%); }
                60%, 100% { transform: translateX(100%); }
            }
            @keyframes ptm-btn-gradient {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            .ptm-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 30px rgba(16, 185, 129, 0.55);
            }
        </style>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="ptm-badge">
                    <span class="ptm-dot"></span>
                    <i class="fa-solid fa-notes-medical"></i> Portal Pasien — Aktif
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Cek Hasil Skrining Kesehatan Anda
                </h2>
                <p class="text-gray-600 mt-3 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
                    Masukkan NIK dan Tanggal Lahir Anda sesuai KTP untuk melihat riwayat dan hasil pemeriksaan kesehatan PTM.
                </p>
            </div>

            <!-- Animated Border Card -->
            <div class="ptm-card-wrapper">
                <div class="ptm-card-inner">
                    <!-- Inner light effect -->
                    <div class="absolute top-0 left-0 w-48 h-48 bg-emerald-400/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-0 right-0 w-40 h-40 bg-teal-400/5 rounded-full blur-2xl pointer-events-none"></div>

                    <form action="{{ route('frontend.cek_riwayat') }}" method="POST" class="space-y-5" autocomplete="off">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Input NIK -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-id-card text-emerald-500 mr-1"></i>
                                    Nomor Induk Kependudukan (NIK)
                                </label>
                                <div class="relative">
                                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                        placeholder="Contoh: 6371012345670001" required autocomplete="new-password"
                                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 text-gray-800 font-medium text-sm bg-gray-50/50 focus:bg-white">
                                </div>
                                @error('nik')
                                    <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Input Tanggal Lahir -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-calendar-day text-emerald-500 mr-1"></i>
                                    Tanggal Lahir <span class="text-xs text-emerald-600 font-normal">(pilih tanggal)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="tanggal_lahir_input" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir') }}" placeholder="Pilih tanggal lahir Anda"
                                        required readonly autocomplete="off"
                                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 text-gray-800 font-medium text-sm bg-gray-50/50 focus:bg-white cursor-pointer">
                                </div>
                                @error('tanggal_lahir')
                                    <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Info hint -->
                        <p class="text-xs text-gray-400 text-center flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                            Data Anda aman dan hanya dapat diakses oleh Anda sendiri.
                        </p>

                        <div>
                            <button type="submit" class="ptm-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Cek Hasil Skrining Saya</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAMPILAN NOTIFIKASI TIDAK DITEMUKAN -->
            @if(session('status_pencarian') === 'not_found')
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-800 flex items-start space-x-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-xl text-amber-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm md:text-base">Data Pasien Tidak Ditemukan</h4>
                        <p class="text-xs md:text-sm mt-1 text-amber-700">Kombinasi NIK dan Tanggal Lahir yang Anda masukkan belum terdaftar dalam sistem atau belum pernah melakukan pemeriksaan PTM. Silakan kunjungi Puskesmas terdekat untuk melakukan skrining kesehatan gratis.</p>
                    </div>
                </div>
            @endif

            <!-- TAMPILAN NOTIFIKASI SESI BERAKHIR -->
            @if(session('status_pencarian') === 'session_expired')
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-5 text-blue-800 flex items-start space-x-3 shadow-sm">
                    <i class="fa-solid fa-info-circle text-xl text-blue-500 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-sm md:text-base">Sesi Telah Berakhir</h4>
                        <p class="text-xs md:text-sm mt-1 text-blue-700">Sesi Anda telah berakhir atau Anda belum login. Silakan masukkan kembali NIK dan Tanggal Lahir Anda untuk masuk ke Portal Pasien.</p>
                    </div>
                </div>
            @endif
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
                <div class="reveal counter-card bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-hospital-user text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2" data-count="30" data-suffix="+">30+</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Puskesmas Terintegrasi</p>
                    <div class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300"></div>
                </div>

                <!-- Card 2 -->
                <div class="reveal counter-card bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-file-medical text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2" data-count="15000" data-suffix="+">15.000+</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Skrining Deteksi Dini</p>
                    <div class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300"></div>
                </div>

                <!-- Card 3 -->
                <div class="reveal counter-card bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-heart-circle-check text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2" data-count="85" data-suffix="%">85%</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Kepatuhan Rujukan</p>
                    <div class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300"></div>
                </div>

                <!-- Card 4 -->
                <div class="reveal counter-card bg-white/5 backdrop-blur-sm border border-white/10 p-8 rounded-2xl text-center shadow-xl hover:bg-white/10 transition-all duration-300 group">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-400/30 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-clipboard-list text-2xl text-emerald-400 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold tracking-tight mb-2" data-count="6" data-suffix="">6</h3>
                    <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wider">Program Prioritas PTM</p>
                    <div class="w-12 h-1 bg-yellow-400 rounded-full mx-auto mt-4 opacity-70 group-hover:w-20 transition-all duration-300"></div>
                </div>
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
                                darah, gula darah, berat badan) di Puskesmas Terdekat.</p>
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
                                <i class="fa-solid fa-microchip text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                03</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Analisis Sistem (DSS)</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Sistem cerdas menganalisis data secara real-time untuk mendeteksi lonjakan kasus PTM berisiko tinggi secara otomatis.</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div
                        class="bg-gray-50 hover:bg-emerald-50/50 border border-gray-100 hover:border-emerald-200 p-6 rounded-2xl shadow-sm transition duration-300 group text-center flex flex-col justify-between">
                        <div>
                            <div
                                class="w-16 h-16 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                                <i class="fa-solid fa-map-location-dot text-xl"></i>
                            </div>
                            <span
                                class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-1">Langkah
                                04</span>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Monitoring & Intervensi</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Berdasarkan rekomendasi sistem, Pegawai Dinkes diterjunkan untuk monitoring, dan Kepala P2PTM mengambil kebijakan preventif.</p>
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

    <!-- 3. PROFIL LEMBAGA (Dipindah ke bawah) -->
    <section id="profil-lembaga" class="py-16 bg-white relative border-t border-gray-100">
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
            if (window.scrollY > 30) {
                navbar.classList.remove('bg-transparent', 'border-transparent', 'py-4');
                navbar.classList.add('bg-emerald-950/95', 'backdrop-blur-md', 'shadow-xl', 'py-3', 'border-b', 'border-emerald-800/60');
            } else {
                navbar.classList.add('bg-transparent', 'border-transparent', 'py-4');
                navbar.classList.remove('bg-emerald-950/95', 'backdrop-blur-md', 'shadow-xl', 'py-3', 'border-b', 'border-emerald-800/60');
            }
        });

        // =============================================
        //  SCROLL REVEAL ANIMATION
        // =============================================
        (function () {
            const selectors = '.reveal, .reveal-left, .reveal-right, .anim-underline';
            const elements = document.querySelectorAll(selectors);

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        // Stagger delay based on sibling index
                        const siblings = entry.target.parentElement.querySelectorAll(selectors);
                        let idx = 0;
                        siblings.forEach((el, j) => { if (el === entry.target) idx = j; });
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, idx * 120);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });

            elements.forEach(el => observer.observe(el));
        })();

        // =============================================
        //  COUNTER NUMBER ANIMATION
        // =============================================
        (function () {
            const counters = document.querySelectorAll('[data-count]');
            const counterObs = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const el = entry.target;
                    const target = parseFloat(el.getAttribute('data-count'));
                    const suffix = el.getAttribute('data-suffix') || '';
                    const duration = 1800;
                    const step = target / (duration / 16);
                    let current = 0;
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            clearInterval(timer);
                            current = target;
                        }
                        el.textContent = (Number.isInteger(target) ? Math.floor(current) : current.toFixed(0)) + suffix;
                    }, 16);
                    counterObs.unobserve(el);
                });
            }, { threshold: 0.5 });
            counters.forEach(el => counterObs.observe(el));
        })();
    </script>

    <!-- Flatpickr JS (Format dd/mm/yyyy Indonesia) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elTgl = document.getElementById('tanggal_lahir_input');
            if (elTgl) {
                flatpickr(elTgl, {
                    dateFormat: "d/m/Y",
                    allowInput: false,
                    locale: "id",
                    maxDate: "today",
                    disableMobile: false
                });
            }
        });
    </script>
</body>

</html>