<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Dinas Kesehatan Kota Banjarmasin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- NAVIGATION BAR -->
    <header class="bg-emerald-900 py-4 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo"
                        class="h-14 w-auto bg-white rounded-full p-1 border border-emerald-100"
                        style="max-height: 60px;">
                    <div class="border-l-2 border-emerald-400 pl-3">
                        <span
                            class="font-extrabold text-xl text-white block tracking-tight leading-tight uppercase">DINAS
                            KESEHATAN</span>
                        <span class="text-xs font-semibold text-emerald-100 tracking-wider uppercase block">Kota
                            Banjarmasin</span>
                    </div>
                </div>

                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('frontend.home') }}"
                        class="text-white font-semibold hover:text-emerald-300 transition">Beranda</a>

                    <div class="relative group py-4">
                        <button class="text-emerald-300 font-semibold flex items-center gap-1 focus:outline-none">
                            Profil <i
                                class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 group-hover:rotate-180"></i>
                        </button>
                        <div
                            class="absolute left-0 top-full mt-[-10px] w-56 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible translate-y-4 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">
                            <div class="py-2 flex flex-col">
                                <a href="{{ route('frontend.profil') }}"
                                    class="px-5 py-3 text-sm font-medium text-emerald-600 bg-emerald-50 pl-6 border-l-4 border-emerald-500"><i
                                        class="fa-regular fa-building w-5"></i> Tentang Kami</a>
                                <a href="{{ route('frontend.profil') }}#visi-misi"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200"><i
                                        class="fa-solid fa-bullseye w-5"></i> Visi Misi</a>
                                <a href="{{ route('frontend.struktur') }}"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200 border-t border-gray-50">
                                    <i class="fa-solid fa-sitemap w-5"></i> Struktur Organisasi
                                </a>
                                <a href="{{ route('frontend.profil') }}#kinerja"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200"><i
                                        class="fa-solid fa-file-contract w-5"></i> Perjanjian Kinerja</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('frontend.home') }}#layanan"
                        class="text-white font-semibold hover:text-emerald-300 transition">Layanan PTM</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-grow">
        <!-- TENTANG KAMI -->
<section id="tentang-kami" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Bagian Gambar -->
            <div class="relative group">
                <div
                    class="absolute -inset-1 bg-emerald-600 rounded-2xl opacity-20 blur transition duration-200 group-hover:opacity-30">
                </div>
                <img src="https://smart.kalselprov.go.id/uploads/foto/KESEHATAN.jpeg" alt="Dinas Kesehatan Banjarmasin"
                    class="relative rounded-2xl shadow-2xl w-full h-[450px] object-cover border-4 border-white">
            </div>

            <!-- Bagian Teks -->
            <div class="space-y-8">
                <div>
                    <span
                        class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-4">Profil
                        Instansi</span>
                    <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Tentang Kami</h2>
                    <div class="w-20 h-1.5 bg-yellow-400 rounded-full mt-4"></div>
                </div>

                <div class="space-y-5 text-gray-700 leading-relaxed text-lg">
                    <p>
                        Dinas Kesehatan hadir sebagai garda terdepan dalam mewujudkan masyarakat Indonesia yang sehat,
                        produktif, dan sejahtera. Sebagai perangkat daerah yang menyelenggarakan urusan pemerintahan di
                        bidang kesehatan, kami berkomitmen memberikan pelayanan kesehatan berkualitas yang merata dan
                        terjangkau bagi seluruh lapisan masyarakat.
                    </p>
                    <p>
                        Keberadaan kami dilandasi oleh amanat <strong>Undang-Undang Nomor 17 Tahun 2023 tentang
                            Kesehatan</strong> dan <strong>Peraturan Pemerintah Nomor 18 Tahun 2016 tentang Perangkat
                            Daerah</strong>, yang menempatkan kesehatan sebagai hak fundamental setiap warga negara.
                    </p>
                    <p>
                        Perjalanan kami mencerminkan adaptasi berkelanjutan terhadap kebutuhan masyarakat. Dari era
                        Djawatan Kesehatan Kota hingga struktur modern saat ini, kami terus berinovasi untuk menjawab
                        dinamika pembangunan kesehatan nasional yang terus berkembang.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <img src="{{ asset('images/dinkes.png') }}" class="h-12 w-auto bg-white rounded-full p-1">
                    <span class="font-bold text-white text-lg uppercase">Dinas Kesehatan<br>Banjarmasin</span>
                </div>
            </div>
            <div>
                <h4 class="text-white font-bold mb-5 uppercase text-sm">Tautan Penting</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('frontend.profil') }}" class="hover:text-emerald-400">Profil Instansi</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white text-emerald-500 font-bold">Login
                            Petugas</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-5 uppercase text-sm">Kontak</h4>
                <ul class="space-y-3 text-sm">
                    <li>Jl. Belitung Darat No.118, Banjarmasin</li>
                    <li>(0511) 3353 086</li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-white font-bold uppercase text-sm">Peta Lokasi</h4>
                <div class="relative w-full" style="padding-bottom:56.25%;">
                    <iframe class="absolute inset-0 w-full h-full rounded-lg"
                        src="https://maps.google.com/maps?q=Dinas+Kesehatan+Kota+Banjarmasin+Jl.+Belitung+Darat+No.118+Banjarmasin&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        style="border:0;" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>