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

                <div class="flex items-center gap-2 text-sm font-medium text-slate-500 bg-white px-4 py-2 rounded-xl shadow-sm ring-1 ring-black/5">
                    <i class="bi bi-calendar3 text-indigo-500"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            {{-- ================= STATISTIK UTAMA (GRID) ================= --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                {{-- Deteksi Dini --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-indigo-50 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Deteksi Dini</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $totalDeteksi ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-activity text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Faktor Risiko --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-amber-50 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Faktor Risiko</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $totalFaktor ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Risiko Tinggi --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-rose-50 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Risiko Tinggi</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ $highRiskCount ?? 0 }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-md shadow-rose-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-heart-pulse-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Total Peserta --}}
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden ring-1 ring-black/5 flex flex-col justify-between h-[140px] animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-emerald-50 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative flex justify-between items-start z-10">
                        <div>
                            <div class="text-slate-400 text-sm font-semibold mb-1 uppercase tracking-wider">Total Peserta</div>
                            <div class="text-4xl font-black text-slate-800 tracking-tight">{{ number_format($totalPeserta ?? 0) }}</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-200 group-hover:-translate-y-1 transition-transform">
                            <i class="bi bi-people-fill text-2xl"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= GRAFIK TREN PTM ================= --}}
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-md ring-1 ring-black/5 p-6 md:p-8 mb-10 transition-all duration-300 animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <div>
                        <h4 class="text-xl font-bold text-slate-800">Tren Kasus Penyakit Tidak Menular</h4>
                        <p class="text-sm text-slate-500 mt-1">Grafik perkembangan input data kesehatan peserta</p>
                    </div>
                    <div class="relative">
                        <select id="filterRange" class="appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 pl-4 pr-10 cursor-pointer hover:bg-slate-100 transition-colors outline-none shadow-sm">
                            <option value="monthly">Data Bulanan</option>
                            <option value="weekly">Data Mingguan</option>
                            <option value="daily">Data Harian</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="relative w-full h-[350px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            {{-- ================= GRAFIK ANALITIK (GRID 2) ================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                {{-- KIRI: Grafik Kegiatan & Peserta --}}
                <div class="bg-white rounded-3xl shadow-sm hover:shadow-md ring-1 ring-black/5 p-6 md:p-8 transition-all duration-300 flex flex-col animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-slate-800">Kegiatan PTM Puskesmas Anda</h4>
                        <p class="text-sm text-slate-500 mt-1">Distribusi jenis kegiatan yang telah dilaksanakan</p>
                    </div>

                    {{-- Kanvas Chart --}}
                    <div class="relative w-full h-[250px] flex justify-center items-center">
                        @if(!empty($kegiatanTotals) && collect($kegiatanTotals)->sum() > 0)
                            <canvas id="kegiatanChart"></canvas>
                        @else
                            <div class="text-center bg-slate-50 rounded-2xl p-8 border border-slate-100 w-full">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
                                    <i class="bi bi-clipboard-x text-3xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada data kegiatan di Puskesmas Anda</p>
                            </div>
                        @endif
                    </div>

                    {{-- Info Peserta --}}
                    @if(!empty($kegiatanPeserta) && collect($kegiatanPeserta)->sum() > 0)
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <h5 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Total Kehadiran Peserta</h5>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($kegiatanLabels as $index => $label)
                                    <div class="bg-slate-50 hover:bg-indigo-50/50 rounded-2xl p-4 border border-slate-100 transition-colors text-center group">
                                        <span class="block text-xs font-semibold text-slate-500 group-hover:text-indigo-600 mb-2 truncate" title="{{ $label }}">{{ $label }}</span>
                                        <div class="flex items-baseline justify-center gap-1">
                                            <span class="text-2xl font-black text-slate-800">{{ $kegiatanPeserta[$index] ?? 0 }}</span>
                                            <span class="text-xs font-medium text-slate-500">Org</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- KANAN: Grafik Faktor Risiko --}}
                <div class="bg-white rounded-3xl shadow-sm hover:shadow-md ring-1 ring-black/5 p-6 md:p-8 transition-all duration-300 flex flex-col animate-fade-in-up" style="animation-delay: 0.7s;">
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-slate-800">Distribusi Faktor Risiko</h4>
                        <p class="text-sm text-slate-500 mt-1">Proporsi indikasi risiko dari peserta yang di-skrining</p>
                    </div>

                    <div class="relative w-full h-[320px] flex justify-center items-center my-auto">
                        @if(!empty($faktorTotals) && collect($faktorTotals)->sum() > 0)
                            <canvas id="faktorChart"></canvas>
                        @else
                            <div class="text-center bg-slate-50 rounded-2xl p-8 border border-slate-100 w-full h-[250px] flex flex-col items-center justify-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white shadow-sm mb-4">
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

        body { font-family: 'Inter', sans-serif; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; 
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
                monthly: { labels: {!! json_encode($monthLabels ?? []) !!}, data: {!! json_encode($monthTotals ?? []) !!} },
                weekly: { labels: {!! json_encode($weeklyLabels ?? []) !!}, data: {!! json_encode($weeklyTotals ?? []) !!} },
                daily: { labels: {!! json_encode($dailyLabels ?? []) !!}, data: {!! json_encode($dailyTotals ?? []) !!} }
            };

            // =========================
            // 1. GRAFIK TREN PTM (Smooth Area Chart)
            // =========================
            const trendCtx = document.getElementById('trendChart');
            if(trendCtx) {
                const ctx = trendCtx.getContext('2d');
                let gradientFill = ctx.createLinearGradient(0, 0, 0, 350);
                gradientFill.addColorStop(0, 'rgba(99, 102, 241, 0.3)'); // indigo-500 transparent
                gradientFill.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

                const trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.monthly.labels,
                        datasets: [{
                            label: 'Jumlah Pasien',
                            data: chartData.monthly.data,
                            borderColor: '#6366f1', // indigo-500
                            backgroundColor: gradientFill,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4, // Smooth curve
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#6366f1',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                border: { display: false },
                                ticks: { padding: 10, precision: 0 }
                            },
                            x: { 
                                border: { display: false }, 
                                grid: { display: false },
                                ticks: { padding: 10 }
                            }
                        }
                    }
                });

                document.getElementById('filterRange').addEventListener('change', function () {
                    const key = this.value;
                    trendChart.data.labels = chartData[key].labels;
                    trendChart.data.datasets[0].data = chartData[key].data;
                    trendChart.update();
                });
            }

            // =========================
            // 2. GRAFIK JUMLAH KEGIATAN (Aesthetic Bar Chart)
            // =========================
            const ctxKegiatan = document.getElementById('kegiatanChart');
            if (ctxKegiatan) {
                new Chart(ctxKegiatan, {
                    type: 'bar', 
                    data: {
                        labels: {!! json_encode($kegiatanLabels ?? []) !!},
                        datasets: [{
                            label: 'Jumlah Kegiatan',
                            data: {!! json_encode($kegiatanTotals ?? []) !!},
                            backgroundColor: '#6366f1', // Indigo-500
                            hoverBackgroundColor: '#4f46e5', // Indigo-600
                            borderRadius: 8, 
                            borderSkipped: false,
                            maxBarThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: '#f8fafc', drawTicks: false }, // Sangat tipis
                                ticks: { precision: 0, stepSize: 1, padding: 10 }
                            },
                            x: {
                                border: { display: false },
                                grid: { display: false }, 
                                ticks: { padding: 10, font: { weight: '500' } }
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