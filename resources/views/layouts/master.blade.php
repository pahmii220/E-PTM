<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Pelaporan PTM | Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f9fafb;
        }

        /* Sidebar */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #1f2937;
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: transform 0.3s ease;
        }

        .app-sidebar nav::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            height: 64px;
            z-index: 50;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 1rem;
        }

        /* Main content */
        .app-main {
            margin-left: 240px;
            margin-top: 64px;
            padding: 20px;
            min-height: calc(100vh - 64px - 60px);
            /* navbar + footer */
            display: flex;
            flex-direction: column;
        }

        /* Footer */
        footer {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width:768px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-main {
                margin-left: 0;
            }

            .navbar {
                left: 0;
            }

            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

    {{-- Sidebar --}}
    @include('sidebar.sidebar')



    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-30 z-30 md:hidden"></div>

    {{-- Navbar --}}
        <nav
            class="bg-white border-b border-gray-200 px-4 py-2 flex justify-end items-center fixed top-0 left-64 right-0 z-50 shadow-sm">
            <div class="relative">
                <!-- Button profil -->
            <button id="profileBtn" class="flex items-center gap-2 focus:outline-none">
    <img src="https://api.dicebear.com/8.x/bottts/svg?seed={{ Auth::user()->username ?? Auth::user()->id }}&backgroundColor=ebfbee"
        alt="Avatar"
        class="rounded-full border-2 border-green-400 shadow-sm w-10 h-10">

    <span class="font-medium text-gray-700">
        {{ ucfirst(Auth::user()->role_name) }}
    </span>

    <i class="bi bi-caret-down-fill text-gray-500"></i>
</button>


        
                <!-- Dropdown -->
                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg overflow-hidden">
                    {{-- MENU PROFIL --}}
@if(Auth::user()->role_name === 'petugas')
    <a href="{{ route('petugas.profil') }}"
       class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
        <i class="bi bi-person-circle text-green-500"></i>
        Profil Petugas
    </a>
@endif

@if(Auth::user()->role_name === 'pengguna')
    <a href="{{ route('pengguna.pegawai_dinkes.edit', Auth::id()) }}"
       class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
        <i class="bi bi-person-circle text-green-500"></i>
        Profil Pegawai Dinkes
    </a>
@endif

{{-- ================= PENGATURAN AKUN ================= --}}
@if(auth()->check() && in_array(auth()->user()->role_name, ['petugas', 'pengguna']))
    <a href="{{ auth()->user()->role_name === 'petugas'
            ? route('petugas.pengaturan')
            : route('pengguna.pengaturan') }}"
       class="block px-4 py-2 flex items-center gap-2 transition
       {{
           auth()->user()->role_name === 'petugas'
               ? (request()->routeIs('petugas.pengaturan*')
                    ? 'bg-gray-100 text-green-600 font-semibold'
                    : 'hover:bg-gray-100 text-gray-700')
               : (request()->routeIs('pengguna.pengaturan*')
                    ? 'bg-gray-100 text-green-600 font-semibold'
                    : 'hover:bg-gray-100 text-gray-700')
       }}">
        <i class="bi bi-gear"></i>
        <span>Pengaturan</span>
    </a>
@endif


                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2 text-red-500">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    
    <script>
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileBtn.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        // Klik di luar untuk menutup dropdown
        window.addEventListener('click', function (e) {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>



    {{-- Main content --}}
        <main class="app-main flex-1 flex flex-col p-6 min-h-[calc(100vh-64px-60px)]">
            {{-- ================= ALERT GLOBAL ================= --}}
            @if(session('success'))
                <div class="container-fluid px-4 mb-3">
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="container-fluid px-4 mb-3">
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif
            {{-- =============== END ALERT GLOBAL =============== --}}

            @yield('content')
        </main>


        <script>
            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                });
            }, 3000);
        </script>


{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#petugasTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            info: true,
            searching: true,
            lengthChange: true,
            scrollX: false,
            order: [[1, 'asc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            }
        });
    });
</script>

<style>
    body {
        background-color: #f8fafc;
    }

    .card {
        overflow: hidden;
        margin-top: -25px !important;
    }

    table {
        width: 100%;
        font-size: 0.9rem;
    }

    table th,
    table td {
        vertical-align: middle !important;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1500px !important;
        }

        table th,
        table td {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 991px) {
        .container-fluid {
            padding: 10px;
        }

        table {
            font-size: 0.85rem;
        }

    }
</style>



    {{-- Footer --}}
    <footer>
        <strong>© 2025 Muhammad Pahmi</strong>
    </footer>
    @stack('scripts')

</body>

</html>