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
                                <a href="{{ route('frontend.profil') }}#tugas-fungsi"
                                    class="px-5 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 hover:pl-6 transition-all duration-200"><i
                                        class="fa-solid fa-list-check w-5"></i> Perjanjian Kinerja</a>
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

        <!-- VISI DAN MISI -->
        <section id="visi-misi" class="py-20 bg-gradient-to-b from-emerald-50/50 via-teal-50/30 to-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-3">Arah Kebijakan</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Visi dan Misi</h2>
                    <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
                </div>

                <div class="grid lg:grid-cols-3 gap-8 items-stretch">
                    <!-- Kartu Visi -->
                    <div class="bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-950 text-white rounded-3xl p-8 shadow-2xl flex flex-col justify-between relative overflow-hidden group border border-emerald-700/50">
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        <div>
                            <div class="w-14 h-14 bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center mb-6 border border-white/20">
                                <i class="fa-solid fa-eye text-2xl text-emerald-300"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-300 tracking-widest uppercase block mb-2">Visi Utama</span>
                            <h3 class="text-xl sm:text-2xl font-black leading-snug text-white">
                                "Mewujudkan Masyarakat yang Sehat, Mandiri, dan Berkeadilan Melalui Pelayanan Kesehatan Prima"
                            </h3>
                        </div>
                        <p class="text-xs text-emerald-100/80 mt-6 pt-6 border-t border-white/10 leading-relaxed">
                            Komitmen berkelanjutan Dinas Kesehatan dalam menjamin kepastian kualitas hidup sehat masyarakat secara menyeluruh.
                        </p>
                    </div>

                    <!-- Kartu Misi (2 Kolom) -->
                    <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-lg border border-gray-100 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-emerald-600 tracking-widest uppercase block mb-2">Misi Pembangunan Kesehatan</span>
                            <h3 class="text-2xl font-extrabold text-gray-900 mb-6">4 Pilar Misi Strategis</h3>
                            
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div class="flex items-start space-x-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-800 rounded-xl font-black text-sm flex items-center justify-center">01</div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm mb-1">Pemerataan Layanan Kesehatan</h4>
                                        <p class="text-xs text-gray-600 leading-relaxed">Meningkatkan akses dan kualitas pelayanan kesehatan dasar hingga rujukan secara adil dan terjangkau.</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-800 rounded-xl font-black text-sm flex items-center justify-center">02</div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm mb-1">Pengendalian PTM & Deteksi Dini</h4>
                                        <p class="text-xs text-gray-600 leading-relaxed">Mencegah dan mengendalikan faktor risiko Penyakit Tidak Menular melalui integrasi sistem digitalisasi E-PTM.</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-800 rounded-xl font-black text-sm flex items-center justify-center">03</div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm mb-1">Pemberdayaan Masyarakat</h4>
                                        <p class="text-xs text-gray-600 leading-relaxed">Mendorong kemandirian masyarakat hidup sehat melalui Gerakan Masyarakat Hidup Sehat (GERMAS).</p>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 text-emerald-800 rounded-xl font-black text-sm flex items-center justify-center">04</div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm mb-1">Tata Kelola & SDM Profesional</h4>
                                        <p class="text-xs text-gray-600 leading-relaxed">Meningkatkan kapasitas kompetensi sumber daya manusia kesehatan dan tata kelola berbasis teknologi informasi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TUGAS DAN FUNGSI DINAS KESEHATAN -->
        <section id="tugas-fungsi" class="py-20 bg-white border-t border-gray-100">
            <span id="kinerja"></span>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-widest rounded mb-3">Tugas & Fungsi Instansi</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Tugas dan Fungsi</h2>
                    <div class="w-20 h-1.5 bg-yellow-400 rounded-full mx-auto mt-4"></div>
                    <p class="text-gray-500 mt-3 text-xs sm:text-sm font-medium">Diterbitkan: 20 Oct 2023</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <!-- Cards Tugas -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-950 text-white rounded-3xl p-8 shadow-xl relative overflow-hidden border border-emerald-800">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 space-y-4">
                            <div class="w-12 h-12 bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center border border-white/20 text-emerald-300 text-xl">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest block">Landasan Tugas Utama</span>
                            <h3 class="text-xl font-bold text-white leading-snug">Tugas Dinas Kesehatan Kalimantan Selatan</h3>
                            <p class="text-sm text-emerald-100/90 leading-relaxed pt-2 border-t border-white/10">
                                Dinas Kesehatan mempunyai tugas membantu Gubernur melaksanakan urusan Pemerintahan di Bidang Kesehatan yang menjadi kewenangan Daerah dan Tugas pembantuan yang ditugaskan kepada Daerah Provinsi.
                            </p>
                        </div>
                    </div>

                    <!-- List Fungsi (8 Poin) -->
                    <div class="lg:col-span-7 bg-gray-50 border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-gears text-emerald-600"></i>
                            <span>Fungsi Dinas Kesehatan Kalimantan Selatan</span>
                        </h3>

                        <ol class="space-y-3.5 text-xs sm:text-sm text-gray-700 font-medium">
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">1</span>
                                <span class="leading-relaxed">Perumusan kebijakan di bidang kesehatan masyarakat, pencegahan dan pengendalian penyakit, pelayanan kesehatan, kefarmasian, Alat Kesehatan dan PKRT serta sumber daya kesehatan;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">2</span>
                                <span class="leading-relaxed">Pelaksanaan kebijakan Kesehatan Masyarakat;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">3</span>
                                <span class="leading-relaxed">Pelaksanaan kebijakan pencegahan dan pengendalian penyakit;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">4</span>
                                <span class="leading-relaxed">Pelaksanaan kebijakan pelayanan kesehatan;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">5</span>
                                <span class="leading-relaxed">Pelaksanaan kebijakan farmasi dan sumber daya kesehatan;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">6</span>
                                <span class="leading-relaxed">Pembinaan, pengawasan dan pengendalian Unit Pelaksana Teknis Daerah;</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">7</span>
                                <span class="leading-relaxed">Pengelolaan kegiatan kesekretariatan; dan</span>
                            </li>
                            <li class="flex items-start gap-3 bg-white p-3.5 rounded-2xl border border-gray-100 shadow-sm">
                                <span class="flex-shrink-0 w-7 h-7 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs flex items-center justify-center">8</span>
                                <span class="leading-relaxed">Pelaksanaan fungsi lain yang diberikan oleh Gubernur sesuai bidang tugas dan kewenangannya.</span>
                            </li>
                        </ol>
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