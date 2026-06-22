@extends('layouts.master')

@section('title', 'Dashboard Petugas')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen bg-slate-50/70 font-sans">
        <main class="flex-1 p-6 lg:p-8 xl:p-10 max-w-7xl mx-auto w-full">

            {{-- ================= ALERT PROFIL BELUM LENGKAP ================= --}}
            @if(auth()->check() && !auth()->user()->profilPetugasLengkap())
                <div id="alert-profil"
                    class="bg-white border-l-4 border-amber-500 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 animate-fade-in-up ring-1 ring-black/5">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 text-amber-500 bg-amber-100 p-3 rounded-xl">
                            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-slate-800 text-lg font-bold mb-0">Profil Anda Belum Lengkap!</h3>
                            <p class="text-slate-500 text-sm mt-1 mb-0 leading-relaxed">
                                Silakan lengkapi profil, kontak, dan identitas Anda untuk memaksimalkan penggunaan sistem PTM.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('petugas.profil', auth()->id()) }}"
                        class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all whitespace-nowrap gap-2">
                        Lengkapi Sekarang <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif

            {{-- ================= HEADER ================= --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-2">
                        Dashboard Analitik PTM
                    </h2>
                    <p class="text-slate-500 text-base">
                        Selamat datang kembali,
                        <span class="font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                            {{ Auth::user()->Nama_Lengkap }}
                        </span> 👋
                    </p>
                </div>

                <div
                    class="flex items-center gap-2 text-sm font-medium text-slate-500 bg-white px-4 py-2 rounded-xl shadow-sm ring-1 ring-black/5">
                    <i class="bi bi-calendar3 text-indigo-500"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            {{-- ================= STATISTIK UTAMA (GRID) ================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                {{-- Deteksi Dini --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up"
                    style="animation-delay: 0.1s;">
                    <div
                        class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-indigo-50 opacity-50 group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Deteksi Dini
                            </div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $totalDeteksi ?? 0 }}</div>
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-activity text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Faktor Risiko --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up"
                    style="animation-delay: 0.2s;">
                    <div
                        class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-amber-50 opacity-50 group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Faktor Risiko
                            </div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $totalFaktor ?? 0 }}</div>
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Risiko Tinggi --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up"
                    style="animation-delay: 0.3s;">
                    <div
                        class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-rose-50 opacity-50 group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Risiko Tinggi
                            </div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $highRiskCount ?? 0 }}</div>
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-md shadow-rose-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-heart-pulse-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Peserta --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up"
                    style="animation-delay: 0.4s;">
                    <div
                        class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-emerald-50 opacity-50 group-hover:scale-150 transition-transform duration-700">
                    </div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Total Peserta
                            </div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">
                                {{ number_format($totalPeserta ?? 0) }}
                            </div>
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-people-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= ROW: GRAFIK TREN & FAKTOR RISIKO ================= --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">

                {{-- KIRI: GRAFIK TREN PTM (Mengambil 2/3 Lebar Layar) --}}
                <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm hover:shadow-md ring-1 ring-black/5 p-6 md:p-8 transition-all duration-300 animate-fade-in-up"
                    style="animation-delay: 0.6s;">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                        <div>
                            <h4 class="text-xl font-bold text-slate-800">Tren Kasus Penyakit Tidak Menular</h4>
                            <p class="text-sm text-slate-500 mt-1">Grafik perkembangan input data kesehatan peserta</p>
                        </div>
                        <div class="relative">
                            <select id="filterRange"
                                class="appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-4 pr-10 cursor-pointer hover:bg-slate-100 transition-colors outline-none shadow-sm">
                                <option value="monthly">Data Bulanan</option>
                                <option value="weekly">Data Mingguan</option>
                                <option value="daily">Data Harian</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                <i class="bi bi-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative w-full h-[350px]">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                {{-- KANAN: Grafik Faktor Risiko --}}
                <div class="xl:col-span-1 bg-white rounded-3xl shadow-sm hover:shadow-md ring-1 ring-black/5 p-6 md:p-8 transition-all duration-300 flex flex-col animate-fade-in-up"
                    style="animation-delay: 0.7s;">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-slate-800">Distribusi Faktor Risiko</h4>
                        <p class="text-sm text-slate-500 mt-1">Proporsi indikasi risiko dari peserta yang di-skrining</p>
                    </div>

                    <div class="relative w-full h-[320px] flex justify-center items-center my-auto">
                        @if(!empty($faktorTotals) && collect($faktorTotals)->sum() > 0)
                            <canvas id="faktorChart"></canvas>
                        @else
                            <div
                                class="text-center bg-slate-50 rounded-2xl p-8 border border-slate-100 w-full h-[250px] flex flex-col items-center justify-center">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
                                    <i class="bi bi-pie-chart text-3xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada data faktor risiko yang diinput.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </main>
    </div>
@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Custom Scrollbar untuk Tracking List */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Konfigurasi Global Font & Warna Chart
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8'; // slate-400
            Chart.defaults.scale.grid.color = '#f1f5f9'; // slate-100
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)'; // slate-900
            Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold' };
            Chart.defaults.plugins.tooltip.bodyFont = { size: 14 };
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.displayColors = false;

            const chartData = {
                monthly: {
                    labels: {!! json_encode($monthLabels ?? []) !!},
                    pasien: {!! json_encode($monthPasien ?? []) !!},
                    deteksi: {!! json_encode($monthDeteksi ?? []) !!},
                    faktor: {!! json_encode($monthFaktor ?? []) !!}
                },
                weekly: {
                    labels: {!! json_encode($weeklyLabels ?? []) !!},
                    pasien: {!! json_encode($weeklyPasien ?? []) !!},
                    deteksi: {!! json_encode($weeklyDeteksi ?? []) !!},
                    faktor: {!! json_encode($weeklyFaktor ?? []) !!}
                },
                daily: {
                    labels: {!! json_encode($dailyLabels ?? []) !!},
                    pasien: {!! json_encode($dailyPasien ?? []) !!},
                    deteksi: {!! json_encode($dailyDeteksi ?? []) !!},
                    faktor: {!! json_encode($dailyFaktor ?? []) !!}
                }
            };

            // =========================
            // 1. GRAFIK TREN PTM (Grouped Bar Chart - Aesthetic)
            // =========================
            const trendCtx = document.getElementById('trendChart');
            if (trendCtx) {
                const ctx = trendCtx.getContext('2d');

                const trendChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.monthly.labels,
                        datasets: [
                            {
                                label: 'Faktor Risiko',
                                data: chartData.monthly.faktor,
                                backgroundColor: '#ef4444',
                                hoverBackgroundColor: '#dc2626',
                                borderRadius: 6,
                                borderSkipped: 'bottom',
                                maxBarThickness: 15
                            },
                            {
                                label: 'Deteksi Dini',
                                data: chartData.monthly.deteksi,
                                backgroundColor: '#22c55e',
                                hoverBackgroundColor: '#16a34a',
                                borderRadius: 6,
                                borderSkipped: 'bottom',
                                maxBarThickness: 15
                            },
                            {
                                label: 'Peserta',
                                data: chartData.monthly.pasien,
                                backgroundColor: '#3b82f6',
                                hoverBackgroundColor: '#2563eb',
                                borderRadius: 6,
                                borderSkipped: 'bottom',
                                maxBarThickness: 15
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                alignment: 'center',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: { size: 12, weight: '600' }
                                }
                            }
                        },
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#f1f5f9', drawTicks: false },
                                ticks: { padding: 10, precision: 0 }
                            },
                            x: {
                                border: { display: false },
                                grid: { display: false },
                                ticks: { padding: 10, font: { weight: '600' } }
                            }
                        }
                    }
                });

                // Update data grafik saat dropdown berubah
                document.getElementById('filterRange').addEventListener('change', function () {
                    const key = this.value;
                    trendChart.data.labels = chartData[key].labels;
                    trendChart.data.datasets[0].data = chartData[key].faktor;
                    trendChart.data.datasets[1].data = chartData[key].deteksi;
                    trendChart.data.datasets[2].data = chartData[key].pasien;
                    trendChart.update();
                });
            }

          // =========================
            // 2. GRAFIK JUMLAH KEGIATAN (Modern Column Chart)
            // =========================
            const ctxKegiatan = document.getElementById('kegiatanChart');
            if (ctxKegiatan) {
                const ctx2 = ctxKegiatan.getContext('2d');

                // Gradient vertikal
                let gradientKegiatan = ctx2.createLinearGradient(0, 0, 0, 250);
                gradientKegiatan.addColorStop(0, '#6366f1');
                gradientKegiatan.addColorStop(1, '#c7d2fe');

                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($kegiatanLabels ?? []) !!},
                        datasets: [{
                            label: 'Jumlah Kehadiran',
                            data: {!! json_encode($kegiatanPeserta ?? []) !!},
                            backgroundColor: gradientKegiatan,
                            hoverBackgroundColor: '#4f46e5',
                            borderRadius: 12,
                            borderSkipped: false,
                            maxBarThickness: 55
                        }]
                    },
                    plugins: [{
                        id: 'valueOnBar',
                        afterDatasetsDraw: function (chart, args, options) {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach((dataset, i) => {
                                const meta = chart.getDatasetMeta(i);
                                meta.data.forEach((bar, index) => {
                                    const data = dataset.data[index];
                                    if (data !== undefined && data !== null) {
                                        ctx.fillStyle = '#1e293b'; // slate-800
                                        ctx.font = 'bold 12px "Inter", sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'bottom';
                                        ctx.fillText(data + ' Orang', bar.x, bar.y - 6);
                                    }
                                });
                            });
                        }
                    }],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.95)',
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        return context.raw + ' Orang';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grace: '10%',
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    precision: 0,
                                    padding: 10
                                }
                            },
                            x: {
                                border: {
                                    display: false
                                },
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10,
                                    font: {
                                        weight: '600'
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // =========================
            // 3. GRAFIK FAKTOR RISIKO (Clean Doughnut)
            // =========================
            const ctxFaktor = document.getElementById('faktorChart');
            if (ctxFaktor) {
                new Chart(ctxFaktor, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($faktorLabels ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($faktorTotals ?? []) !!},
                            backgroundColor: [
                                '#f43f5e', // rose-500
                                '#f59e0b', // amber-500
                                '#0ea5e9', // sky-500
                                '#10b981', // emerald-500
                                '#8b5cf6'  // violet-500
                            ],
                            borderWidth: 4,
                            borderColor: '#ffffff', // Jarak putih antar chart
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%', // Ketebalan donut proporsional
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 25,
                                    font: { size: 13, weight: '500' },
                                    color: '#475569' // slate-600
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush