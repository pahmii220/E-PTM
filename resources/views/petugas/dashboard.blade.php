@extends('layouts.master')

@section('title', 'Dashboard Petugas')

@section('content')
    <div class="flex flex-col md:flex-row">
        <main class="flex-1 p-6">

            {{-- Header --}}
            <h2 class="text-xl font-bold text-green-600 mb-1">
                Dashboard Petugas Puskesmas
            </h2>

            <p class="text-gray-600 text-sm mb-6">
                Selamat datang,
                <span class="font-semibold">
                    {{ Auth::user()->Nama_Lengkap }}
                </span>
            </p>

            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

                <div
                    class="bg-gradient-to-r from-indigo-400 to-indigo-600 text-white rounded-xl shadow p-4 flex justify-between items-center">
                    <div>
                        <div class="text-xs uppercase tracking-wide opacity-90">Deteksi Dini</div>
                        <div class="text-2xl font-bold">{{ $totalDeteksi }}</div>
                    </div>
                    <i class="bi bi-activity text-3xl opacity-80"></i>
                </div>

                <div
                    class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-xl shadow p-4 flex justify-between items-center">
                    <div>
                        <div class="text-xs uppercase tracking-wide opacity-90">Faktor Risiko</div>
                        <div class="text-2xl font-bold">{{ $totalFaktor }}</div>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill text-3xl opacity-80"></i>
                </div>

                <div
                    class="bg-gradient-to-r from-red-400 to-red-600 text-white rounded-xl shadow p-4 flex justify-between items-center">
                    <div>
                        <div class="text-xs uppercase tracking-wide opacity-90">Risiko Tinggi</div>
                        <div class="text-2xl font-bold">{{ $highRiskCount }}</div>
                    </div>
                    <i class="bi bi-heart-pulse-fill text-3xl opacity-80"></i>
                </div>
            </div>

            {{-- Peserta & Grafik --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Total Peserta --}}
                <div
                    class="bg-emerald-700 text-white rounded-xl shadow px-5 py-4 flex flex-col justify-center items-center min-h-[150px]">
                    <div class="text-5xl font-extrabold">
                        {{ number_format($totalPeserta) }}
                    </div>
                    <div class="text-sm uppercase tracking-wider opacity-90 mt-1">
                        Peserta
                    </div>
                </div>

                {{-- Grafik --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">
                            Tren Peserta
                        </h4>

                        <select id="filterRange" class="text-xs border rounded px-2 py-1">
                            <option value="monthly">Bulanan</option>
                            <option value="weekly">Mingguan</option>
                            <option value="daily">Harian</option>
                        </select>
                    </div>

                    <div class="relative" style="height:240px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const chartData = {
                monthly: {
                    labels: {!! json_encode($monthLabels) !!},
                    data: {!! json_encode($monthTotals) !!}
                },
                weekly: {
                    labels: {!! json_encode($weeklyLabels) !!},
                    data: {!! json_encode($weeklyTotals) !!}
                },
                daily: {
                    labels: {!! json_encode($dailyLabels) !!},
                    data: {!! json_encode($dailyTotals) !!}
                }
            };

            const ctx = document.getElementById('trendChart').getContext('2d');

            const trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.monthly.labels,
                    datasets: [{
                        data: chartData.monthly.data,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15,118,110,0.15)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            document.getElementById('filterRange').addEventListener('change', function () {
                const key = this.value;
                trendChart.data.labels = chartData[key].labels;
                trendChart.data.datasets[0].data = chartData[key].data;
                trendChart.update();
            });
        });
    </script>
@endpush