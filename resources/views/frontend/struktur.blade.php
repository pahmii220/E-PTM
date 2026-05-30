<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - Dinas Kesehatan Kota Banjarmasin</title>
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
                    <img src="{{ asset('images/dinkes.png') }}" class="h-14 w-auto bg-white rounded-full p-1"
                        style="max-height: 60px;">
                    <div class="border-l-2 border-emerald-400 pl-3">
                        <span class="font-extrabold text-xl text-white block leading-tight uppercase">DINAS
                            KESEHATAN</span>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Kota
                            Banjarmasin</span>
                    </div>
                </div>

                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('frontend.home') }}"
                        class="text-white font-semibold hover:text-emerald-300">Beranda</a>
                    <a href="{{ route('frontend.profil') }}"
                        class="text-white font-semibold hover:text-emerald-300">Profil</a>
                    <a href="{{ route('frontend.home') }}#layanan"
                        class="text-white font-semibold hover:text-emerald-300">Layanan PTM</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Header Page -->
    <section class="bg-emerald-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold">Struktur Organisasi</h1>
            <p class="text-emerald-200 mt-2">Peraturan Gubernur Nomor 012 Tahun 2023</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Intro Section -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-12">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <img src="https://dinkeskalsel.id/public/images/default.jpeg" alt="Struktur Organisasi"
                        class="w-full rounded-xl shadow-lg border">
                </div>
                <div class="lg:col-span-2 text-gray-700 leading-relaxed">
                    <p class="mb-4">
                        Berdasarkan Peraturan Gubernur Nomor 012 Tahun 2023 tentang Kedudukan, Susunan Organisasi,
                        Tugas, Fungsi, dan Tata Kerja Perangkat Daerah Provinsi Kalimantan Selatan, Dinas Kesehatan
                        Provinsi Kalimantan Selatan dipimpin oleh Kepala Dinas yang dibantu oleh 1 (satu) Sekretaris, 4
                        (empat) Kepala Bidang, dan 5 (lima) Kepala UPT.
                    </p>
                    <p>
                        Dinas Kesehatan Provinsi juga mempunyai Unit Pelaksana Teknis (UPT) yang bertanggung jawab
                        terhadap obat dan perbekalan kesehatan, krisis kesehatan, kebugaran masyarakat, laboratorium,
                        dan pelatihan petugas kesehatan serta pendidikan tertentu.
                    </p>
                </div>
            </div>
        </div>

        <!-- Structure Grid -->
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Susunan Organisasi</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

            <!-- Card Sekretariat -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-emerald-800 mb-4 border-b pb-2">Sekretariat</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc ml-4">
                    <li>Sub Bagian Perencanaan dan Pelaporan</li>
                    <li>Sub Bagian Keuangan dan Aset</li>
                    <li>Sub Bagian Umum dan Kepegawaian</li>
                </ul>
            </div>

            <!-- Card Kesmas -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-emerald-800 mb-4 border-b pb-2">Bidang Kesehatan Masyarakat</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc ml-4">
                    <li>Seksi Kesehatan Keluarga dan Gizi</li>
                    <li>Seksi Promosi Kesehatan, Pemberdayaan Masyarakat dan Kesehatan Jiwa</li>
                    <li>Seksi Tata Kelola Masyarakat</li>
                </ul>
            </div>

            <!-- Card P2P -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-emerald-800 mb-4 border-b pb-2">Bidang P2P</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc ml-4">
                    <li>Seksi Surveilans dan Imunisasi</li>
                    <li>Seksi Pencegahan dan Pengendalian Penyakit Menular dan Kesehatan Lingkungan</li>
                    <li>Seksi Pencegahan dan Pengendalian Penyakit Tidak Menular</li>
                </ul>
            </div>

            <!-- Card Yankes -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-emerald-800 mb-4 border-b pb-2">Bidang Pelayanan Kesehatan</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc ml-4">
                    <li>Seksi Pelayanan Kesehatan Primer</li>
                    <li>Seksi Pelayanan Kesehatan Rujukan</li>
                    <li>Seksi Tata Kelola dan Mutu Pelayanan Kesehatan</li>
                </ul>
            </div>

            <!-- Card Farmasi -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg text-emerald-800 mb-4 border-b pb-2">Bidang Farmasi & SDK</h3>
                <ul class="text-sm text-gray-600 space-y-2 list-disc ml-4">
                    <li>Seksi Kefarmasian</li>
                    <li>Seksi Alat Kesehatan dan PKRT</li>
                    <li>Seksi Sumber Daya Manusia Kesehatan</li>
                </ul>
            </div>
        </div>

        <!-- UPT Section -->
        <div class="bg-emerald-50 p-8 rounded-2xl border border-emerald-100">
            <h3 class="font-bold text-emerald-900 text-xl mb-6">Daftar Unit Pelaksana Teknis (UPT)</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex items-center space-x-2 text-sm text-gray-700 bg-white p-3 rounded-lg"><i
                        class="fa-solid fa-check-circle text-emerald-600"></i><span>Instalasi Gudang Farmasi
                        (IGFPK)</span></div>
                <div class="flex items-center space-x-2 text-sm text-gray-700 bg-white p-3 rounded-lg"><i
                        class="fa-solid fa-check-circle text-emerald-600"></i><span>Balai Kesehatan Olahraga
                        (BKOM)</span></div>
                <div class="flex items-center space-x-2 text-sm text-gray-700 bg-white p-3 rounded-lg"><i
                        class="fa-solid fa-check-circle text-emerald-600"></i><span>Laboratorium Kesehatan
                        (Labkes)</span></div>
                <div class="flex items-center space-x-2 text-sm text-gray-700 bg-white p-3 rounded-lg"><i
                        class="fa-solid fa-check-circle text-emerald-600"></i><span>Balai Pelatihan Kesehatan
                        (Bapelkes)</span></div>
                <div class="flex items-center space-x-2 text-sm text-gray-700 bg-white p-3 rounded-lg"><i
                        class="fa-solid fa-check-circle text-emerald-600"></i><span>Unit Kewaspadaan Krisis
                        (UKPKK)</span></div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-3 mb-6"><img src="{{ asset('images/dinkes.png') }}"
                        class="h-12 w-auto bg-white rounded-full p-1"><span
                        class="font-bold text-white text-lg uppercase">Dinas Kesehatan<br>Banjarmasin</span></div>
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
                <div class="relative w-full" style="padding-bottom:56.25%;"><iframe
                        class="absolute inset-0 w-full h-full rounded-lg"
                        src="https://maps.google.com/maps?q=Dinas+Kesehatan+Kota+Banjarmasin+Jl.+Belitung+Darat+No.118+Banjarmasin&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        style="border:0;" loading="lazy"></iframe></div>
            </div>
        </div>
    </footer>
</body>

</html>