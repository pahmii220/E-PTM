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
                Aplikasi Pelaporan<br>Penyakit Tidak Menular
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
                        x-data="{ pegawaiOpen: {{ request()->routeIs('admin.data_petugas.*', 'admin.pegawai.*', 'admin.data_puskesmas.*', 'admin.pejabat.*', 'admin.reset.*') ? 'true' : 'false' }} }">
                        <button @click="pegawaiOpen = !pegawaiOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-folder2-open text-lg"></i> Data Master</span>
                            <i :class="pegawaiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="pegawaiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">

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

                    <li
                        x-data="{ pemeriksaanOpen: {{ request()->routeIs('petugas.pasien.*', 'petugas.faktor_resiko.*', 'petugas.deteksi_dini.*', 'petugas.tindak_lanjut.*') ? 'true' : 'false' }} }">
                        <button @click="pemeriksaanOpen = !pemeriksaanOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-clipboard-pulse text-lg"></i> Pemeriksaan
                                PTM</span>
                            <i :class="pemeriksaanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="pemeriksaanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('petugas.pasien.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.pasien.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-lines-fill me-2"></i> Data Peserta
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.faktor_resiko.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.faktor_resiko.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-activity me-2"></i> Faktor Risiko
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.deteksi_dini.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.deteksi_dini.*') ? 'bg-green-500 text-white' : '' }}">
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

                    <li x-data="{ kegiatanOpen: {{ request()->routeIs('petugas.kegiatan.*') ? 'true' : 'false' }} }">
                        <button @click="kegiatanOpen = !kegiatanOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-calendar-event text-lg"></i> Kegiatan
                                PTM</span>
                            <i :class="kegiatanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="kegiatanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('petugas.kegiatan.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.kegiatan.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-calendar-event me-2"></i> Data Kegiatan PTM
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('admin.laporan.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('admin.laporan.*') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph text-lg"></i> <span>Laporan</span>
                        </a>
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
                        x-data="{ pemeriksaanOpen: {{ request()->routeIs('petugas.pasien.*', 'petugas.faktor_resiko.*', 'petugas.deteksi_dini.*', 'petugas.tindak_lanjut.*') ? 'true' : 'false' }} }">
                        <button @click="pemeriksaanOpen = !pemeriksaanOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-clipboard-pulse text-lg"></i> Pemeriksaan
                                PTM</span>
                            <i :class="pemeriksaanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="pemeriksaanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('petugas.pasien.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.pasien.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-lines-fill me-2"></i> Data Peserta
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.faktor_resiko.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.faktor_resiko.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-activity me-2"></i> Faktor Risiko
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('petugas.deteksi_dini.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.deteksi_dini.*') ? 'bg-green-500 text-white' : '' }}">
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

                    <li x-data="{ kegiatanOpen: {{ request()->routeIs('petugas.kegiatan.*') ? 'true' : 'false' }} }">
                        <button @click="kegiatanOpen = !kegiatanOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-calendar-event text-lg"></i> Kegiatan
                                PTM</span>
                            <i :class="kegiatanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="kegiatanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('petugas.kegiatan.index') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('petugas.kegiatan.*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-calendar-event me-2"></i> Data Kegiatan PTM
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: PEGAWAI DINKES --}}
                {{-- ========================================================== --}}
                @if(Auth::user()->role_name === 'pegawai')
                    {{-- MENU VERIFIKASI DATA --}}
                    <li x-data="{ verifikasiOpen: {{ request()->routeIs('pengguna.verifikasi.*') ? 'true' : 'false' }} }">
                        <button @click="verifikasiOpen = !verifikasiOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-check-square text-lg"></i> Verifikasi Data</span>
                            <i :class="verifikasiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="verifikasiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('pengguna.verifikasi.pasien') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.verifikasi.pasien') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-check me-2"></i> Verifikasi Peserta
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.verifikasi.deteksi') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.verifikasi.deteksi') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-clipboard-check me-2"></i> Verifikasi Deteksi Dini
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.verifikasi.faktor') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.verifikasi.faktor') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-activity me-2"></i> Verifikasi Faktor Risiko
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- MENU REKAP LAPORAN --}}
                    <li
                        x-data="{ rekapOpen: {{ request()->routeIs('pengguna.rekap.*', 'pengguna.laporan.status_ptm', 'pengguna.laporan.kelompok_usia', 'pengguna.laporan.kegiatan') ? 'true' : 'false' }} }">
                        <button @click="rekapOpen = !rekapOpen"
                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                            <span class="flex items-center gap-3"><i class="bi bi-bar-chart-fill text-lg"></i> Rekap Laporan</span>
                            <i :class="rekapOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                        </button>
                        <ul x-show="rekapOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                            <li>
                                <a href="{{ route('pengguna.rekap.puskesmas') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.rekap.puskesmas*') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-building me-2"></i> Rekap Puskesmas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.laporan.status_ptm') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.laporan.status_ptm') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-heart-pulse me-2"></i> Rekap Skrining PTM
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.laporan.kelompok_usia') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.laporan.kelompok_usia') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-person-lines-fill me-2"></i> PTM Kelompok Usia
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pengguna.laporan.kegiatan') }}"
                                    class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('pengguna.laporan.kegiatan') ? 'bg-green-500 text-white' : '' }}">
                                    <i class="bi bi-calendar-event me-2"></i> Laporan Kegiatan PTM
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- MENU BARU: EVALUASI SISTEM (SUS) --}}
                    <li>
                        <a href="{{ route('pengguna.evaluasi.form') }}"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 {{ request()->routeIs('evaluasi.form') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-ui-checks text-lg"></i>
                            <span>Evaluasi Sistem</span>
                        </a>
                    </li>
                @endif


                {{-- ========================================================== --}}
                {{-- ROLE: KEPALA P2PTM (Telah disesuaikan menjadi Sub-Menu) --}}
                {{-- ========================================================== --}}
            @if(Auth::user()->role_name === 'kepala_p2ptm')
                <li x-data="{ laporanKepalaOpen: {{ request()->routeIs('kepala.laporan.*') ? 'true' : 'false' }} }">
                    <button @click="laporanKepalaOpen = !laporanKepalaOpen"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                        <span class="flex items-center gap-3"><i class="bi bi-folder-symlink text-lg"></i> Laporan</span>
                        <i :class="laporanKepalaOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                    </button>

                    <ul x-show="laporanKepalaOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden text-sm">
                        {{-- Fitur Existing --}}
                        <li>
                            <a href="{{ route('kepala.laporan.peserta') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.peserta*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-people me-2"></i> Laporan Peserta
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('kepala.laporan.deteksi_dini') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.deteksi_dini*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-activity me-2"></i> Laporan Deteksi Dini PTM
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('kepala.laporan.faktor_risiko') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.faktor_risiko*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-exclamation-triangle me-2"></i> Laporan Faktor Resiko PTM
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('kepala.laporan.tindak_lanjut') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.tindak_lanjut*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-arrow-right-circle me-2"></i> Laporan Tindak Lanjut
                            </a>
                        </li>

                        {{-- GABUNGAN 4 LAPORAN BARU: Menjadi Satu Pintu --}}
                        <li>
                            <a href="{{ route('kepala.laporan.eksekutif') }}"
                                class="block px-4 py-2 rounded-md hover:bg-green-500 {{ request()->routeIs('kepala.laporan.eksekutif*') ? 'bg-green-500 text-white' : '' }}">
                                <i class="bi bi-clipboard-data-fill me-2"></i> Rekapitulasi & Kegiatan
                            </a>
                        </li>
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
                            <i class="bi bi-box-arrow-right text-lg"></i><span>Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </aside>
</div>