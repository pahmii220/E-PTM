<div x-data="{ sidebarOpen: false }" class="relative">
    {{-- Tombol Hamburger Mobile --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="md:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        <i :class="sidebarOpen ? 'bi bi-x-lg' : 'bi bi-list'" class="text-xl"></i>
    </button>

    {{-- Overlay Mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden">
    </div>

    {{-- Sidebar Utama --}}
    <aside
        class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white flex flex-col z-40 transform transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        {{-- Bagian Logo Dinkes --}}
        <div class="flex flex-col items-center justify-center pt-6 pb-6 border-b border-gray-700">
            <img src="{{ asset('images/dinkes.png') }}" alt="Logo" class="mb-3 max-h-20">
            <h6 class="text-white text-md font-extrabold text-center leading-tight">
                Aplikasi Manajemen Data & Monitoring<br>Penyakit Tidak Menular
            </h6>
        </div>

        {{-- Navigasi Menu --}}
        <nav class="flex-1 overflow-y-auto mt-3 px-2">
            <ul class="flex flex-col gap-1 text-sm">

                {{-- ========================================================== --}}
                {{-- MENU DASHBOARD (UNTUK SEMUA ROLE) --}}
                {{-- ========================================================== --}}
                @if(Auth::user()->role_name === 'admin')
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-house-fill text-lg"></i><span>Dashboard Admin</span>
                        </a>
                    </li>
                @elseif(Auth::user()->role_name === 'petugas')
                    <li>
                        <a href="{{ route('petugas.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('petugas.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-house-fill text-lg"></i><span>Dashboard Petugas</span>
                        </a>
                    </li>
                @elseif(Auth::user()->role_name === 'pegawai')
                    <li>
                        <a href="{{ route('pengguna.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('pengguna.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-speedometer2 text-lg"></i><span>Dashboard Pegawai</span>
                        </a>
                    </li>
                @elseif(Auth::user()->role_name === 'kepala_p2ptm')
                    <li>
                        <a href="{{ route('kepala.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('kepala.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-speedometer2 text-lg"></i><span>Dashboard Kepala P2PTM</span>
                        </a>
                    </li>
                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: ADMIN --}}
                {{-- ========================================================== --}}
                @if(Auth::user()->role_name === 'admin')
                    <li
                        x-data="{ pegawaiOpen: {{ request()->routeIs('admin.master_pengguna.*', 'admin.data_petugas.*', 'admin.pegawai.*', 'admin.data_puskesmas.*', 'admin.pejabat.*', 'admin.reset.*') ? 'true' : 'false' }} }">
                        <button @click="pegawaiOpen = !pegawaiOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-folder2-open text-lg"></i> Manajemen Pengguna</span>
                            <i :class="pegawaiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="pegawaiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">

                            <li>
                                <a href="{{ route('admin.master_pengguna.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.master_pengguna.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-lines-fill me-2"></i> Data Pengguna
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.data_petugas.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.data_petugas.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-people me-2"></i> Data Petugas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.pengguna.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.pegawai.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-vcard me-2"></i> Data Pegawai (Dinkes)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.data_puskesmas.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.data_puskesmas.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-building me-2"></i> Data Puskesmas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.pejabat.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.pejabat.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-badge me-2"></i> Master Pejabat
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.reset.requests') }}"
                                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-500 {{ request()->routeIs('admin.reset.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-key-fill"></i> <span>Reset Password</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li x-data="{ lapOpen: {{ request()->routeIs('kepala.laporan.*', 'kepala.laporan_monitoring.*', 'admin.laporan.*') ? 'true' : 'false' }} }">
                        <button @click="lapOpen = !lapOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('kepala.laporan.*', 'kepala.laporan_monitoring.*', 'admin.laporan.*') ? 'bg-green-600 text-white' : '' }}">
                            <span class="flex items-center gap-3"><i class="bi bi-collection text-lg"></i> Laporan</span>
                            <i :class="lapOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="lapOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden text-sm">
                            <li>
                                <a href="{{ route('kepala.laporan.eksekutif') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.eksekutif') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-tags me-2"></i> Laporan per Kategori
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kepala.laporan.pegawai') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.pegawai') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-badge me-2"></i> Laporan Data Pegawai P2PTM
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kepala.laporan.evaluasi') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.evaluasi') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-patch-check me-2"></i> Laporan Evaluasi Sistem
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kepala.laporan_monitoring.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan_monitoring.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-shield-check me-2"></i> Tinjau Laporan Hasil Monitoring
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kepala.laporan.perlengkapan_tugas') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.perlengkapan_tugas') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-box-seam me-2"></i> Laporan Logistik &amp; Alokasi Alkes
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kepala.laporan.surat_tugas') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.surat_tugas') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-file-earmark-text me-2"></i> Surat Perintah Tugas
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- MENU BARU: EVALUASI SISTEM (SUS) --}}
                    <li>
                        <a href="{{ route('pengguna.evaluasi.report') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('evaluasi.*') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-patch-check text-lg"></i> <span> Hasil Evaluasi</span>
                        </a>
                    </li>
                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: PETUGAS PUSKESMAS (Atau Admin yang punya akses) --}}
                {{-- ========================================================== --}}
                @if(in_array(Auth::user()->role_name, ['petugas']))
                    <li
                        x-data="{ pemeriksaanOpen: {{ (request()->routeIs('petugas.peserta.*', 'petugas.faktor_resiko.*', 'petugas.deteksi_dini.index', 'petugas.deteksi_dini.create', 'petugas.deteksi_dini.edit', 'petugas.tindak_lanjut.*') && request('from') !== 'riwayat') ? 'true' : 'false' }} }">
                        <button @click="pemeriksaanOpen = !pemeriksaanOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-clipboard-pulse text-lg"></i> Pemeriksaan
                                PTM</span>
                            <i :class="pemeriksaanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="pemeriksaanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('petugas.peserta.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ (request()->routeIs('petugas.peserta.*') && request('from') !== 'riwayat') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-lines-fill me-2"></i> Data Pasien
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.deteksi_dini.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ (request()->routeIs('petugas.deteksi_dini.index') || request()->routeIs('petugas.deteksi_dini.create') || request()->routeIs('petugas.deteksi_dini.edit')) ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-clipboard-check me-2"></i> Deteksi Dini PTM
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.tindak_lanjut.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.tindak_lanjut.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-clipboard-plus me-2"></i> Tindak Lanjut PTM
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('petugas.deteksi_dini.riwayat') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ (request()->routeIs('petugas.deteksi_dini.riwayat') || request('from') === 'riwayat') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-clock-history text-lg"></i><span>Riwayat Pemeriksaan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('petugas.laporan.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('petugas.laporan.*') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-journal-text text-lg"></i><span>Pengajuan Laporan</span>
                        </a>
                    </li>



                    <hr class="border-t border-green-600 opacity-50 my-2">
                    
                    {{-- MENU EVALUASI SISTEM --}}
                    <li>
                        <a href="{{ route('petugas.evaluasi.form') }}"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('petugas.evaluasi.form') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-ui-checks text-lg"></i>
                            <span>Evaluasi Sistem</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('petugas.faq') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('petugas.faq') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-question-circle text-lg"></i><span>Pusat Bantuan (FAQ)</span>
                        </a>
                    </li>
                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: PEGAWAI DINKES --}}
                {{-- ========================================================== --}}
                @if(Auth::user()->role_name === 'pegawai')
                    {{-- MENU LAPORAN DINKES / PUSAT DATA & EVALUASI PTM --}}
                    <li x-data="{ laporanPegawaiOpen: {{ request()->routeIs('pengguna.verifikasi_laporan.*', 'pengguna.rekap.*', 'pengguna.laporan_monitoring.*', 'pengguna.perlengkapan.*') ? 'true' : 'false' }} }">
                        <button @click="laporanPegawaiOpen = !laporanPegawaiOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-folder-symlink text-lg"></i> Pusat Data &amp; Evaluasi PTM</span>
                            <i :class="laporanPegawaiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>

                        <ul x-show="laporanPegawaiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden text-sm">
                            <li>
                                <a href="{{ route('pengguna.verifikasi_laporan.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.verifikasi_laporan.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-ui-checks-grid me-2"></i> Monitoring Pelaporan Puskesmas
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('pengguna.laporan_monitoring.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.laporan_monitoring.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Hasil Monitoring
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('pengguna.perlengkapan.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.perlengkapan.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-box-seam me-2"></i> Logistik &amp; Alokasi Alkes
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- MENU TUGAS LUAR --}}
                    <li>
                        <a href="{{ route('pengguna.surat_tugas.index') }}"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('pengguna.surat_tugas.*') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-file-earmark-text text-lg"></i>
                            <span>Pengajuan Surat Tugas Luar</span>
                        </a>
                    </li>


                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: KEPALA P2PTM (Telah disesuaikan menjadi Sub-Menu) --}}
                {{-- ========================================================== --}}
            @if(Auth::user()->role_name === 'kepala_p2ptm')
                <li x-data="{ lapOpen: {{ request()->routeIs('kepala.laporan.*', 'kepala.laporan_monitoring.*') ? 'true' : 'false' }} }">
                    <button @click="lapOpen = !lapOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('kepala.laporan.*', 'kepala.laporan_monitoring.*') ? 'bg-green-600 text-white' : '' }}">
                        <span class="flex items-center gap-3"><i class="bi bi-collection text-lg"></i> Laporan</span>
                        <i :class="lapOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                    </button>
                    <ul x-show="lapOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden text-sm">
                        <li>
                            <a href="{{ route('kepala.laporan.eksekutif') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.eksekutif') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-tags me-2"></i> Laporan per Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kepala.laporan.pegawai') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.pegawai') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-person-badge me-2"></i> Laporan Data Pegawai P2PTM
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kepala.laporan.evaluasi') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.evaluasi') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-patch-check me-2"></i> Laporan Evaluasi Sistem
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kepala.laporan_monitoring.index') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan_monitoring.*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-shield-check me-2"></i> Tinjau Laporan Hasil Monitoring
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kepala.laporan.perlengkapan_tugas') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.perlengkapan_tugas') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-box-seam me-2"></i> Laporan Logistik &amp; Alokasi Alkes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kepala.laporan.surat_tugas') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.surat_tugas') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-file-earmark-text me-2"></i> Surat Perintah Tugas
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="mt-2 border-t border-gray-700 pt-2">
                    <a href="{{ route('kepala.surat_tugas.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('kepala.surat_tugas.*') ? 'bg-green-600 text-white' : '' }}">
                        <i class="bi bi-file-earmark-check text-lg"></i>
                        <span>Verifikasi Surat Tugas Luar</span>
                    </a>
                </li>
            @endif


                {{-- ========================================================== --}}
                {{-- SUB MENU: PROFIL & AKUN (TIDAK UNTUK KEPALA P2PTM) --}}
                {{-- ========================================================== --}}
                @if(Auth::user()->role_name !== 'kepala_p2ptm')
                <li x-data="{ profilOpen: {{ request()->routeIs('petugas.profil', 'petugas.pengaturan', 'pengguna.pegawai_dinkes.edit', 'pengguna.pengaturan') ? 'true' : 'false' }} }" class="mt-2 border-t border-gray-700 pt-2">
                    <button @click="profilOpen = !profilOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                        <span class="flex items-center gap-3"><i class="bi bi-person-circle text-lg"></i> Profil & Akun</span>
                        <i :class="profilOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                    </button>
                    <ul x-show="profilOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden text-sm">
                        @if(Auth::user()->role_name === 'petugas')
                            <li>
                                <a href="{{ route('petugas.profil') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.profil') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person me-2"></i> Edit Profil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.pengaturan') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.pengaturan') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-gear me-2"></i> Pengaturan Akun
                                </a>
                            </li>
                        @elseif(Auth::user()->role_name === 'pegawai')
                            <li>
                                <a href="{{ route('pengguna.pegawai_dinkes.edit', Auth::user()->id) }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.pegawai_dinkes.edit') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person me-2"></i> Edit Profil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.pengaturan') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.pengaturan') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-gear me-2"></i> Pengaturan Akun
                                </a>
                            </li>
                        @elseif(Auth::user()->role_name === 'admin' || Auth::user()->role_name === 'Administrator')
                            <li>
                                <a href="{{ route('admin.profil') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('admin.profil') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-vcard me-2"></i> Profil Admin
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif


                {{-- ========================================================== --}}
                {{-- TOMBOL LOGOUT (UNTUK SEMUA ROLE) --}}
                {{-- ========================================================== --}}
                <li class="mt-3 border-t border-gray-700 pt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                            <i class="bi bi-box-arrow-right text-lg"></i><span>Keluar</span>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </aside>
</div>