<!-- Sidebar Wrapper -->
<div x-data="{ sidebarOpen: false, masterOpen: false, pegawaiOpen: false, verifikasiOpen: false }" class="relative">
    <!-- Tombol Toggle (mobile) -->
    <button @click="sidebarOpen = !sidebarOpen"
        class="md:hidden fixed top-4 left-4 z-50 bg-green-600 text-white p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        <i :class="sidebarOpen ? 'bi bi-x-lg' : 'bi bi-list'" class="text-xl"></i>
    </button>

    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden">
    </div>

    <!-- Sidebar -->
    <aside
        class="fixed top-0 left-0 h-screen w-64 bg-gray-800 text-white flex flex-col z-40 transform transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <!-- Logo -->
        <div class="flex flex-col items-center justify-center pt-6 pb-6 border-b border-gray-700">
            <img src="{{ asset('images/dinkes.png') }}" alt="Logo" class="mb-3 max-h-20">
            <h6 class="text-white text-md font-extrabold text-center leading-tight">
                Aplikasi Pelaporan<br>Penyakit Tidak Menular
            </h6>
        </div>

        <!-- Navigasi -->
        <nav class="flex-1 overflow-y-auto mt-3 px-2">
            <ul class="flex flex-col gap-1 text-sm">

                <!-- ======================= -->
                <!-- DASHBOARD (Admin/Petugas) -->
                <!-- ======================= -->
                @if(Auth::user()->role_name === 'admin')
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200
                                {{ request()->routeIs('admin.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-house-fill text-lg"></i>
                            <span>Dashboard Admin</span>
                        </a>
                    </li>

                @elseif(Auth::user()->role_name === 'petugas')
                    <li>
                        <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200
                                {{ request()->routeIs('petugas.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-house-fill text-lg"></i>
                            <span>Dashboard Petugas</span>
                        </a>
                    </li>
                @endif

                <!-- ======================= -->
                <!-- DASHBOARD PENGGUNA (Dinkes) -->
                <!-- ======================= -->
                @if(Auth::user()->role_name === 'pengguna')
                    <li>
                        <a href="{{ route('pengguna.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200
                                {{ request()->routeIs('pengguna.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="bi bi-speedometer2 text-lg"></i>
                            <span>Dashboard Pengguna</span>
                        </a>
                    </li>
                @endif

                <!-- =========================== -->
                <!-- MENU ADMIN: MASTER PEGAWAI -->
                <!-- =========================== -->
                @if(Auth::user()->role_name === 'admin')
                                    <li
                                        <li
                    x-data="{ 
                        pegawaiOpen: {{ 
                            request()->routeIs('admin.data_petugas.*') ||
                        request()->routeIs('admin.pengguna.*') ||
                        request()->routeIs('admin.data_puskesmas.*') ||
                        request()->routeIs('admin.reset.*')
                        ? 'true' : 'false' 
                        }} 
                    }">

                                        <button @click="pegawaiOpen = !pegawaiOpen"
                                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                                            <span class="flex items-center gap-3">
                                                <i class="bi bi-folder2-open text-lg"></i> Master Pegawai
                                            </span>
                                            <i :class="pegawaiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                                        </button>

                                        <ul x-show="pegawaiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                                            <li>
                                                <a href="{{ route('admin.data_petugas.index') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                                        {{ request()->routeIs('admin.data_petugas.*') ? 'bg-green-500 text-white' : '' }}">
                                                    <i class="bi bi-people me-2"></i> Data Petugas
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('admin.pengguna.index') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                                   {{ request()->routeIs('admin.pengguna.*') ? 'bg-green-500 text-white' : '' }}">
                                                    <i class="bi bi-person-vcard me-2"></i> Data Pengguna (Dinkes)
                                                </a>
                                            </li>

                                        <li>
                                            <a href="{{ route('admin.data_puskesmas.index') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                                      {{ request()->routeIs('admin.data_puskesmas.*') ? 'bg-green-500 text-white' : '' }}">
                                                <i class="bi bi-building me-2"></i> Data Puskesmas
                                            </a>

                                        </li>

                                        <li>
                                            <a href="{{ route('admin.reset.requests') }}"
                                                class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600">
                                                <i class="bi bi-key-fill"></i>
                                                <span>Reset Password</span>
                                            </a>
                                        </li>
                                        </ul>
                                    </li>
                @endif

                    <!-- ======================================= -->
                    <!-- MENU PETUGAS: MASTER DATA -->
                    <!-- (HANYA DATA PESERTA) -->
                    <!-- ======================================= -->
                    @if(in_array(Auth::user()->role_name, ['admin', 'petugas']))
                                <li x-data="{ 
                                            masterOpen: {{ 
                            request()->routeIs('petugas.pasien.*') ? 'true' : 'false' 
                        }} 
                                        }">

                                    <button @click="masterOpen = !masterOpen"
                                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                                        <span class="flex items-center gap-3">
                                            <i class="bi bi-folder-fill text-lg"></i> Master Data
                                        </span>
                                        <i :class="masterOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                                    </button>

                                    <ul x-show="masterOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">
                                        <li>
                                            <a href="{{ route('petugas.pasien.index') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                                    {{ request()->routeIs('petugas.pasien.*') ? 'bg-green-500 text-white' : '' }}">
                                                <i class="bi bi-person-lines-fill me-2"></i> Data Peserta
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                    @endif
                    
                    
                    <!-- ======================================= -->
                    <!-- MENU PETUGAS: PEMERIKSAAN PTM -->
                    <!-- ======================================= -->
                    @if(in_array(Auth::user()->role_name, ['admin', 'petugas']))
                                                                    <li x-data="{ 
                                                                                pemeriksaanOpen: {{ 
                                                                request()->routeIs('petugas.deteksi_dini.*') ||
        request()->is('petugas/faktor_resiko*')
        ? 'true' : 'false' 
                                                            }} 
                                                                            }">

                                                                        <button @click="pemeriksaanOpen = !pemeriksaanOpen"
                                                                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                                                                            <span class="flex items-center gap-3">
                                                                                <i class="bi bi-clipboard-pulse text-lg"></i> Pemeriksaan PTM
                                                                            </span>
                                                                            <i :class="pemeriksaanOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                                                                        </button>

                                                                        <ul x-show="pemeriksaanOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">

                                                                            <li>
                                                                                <a href="{{ url('/petugas/faktor_resiko') }}"
                                                                                    class="block px-4 py-2 rounded-md hover:bg-green-500
                                                                                                                                                    {{ request()->is('petugas/faktor_resiko*') ? 'bg-green-500 text-white' : '' }}">
                                                                                    <i class="bi bi-activity me-2"></i> Faktor Risiko
                                                                                </a>
                                                                            </li>

                                                                            <li>
                                                                                <a href="{{ route('petugas.deteksi_dini.index') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                                                                        {{ request()->routeIs('petugas.deteksi_dini.*') ? 'bg-green-500 text-white' : '' }}">
                                                                                    <i class="bi bi-clipboard-check me-2"></i> Deteksi Dini PTM
                                                                                </a>
                                                                            </li>


                                                                            <li>
    <a href="{{ route('petugas.tindak_lanjut.index') }}"
        class="block px-4 py-2 rounded-md hover:bg-green-500
        {{ request()->routeIs('petugas.tindak_lanjut.*') ? 'bg-green-500 text-white' : '' }}">
        <i class="bi bi-clipboard-check me-2"></i> Tindak Lanjut PTM
    </a>
</li>

                                                                        </ul>
                                                                    </li>
                    @endif


                <!-- ============================ -->
                <!-- MENU PENGGUNA: VERIFIKASI -->
                <!-- ============================ -->
                @if(Auth::user()->role_name === 'pengguna')
                                                    <li x-data="{
                                                            verifikasiOpen: {{ 
                                                                request()->routeIs('pengguna.verifikasi.*')
        ? 'true' : 'false' 
                                                            }} 
                                                        }">

                                                        <button @click="verifikasiOpen = !verifikasiOpen"
                                                            class="w-full flex items-center justify-between px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200">
                                                            <span class="flex items-center gap-3">
                                                                <i class="bi bi-check2-square text-lg"></i> Verifikasi Data
                                                            </span>
                                                            <i :class="verifikasiOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                                                        </button>
                                                        

                                                        <ul x-show="verifikasiOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">

                                                            <li>
                                                                <a href="{{ route('pengguna.verifikasi.pasien') }}"
                                                                    class="block px-4 py-2 rounded-md hover:bg-green-500
                                                                        {{ request()->routeIs('pengguna.verifikasi.pasien') ? 'bg-green-500 text-white' : '' }}">
                                                                    <i class="bi bi-person-lines-fill me-2"></i> Verifikasi Peserta
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="{{ route('pengguna.verifikasi.deteksi') }}"
                                                                    class="block px-4 py-2 rounded-md hover:bg-green-500
                                                                        {{ request()->routeIs('pengguna.verifikasi.deteksi') ? 'bg-green-500 text-white' : '' }}">
                                                                    <i class="bi bi-clipboard-check me-2"></i> Verifikasi Deteksi Dini
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a href="{{ route('pengguna.verifikasi.faktor') }}"
                                                                    class="block px-4 py-2 rounded-md hover:bg-green-500
                                                                        {{ request()->routeIs('pengguna.verifikasi.faktor') ? 'bg-green-500 text-white' : '' }}">
                                                                    <i class="bi bi-activity me-2"></i> Verifikasi Faktor Risiko
                                                                </a>
                                                            </li>


                                                        </ul>
                                                    </li>
                @endif
                <!-- ============================ -->
                <!-- MENU PENGGUNA: REKAP LAPORAN -->
                <!-- ============================ -->
                @if(Auth::user()->role_name === 'pengguna')
                            <li x-data="{
                                        rekapOpen: {{ request()->routeIs('pengguna.rekap.*') ? 'true' : 'false' }}
                                    }">

                                <!-- BUTTON REKAP -->
                                <button @click="rekapOpen = !rekapOpen" class="w-full flex items-center justify-between px-4 py-2 rounded-lg
                                               hover:bg-green-600 transition-colors duration-200">
                                    <span class="flex items-center gap-3">
                                        <i class="bi bi-bar-chart-fill text-lg"></i>
                                        <span>Rekap Laporan</span>
                                    </span>
                                    <i :class="rekapOpen ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"></i>
                                </button>

                                <!-- SUB MENU -->
                                <ul x-show="rekapOpen" x-transition class="ml-6 mt-1 flex flex-col gap-1 overflow-hidden">

                                    <li>
                                        <a href="{{ route('pengguna.rekap.puskesmas') }}" class="block px-4 py-2 rounded-md hover:bg-green-500
                                               {{ request()->routeIs('pengguna.rekap.puskesmas*')
        ? 'bg-green-500 text-white'
        : '' }}">
                                            <i class="bi bi-building me-2"></i>
                                            Rekap Puskesmas
                                        </a>
                                    </li>

                                </ul>
                            </li>
                @endif


                <!-- TOMOLOL LAPORAN (ADMIN & PETUGAS) -->
                @if(in_array(Auth::user()->role_name, ['admin']))
                    <li>
                        <a href="{{ route(Auth::user()->role_name . '.laporan.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-200
                            {{ request()->routeIs(Auth::user()->role_name . '.laporan.*') ? 'bg-green-600 text-white' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                @endif


                <!-- Logout -->
                <li class="mt-3 border-t border-gray-700 pt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                            <i class="bi bi-box-arrow-right text-lg"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </aside>
</div>