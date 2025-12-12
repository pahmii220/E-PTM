@extends('layouts.master')

@section('title', 'Dashboard Admin — Soft Gradient')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 flex flex-col">

        <!-- Header -->
        <header class="px-2 py-1 bg-gradient-to-r from-green-50 via-white to-blue-50 border-b border-gray-200 text-center">
            <h2 class="text-3xl font-extrabold text-gray-800">
                Dashboard Admin
            </h2>

            <p class="text-gray-600 text-lg leading-relaxed max-w-3xl mx-auto mt-3">
                Selamat datang kembali <span class="font-semibold text-green-600">Admin</span> di Sistem Aplikasi Pelaporan Kasus
                Penyakit Tidak Menular (PTM). Di halaman ini, Anda dapat memantau kinerja pelaporan PTM secara real-time dan
                mendapatkan ringkasan informasi secara terkini dengan cepat dan mudah.
            </p>


            <p class="text-sm text-gray-500 mt-3">
                Tanggal: {{ now()->format('d F Y') }}
            </p>
        </header>

        <!-- Main -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 space-y-8">

            <!-- Statistik cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Card 1 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Total Pengguna</p>
                        <h3 class="text-xl lg:text-2xl font-extrabold text-gray-800 mt-1">{{ $totalPengguna ?? 0 }}</h3>
                        <p class="text-[11px] text-gray-400 mt-1">Seluruh pengguna terdaftar</p>
                    </div>
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-green-100 to-green-200">
                        <i class="bi bi-people-fill text-xl text-green-700"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Jumlah Petugas</p>
                        <h3 class="text-xl lg:text-2xl font-extrabold text-gray-800 mt-1">{{ $totalPetugas ?? 0 }}</h3>
                        <p class="text-[11px] text-gray-400 mt-1">Petugas aktif</p>
                    </div>
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-100 to-yellow-200">
                        <i class="bi bi-person-badge-fill text-xl text-yellow-700"></i>
                    </div>
                </div>
            </div>


        </div>

        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.getElementById('dashboardChart').getContext('2d');

            // Simple, clean gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(59,130,246,0.25)'); // blue-500
            gradient.addColorStop(1, 'rgba(16,185,129,0.05)'); // emerald-400 light

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Kasus PTM',
                        data: [12, 19, 14, 23, 20, 25],
                        borderColor: '#3b82f6',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointBackgroundColor: '#3b82f6',
                        tension: 0.35,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#374151' } }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#6b7280' },
                            grid: { display: false }
                        },
                        y: {
                            ticks: { color: '#6b7280' },
                            grid: { color: '#e6eef9' }
                        }
                    }
                }
            });
        });
    </script>
@endpush