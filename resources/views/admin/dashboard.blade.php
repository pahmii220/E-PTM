@extends('layouts.master')

@section('title', 'Dashboard Admin — Soft Gradient')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">

        <!-- ================= HEADER ================= -->
        <header class="px-4 py-6 bg-gradient-to-r from-green-50 via-white to-blue-50 border-b border-gray-200 text-center">
            <h2 class="text-3xl font-extrabold text-gray-800 flex justify-center items-center gap-3">
                <i class="bi bi-speedometer2 text-green-600"></i>
                Dashboard Admin
            </h2>

            <p class="text-gray-600 text-base leading-relaxed max-w-3xl mx-auto mt-3">
                Selamat datang kembali <span class="font-semibold text-green-600">Admin</span> di
                <span class="font-medium">Sistem Pelaporan Penyakit Tidak Menular (PTM)</span>.
                Pantau data, aktivitas petugas, dan tren laporan secara ringkas dan real-time.
            </p>

            <p class="text-sm text-gray-500 mt-3">
                <i class="bi bi-calendar-event"></i>
                {{ now()->format('d F Y') }}
            </p>
        </header>

        <!-- ================= MAIN ================= -->
        <main class="p-6 lg:p-8 space-y-8">

            <!-- ===== STATISTIK ===== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Total Pengguna -->
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Total Pengguna</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-1">
                                {{ $totalPengguna ?? 0 }}
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-1">Terdaftar di sistem</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-lg bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <i class="bi bi-people-fill text-xl text-green-700"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Petugas -->
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Petugas Aktif</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-1">
                                {{ $totalPetugas ?? 0 }}
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-1">Puskesmas & Dinkes</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-lg bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center">
                            <i class="bi bi-person-badge-fill text-xl text-yellow-700"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Pasien -->
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Data Peserta</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-1">
                                {{ $totalPasien?? 0 }}  
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-1">Kasus tercatat</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-lg bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
                            <i class="bi bi-heart-pulse-fill text-xl text-red-700"></i>
                        </div>
                    </div>
                </div>

                <!-- Deteksi Dini -->
                <div class="bg-white border border-gray-100 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Deteksi Dini</p>
                            <h3 class="text-2xl font-extrabold text-gray-800 mt-1">
                                {{ $totalDeteksi ?? 0 }}
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-1">Data masuk</p>
                        </div>
                        <div
                            class="w-11 h-11 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                            <i class="bi bi-clipboard-data-fill text-xl text-blue-700"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ===== GRAFIK ===== -->
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="bi bi-graph-up-arrow text-green-600"></i>
                        Tren Kasus PTM
                    </h3>
                    <span class="text-xs text-gray-400">6 bulan terakhir</span>
                </div>

                <div class="relative h-[300px]">
                    <canvas id="dashboardChart"></canvas>
                </div>
            </div>

        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
document.addEventListener("DOMContentLoaded", () => {
    const canvas = document.getElementById('dashboardChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16,185,129,0.25)');
    gradient.addColorStop(1, 'rgba(16,185,129,0.05)');

    new Chart(ctx, {
        type: 'line',
        data: {
            // ⬇️ DATA ASLI DARI DATABASE
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Kasus PTM',
                data: {!! json_encode($data) !!},
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 3,
                pointRadius: 4,
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5 }
                }
            }
        }
    });
});
</script>

@endpush