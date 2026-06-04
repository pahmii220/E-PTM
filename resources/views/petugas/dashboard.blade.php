@extends('layouts.master')

@section('title', 'Dashboard Petugas')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen bg-slate-50/50">
        <main class="flex-1 p-6 lg:p-8">

            {{-- ================= ALERT PROFIL BELUM LENGKAP ================= --}}
            @if(auth()->check() && !auth()->user()->profilPetugasLengkap())
                <div id="alert-profil"
                    class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-2xl shadow-sm mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 animate-fade-in-up">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 text-amber-500 text-3xl">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-amber-800 text-base font-bold mb-0">Profil Anda Belum Lengkap!</h3>
                            <p class="text-amber-700 text-sm mt-1 mb-0">Silakan lengkapi profil, kontak, dan identitas Anda
                                untuk memaksimalkan penggunaan sistem.</p>
                        </div>
                    </div>

                    {{-- PENTING: Sesuaikan href di bawah ini dengan Route halaman Edit Profil Petugas Anda --}}
                <a href="{{ route('petugas.profil', auth()->id()) }}"
                    class="inline-block bg-amber-400 hover:bg-amber-500 text-amber-900 text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors whitespace-nowrap">
                    Lengkapi Sekarang
                </a>
                </div>
            @endif
            {{-- =============================================================== --}}

            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">
                        Dashboard Analitik PTM
                    </h2>
                    <p class="text-slate-500 text-sm">
                        Selamat datang kembali,
                        <span class="font-semibold text-slate-700">
                            {{ Auth::user()->Nama_Lengkap }}
                        </span> 👋
                    </p>
                </div>
            </div>

            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                {{-- Deteksi Dini --}}
                <div
                    class="group bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-indigo-300 relative overflow-hidden">
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative flex justify-between items-center">
                        <div>
                            <div class="text-indigo-100 text-sm font-medium mb-1">Deteksi Dini</div>
                            <div class="text-3xl font-bold text-white">{{ $totalDeteksi ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="bi bi-activity text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- Faktor Risiko --}}
                <div
                    class="group bg-gradient-to-br from-amber-400 to-amber-500 rounded-2xl p-6 shadow-lg shadow-amber-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-amber-300 relative overflow-hidden">
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative flex justify-between items-center">
                        <div>
                            <div class="text-amber-50 text-sm font-medium mb-1">Faktor Risiko</div>
                            <div class="text-3xl font-bold text-white">{{ $totalFaktor ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="bi bi-exclamation-triangle-fill text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- Risiko Tinggi --}}
                <div
                    class="group bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-6 shadow-lg shadow-rose-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-rose-300 relative overflow-hidden">
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative flex justify-between items-center">
                        <div>
                            <div class="text-rose-100 text-sm font-medium mb-1">Risiko Tinggi</div>
                            <div class="text-3xl font-bold text-white">{{ $highRiskCount ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="bi bi-heart-pulse-fill text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Peserta --}}
                <div
                    class="group bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 shadow-lg shadow-emerald-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-emerald-300 relative overflow-hidden">
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative flex justify-between items-center">
                        <div>
                            <div class="text-emerald-100 text-sm font-medium mb-1">Total Peserta</div>
                            <div class="text-3xl font-bold text-white">{{ number_format($totalPeserta ?? 0) }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                            <i class="bi bi-people-fill text-2xl text-white"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Grafik Tren PTM --}}
            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8 transition-all duration-300 hover:shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-lg font-bold text-slate-800">
                        Tren Kasus PTM
                    </h4>
                    <select id="filterRange"
                        class="bg-slate-50 border-none text-slate-600 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 py-2 px-4 cursor-pointer transition-colors hover:bg-slate-100 outline-none">
                        <option value="monthly">Bulanan</option>
                        <option value="weekly">Mingguan</option>
                        <option value="daily">Harian</option>
                    </select>
                </div>
                <div class="relative w-full h-[350px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            {{-- Grafik Analitik --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Kasus per Puskesmas --}}
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all duration-300 hover:shadow-md">
                    <h4 class="text-lg font-bold text-slate-800 mb-6">
                        Kasus PTM per Puskesmas
                    </h4>
                    <div class="relative w-full h-[300px]">
                        <canvas id="puskesmasChart"></canvas>
                    </div>
                </div>

                {{-- Faktor Risiko --}}
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 transition-all duration-300 hover:shadow-md">
                    <h4 class="text-lg font-bold text-slate-800 mb-6">
                        Distribusi Faktor Risiko
                    </h4>
                    <div class="relative w-full h-[300px] flex justify-center">
                        <canvas id="faktorChart"></canvas>
                    </div>
                </div>

            </div>

        </main>
    </div>
@endsection

@push('styles')
    <style>
        /* Animasi sederhana untuk render awal yang smooth */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Konfigurasi Global Chart.js untuk tampilan aesthetic
            Chart.defaults.font.family = "'Inter', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
            Chart.defaults.color = '#64748b'; // slate-500
            Chart.defaults.scale.grid.color = '#f1f5f9'; // slate-100

            const chartData = {
                monthly: {
                    labels: {!! json_encode($monthLabels ?? []) !!},
                    data: {!! json_encode($monthTotals ?? []) !!}
                },
                weekly: {
                    labels: {!! json_encode($weeklyLabels ?? []) !!},
                    data: {!! json_encode($weeklyTotals ?? []) !!}
                },
                daily: {
                    labels: {!! json_encode($dailyLabels ?? []) !!},
                    data: {!! json_encode($dailyTotals ?? []) !!}
                }
            };

            // =========================
            // GRAFIK TREN PTM (Line Chart Gradient)
            // =========================
            const trendCtx = document.getElementById('trendChart').getContext('2d');

            // Membuat Gradient warna untuk background Line Chart
            let gradientFill = trendCtx.createLinearGradient(0, 0, 0, 350);
            gradientFill.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // indigo-500 tipis
            gradientFill.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: chartData.monthly.labels,
                    datasets: [{
                        label: 'Kasus PTM',
                        data: chartData.monthly.data,
                        borderColor: '#6366f1', // indigo-500
                        backgroundColor: gradientFill,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4, // Membuat garis melengkung (smooth)
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)', // slate-900
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 14, weight: 'bold' },
                            displayColors: false,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false } // Hilangkan garis tebal sumbu Y
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false } // Hilangkan grid vertical agar bersih
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                }
            });

            // FILTER RANGE
            document.getElementById('filterRange').addEventListener('change', function () {
                const key = this.value;
                trendChart.data.labels = chartData[key].labels;
                trendChart.data.datasets[0].data = chartData[key].data;
                trendChart.update();
            });

            // =========================
            // GRAFIK PUSKESMAS (Bar Chart Rounded)
            // =========================
            new Chart(document.getElementById('puskesmasChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($puskesmasLabels ?? []) !!},
                    datasets: [{
                        label: 'Jumlah Kasus',
                        data: {!! json_encode($puskesmasTotals ?? []) !!},
                        backgroundColor: '#10b981', // emerald-500
                        borderRadius: 6, // Memberikan sudut tumpul pada bar
                        borderSkipped: false,
                        barThickness: 'flex',
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, border: { display: false } },
                        x: { border: { display: false }, grid: { display: false } }
                    }
                }
            });

            // =========================
            // GRAFIK FAKTOR RISIKO (Doughnut Chart Clean)
            // =========================
            new Chart(document.getElementById('faktorChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($faktorLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($faktorTotals ?? []) !!},
                        backgroundColor: [
                            '#ef4444', // rose-500
                            '#f59e0b', // amber-500
                            '#3b82f6', // blue-500
                            '#10b981', // emerald-500
                            '#8b5cf6'  // violet-500
                        ],
                        borderWidth: 0, // Menghapus border putih default agar menyatu
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', // Cincin lebih tipis dan aesthetic
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                        }
                    }
                }
            });
        });
    </script>
@endpush