@extends('layouts.master')

@section('title', 'Dashboard Eksekutif')

@section('content')
    @php 
        use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;
    use App\Models\Puskesmas;
    use App\Models\Peserta;
    use App\Models\User;

    $waktuSekarang = Carbon::now('Asia/Makassar');
    $jam = $waktuSekarang->format('H');

    // Logika Ucapan
    if ($jam >= 5 && $jam < 12) {
        $ucapan = 'Selamat Pagi';
        $ikonUcapan = 'bi-brightness-alt-high-fill text-yellow-300';
    } elseif ($jam >= 12 && $jam < 15) {
        $ucapan = 'Selamat Siang';
        $ikonUcapan = 'bi-sun-fill text-yellow-400';
    } elseif ($jam >= 15 && $jam < 18) {
        $ucapan = 'Selamat Sore';
        $ikonUcapan = 'bi-cloud-sun-fill text-orange-300';
    } else {
        $ucapan = 'Selamat Malam';
        $ikonUcapan = 'bi-moon-stars-fill text-blue-200';
    }

    // Logic Tanggal
    $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

    // --- FILTER BULAN TREND ---
    $filterTrendBulan = request('trend_bulan', $waktuSekarang->format('m'));
    $listBulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $pesertaQuery = Peserta::query();
    $deteksiBaseQuery = \App\Models\DeteksiDiniPTM::query();

    if ($filterTrendBulan !== 'semua') {
        $pesertaQuery->whereMonth('dibuat_pada', $filterTrendBulan);
        $deteksiBaseQuery->whereMonth('tanggal_pemeriksaan', $filterTrendBulan);
    }

    $totalPesertaVal = $pesertaQuery->count();
    $totalDeteksiVal = $deteksiBaseQuery->count();

    // Data Skrining
    $skNormal = (clone $deteksiBaseQuery)->where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai = (clone $deteksiBaseQuery)->where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko = (clone $deteksiBaseQuery)->where(function($q) {
        $q->where('hasil_skrining', 'LIKE', '%Risiko%')
          ->orWhere('hasil_skrining', 'LIKE', '%Resiko%');
    })->count();
    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;

    // --- DATA TAMBAHAN UNTUK PEGAWAI & PUSKESMAS ---
    $totalPetugas = User::where('role_name', 'petugas')->count();
    $totalPegawai = \App\Models\PegawaiDinkes::count();

    // Data untuk Chart Puskesmas
    $puskesmasList = Puskesmas::all();
    $puskesmasLabels = $puskesmasList->pluck('nama_puskesmas')->toArray();
    $puskesmasData = $puskesmasList->map(function ($p) use ($filterTrendBulan) {
        $q = Peserta::where('puskesmas_id', $p->id);
        if ($filterTrendBulan !== 'semua') {
            $q->whereMonth('dibuat_pada', $filterTrendBulan);
        }
        return $q->count();
    })->toArray();

    // Data Peta Sebaran Puskesmas
    $mapPuskesmasQuery = Puskesmas::whereNotNull('latitude')->whereNotNull('longitude');
    $mapPuskesmasData = $mapPuskesmasQuery->withCount([
        'peserta' => function($q) use ($filterTrendBulan) {
            if ($filterTrendBulan !== 'semua') $q->whereMonth('dibuat_pada', $filterTrendBulan);
        },
        'deteksiDini' => function($q) use ($filterTrendBulan) {
            if ($filterTrendBulan !== 'semua') $q->whereMonth('tanggal_pemeriksaan', $filterTrendBulan);
        }
    ])->get();
    @endphp

    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        {{-- ================= SPANDUK SAMBUTAN ================= --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-4 p-3.5 shadow-lg mb-4 relative overflow-hidden text-white d-flex justify-content-between align-items-center">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                <i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i>
            </div>

            <div class="relative z-10">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="bi {{ $ikonUcapan }} fs-2"></i>
                    <h2 class="fw-bold mb-0 tracking-wide">{{ $ucapan }}, Kepala P2PTM!</h2>
                </div>
                <p class="text-blue-100 mb-0 mt-2 fs-5">Berikut adalah ringkasan data Penyakit Tidak Menular (PTM) dan analisis hari ini.</p>
            </div>

            <div class="relative z-10 d-none d-md-block text-end">
                <div class="text-blue-100 small text-uppercase fw-bold opacity-75 mb-1">Tanggal Hari Ini</div>
                <div class="fs-4 fw-bold">{{ $tanggalIndo }}</div>
            </div>
        </div>

        {{-- ================= PERINGATAN DINI LONJAKAN KASUS WILAYAH ================= --}}
        @include('partials.early_warning_card')

        {{-- BARIS 1: STATISTIK UTAMA --}}
        <div class="row g-4 mb-4">
            @php 
                $cards = [
                    ['title' => 'Total Pasien', 'value' => $totalPesertaVal, 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Deteksi Dini', 'value' => $totalDeteksiVal, 'icon' => 'bi-activity', 'color' => 'success'],
                    ['title' => 'Risiko Tinggi', 'value' => $skRisiko, 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                    ['title' => 'Petugas Puskesmas', 'value' => $totalPetugas, 'icon' => 'bi-person-badge-fill', 'color' => 'info'],
                    ['title' => 'Pegawai Dinkes', 'value' => $totalPegawai, 'icon' => 'bi-person-vcard-fill', 'color' => 'warning']
                ];
            @endphp

            @foreach($cards as $card)
            <div class="col-xl col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">{{ $card['title'] }}</p>
                            <h2 class="fw-extrabold mb-0 text-dark">{{ number_format($card['value']) }}</h2>
                        </div>
                        <div class="bg-{{ $card['color'] }}-subtle p-3 rounded-4 text-{{ $card['color'] }}">
                            <i class="bi {{ $card['icon'] }} fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- BARIS 2: ANALISIS GRAFIK --}}
        <div class="row g-4 mb-4">
            {{-- Grafik diganti dari Volume Data menjadi Puskesmas --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-b pb-3 border-gray-100">
                        <div class="d-flex align-items-center">
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-3 me-3"><i class="bi bi-hospital fs-4"></i></div>
                            <h4 class="fw-bold mb-0 text-dark">Sebaran Pasien per Puskesmas</h4>
                        </div>
                        <form method="GET" action="{{ route('kepala.dashboard') }}" class="d-flex align-items-center gap-1 bg-light p-1.5 rounded-xl border border-gray-200">
                            <span class="text-xs fw-semibold text-gray-500 ms-1 me-1 d-none d-sm-inline"><i class="bi bi-funnel-fill text-blue-600 me-1"></i>Filter Bulan:</span>
                            <select name="trend_bulan" class="form-select form-select-sm border-0 bg-transparent fw-bold text-xs text-blue-900" style="min-width: 135px; cursor: pointer;" onchange="this.form.submit()">
                                <option value="semua" {{ $filterTrendBulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                                @foreach($listBulanIndo as $valBulan => $labelBulan)
                                    <option value="{{ $valBulan }}" {{ $filterTrendBulan == $valBulan ? 'selected' : '' }}>
                                        {{ $labelBulan }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="mx-auto" style="height: 250px; width: 100%;">
                        <canvas id="puskesmasChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-b pb-3 border-gray-100">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple-50 text-purple-600 p-2 rounded-3 me-3"><i class="bi bi-pie-chart-fill fs-4"></i></div>
                            <h4 class="fw-bold mb-0 text-dark">Proporsi Skrining</h4>
                        </div>
                        @if($filterTrendBulan !== 'semua' && isset($listBulanIndo[$filterTrendBulan]))
                            <span class="badge bg-purple-50 text-purple-700 fw-bold px-2.5 py-1 rounded-pill text-xs border border-purple-200">
                                ({{ $listBulanIndo[$filterTrendBulan] }})
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
                        @if($totalSkrining == 0)
                            <div class="text-center text-muted py-5"><i class="bi bi-pie-chart fs-1 opacity-25"></i><p class="small">Data belum tersedia</p></div>
                        @else
                            <canvas id="skriningChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        {{-- BARIS 3: PETA SEBARAN, KEPADATAN & CLUSTERING PUSKESMAS --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                @include('partials.peta_sebaran')
            </div>
        </div>

    </div>

    <style>
        .welcome-banner { background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%); }
        .rounded-4 { border-radius: 1.5rem !important; }
        .transition-hover { transition: all 0.3s ease; }
        .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .fw-extrabold { font-weight: 800; }
        .text-white-75 { color: rgba(255,255,255,0.75); }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-purple-50 { background-color: #f5f3ff; }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            Chart.defaults.font.family = "'Segoe UI', sans-serif";
            
            // Fungsi buat gradient vertikal
            function buatGradient(ctx, r, g, b) {
                const grad = ctx.createLinearGradient(0, 0, 0, 320);
                grad.addColorStop(0,   `rgba(${r},${g},${b},0.95)`);
                grad.addColorStop(1,   `rgba(${r},${g},${b},0.35)`);
                return grad;
            }

            const ctxPuskesmas = document.getElementById('puskesmasChart').getContext('2d');
            const labelsPuskesmas = {!! json_encode($puskesmasLabels) !!};

            // Bar Chart: PUSKESMAS
            new Chart(ctxPuskesmas, {
                type: 'bar',
                data: {
                    labels: labelsPuskesmas,
                    datasets: [{
                        label: 'Pasien Terdaftar',
                        data: {!! json_encode($puskesmasData) !!},
                        backgroundColor: buatGradient(ctxPuskesmas, 59, 130, 246),
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 0,
                        borderRadius: { topLeft: 6, topRight: 6 },
                        borderSkipped: false,
                        barThickness: 18,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                borderRadius: 3,
                                useBorderRadius: true,
                                font: { size: 11, weight: '600' },
                                padding: 14,
                                color: '#374151'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.92)',
                            titleColor: '#f9fafb',
                            bodyColor: '#d1d5db',
                            padding: 14,
                            cornerRadius: 12,
                            titleFont: { size: 13, weight: '700' },
                            bodyFont: { size: 12 },
                            bodySpacing: 7,
                            boxPadding: 5,
                            callbacks: {
                                title: function(ctx) {
                                    return '🏥 ' + ctx[0].label;
                                },
                                label: function(ctx) {
                                    const val = ctx.raw.toLocaleString('id-ID');
                                    return `  👥 ${ctx.dataset.label}: ${val} data`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10, weight: '600' },
                                color: '#6b7280',
                                maxRotation: 30,
                                callback: function(val, index) {
                                    const nama = labelsPuskesmas[index];
                                    return nama.length > 14 ? nama.substring(0, 13) + '…' : nama;
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(243,244,246,1)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                precision: 0,
                                font: { size: 11 },
                                color: '#9ca3af',
                                callback: val => val.toLocaleString('id-ID')
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Data',
                                font: { size: 11, style: 'italic' },
                                color: '#9ca3af'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutBounce'
                    }
                }
            });

            // Doughnut Chart: SKRINING
            @if($totalSkrining > 0)
            new Chart(document.getElementById('skriningChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'Dicurigai', 'Risiko Tinggi'],
                    datasets: [{
                        data: [{{ $skNormal }}, {{ $skDicurigai }}, {{ $skRisiko }}],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, cutout: '75%', 
                    plugins: { 
                        legend: { 
                            position: 'bottom', 
                            labels: { usePointStyle: true, padding: 20, font: { size: 14 } } 
                        } 
                    } 
                }
            });
            @endif
        });
    </script>
@endpush