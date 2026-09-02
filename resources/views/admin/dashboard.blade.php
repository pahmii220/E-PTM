@extends('layouts.master')

@section('title', 'Dashboard Admin — PTM Monitor')

@section('content')
            @php 
                        use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;
    use App\Models\Peserta;
    use App\Models\DeteksiDiniPTM;
    use App\Models\User;
    use App\Models\Puskesmas;
    use App\Models\PegawaiDinkes;

    // 1. Mengatur zona waktu ke WITA
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

    // Logika Tanggal Indonesia Manual
    $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

    // =======================================================================
    // 2. DATA KPI & FILTER BULAN
    // =======================================================================
    $filterTrendBulan = request('trend_bulan', $waktuSekarang->format('m'));
    $listBulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $pesertaQuery = Peserta::query();
    $deteksiBaseQuery = DeteksiDiniPTM::query();

    if ($filterTrendBulan !== 'semua') {
        $pesertaQuery->whereMonth('dibuat_pada', $filterTrendBulan);
        $deteksiBaseQuery->whereMonth('tanggal_pemeriksaan', $filterTrendBulan);
    }

    $realTotalPeserta = $pesertaQuery->count();
    $realTotalDeteksi = $deteksiBaseQuery->count();
    $realTotalPegawai = PegawaiDinkes::count();
    $realTotalPetugas = User::where('role_name', 'petugas')->count();

    // Data Grafik Puskesmas
    $puskesmasList = Puskesmas::all();
    $puskesmasLabels = $puskesmasList->pluck('nama_puskesmas')->toArray();
    $puskesmasData = $puskesmasList->map(function ($p) use ($filterTrendBulan) {
        $q = Peserta::where('puskesmas_id', $p->id);
        if ($filterTrendBulan !== 'semua') {
            $q->whereMonth('dibuat_pada', $filterTrendBulan);
        }
        return $q->count();
    })->toArray();

    // Data Deteksi Dini per Puskesmas
    $deteksiData = $puskesmasList->map(function ($p) use ($filterTrendBulan) {
        $q = DeteksiDiniPTM::where('puskesmas_id', $p->id);
        if ($filterTrendBulan !== 'semua') {
            $q->whereMonth('tanggal_pemeriksaan', $filterTrendBulan);
        }
        return $q->count();
    })->toArray();

    // Data Faktor Risiko per Puskesmas
    $faktorData = $puskesmasList->map(function ($p) use ($filterTrendBulan) {
        $q = \App\Models\FaktorResikoPTM::where('puskesmas_id', $p->id);
        if ($filterTrendBulan !== 'semua') {
            $q->whereMonth('tanggal_pemeriksaan', $filterTrendBulan);
        }
        return $q->count();
    })->toArray();

    // Data Skrining
    $skNormal = (clone $deteksiBaseQuery)->where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai = (clone $deteksiBaseQuery)->where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko = (clone $deteksiBaseQuery)->where(function($q) {
        $q->where('hasil_skrining', 'LIKE', '%Risiko%')
          ->orWhere('hasil_skrining', 'LIKE', '%Resiko%');
    })->count();
    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;

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
            <div class="container py-2">
                    {{-- ================= SPANDUK SELAMAT DATANG ================= --}}
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg mb-6 relative overflow-hidden text-white flex justify-between items-center">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20"><i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i></div>
                    <div class="relative z-10">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi {{ $ikonUcapan }} fs-4"></i>
                            <h1 class="h4 fw-bold mb-0 tracking-wide">{{ $ucapan }}, Administrator!</h1>
                        </div>

                                                           <p class="text-blue-100 text-sm mb-0 mt-2">Berikut adalah ringkasan data PTM, dan statistik puskesmas.</p>
                    </div>
                    <div class="relative z-10 hidden md:block text-right">
                        <div class="text-blue-100 text-xs fw-semibold up
                                   percase tracking-wider mb-1">Tanggal Hari Ini</div>
                        <div class="fs-5 fw-bold">{{ $tanggalIndo }}</div>
                </div>



                                        </div>
                {{-- ================= KARTU INDIKATOR (KPI) ================= --}}
                <div class="row g-3 mb-4">
                    {{-- Card 1: Peserta --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-people-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Total Pasien</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPeserta) }}</h3></div></div></div>
                    </div>
                    {{-- Card 2: Deteksi Dini --}}
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-indigo-50 text-indigo-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-file-medical-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Deteksi Dini</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalDeteksi) }}</h3></div></div></div>
                        </div>
                    {{-- Card 3: Pegawai --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-green-50 text-green-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-person-badge-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Pegawai Dinkes</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPegawai) }}</h3></div></div></div>
                    </div>
                    {{-- Card 4: Petugas --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-person-workspace"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Petugas</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPetugas) }}</h3></div></div></div>
                    </div>
                </div>
                {{-- ================= PUSAT STATISTIK ================= --}}
                <div class="row g-4">
                    {{-- Chart Puskesmas --}}
                    <div class="col-lg-6">
                        <div class="card shadow-sm h-100 border-0 rounded-2xl">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4 border-b pb-3 border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="bi bi-hospital"></i></div>
                                        <h5 class="mb-0 fw-bold text-gray-800">Pasien Terdaftar per Puskesmas</h5>
                                    </div>
                                    <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-1 bg-light p-1.5 rounded-xl border border-gray-200">
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
        {{-- Chart Vertical Grouped --}}
    <div style="height: 320px; width: 100%;">
        <canvas id="puskesmasChart"></canvas>
    </div>                   
                            </div>
                        </div>
                    </div>
                    {{-- Chart Skrining --}}
                    <div class="col-lg-6">
                        <div class="card shadow-sm h-100 border-0 rounded-2xl">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-purple-100 text-purple-600 p-2 rounded-lg"><i class="bi bi-pie-chart-fill"></i></div>
                                        <h5 class="mb-0 fw-bold text-gray-800">Proporsi Hasil Skrining PTM</h5>
                                    </div>
                                    @if($filterTrendBulan !== 'semua' && isset($listBulanIndo[$filterTrendBulan]))
                                        <span class="badge bg-purple-50 text-purple-700 fw-bold px-2.5 py-1 rounded-pill text-xs border border-purple-200">
                                            ({{ $listBulanIndo[$filterTrendBulan] }})
                                        </span>
                                    @endif
                                </div>
                                    <div style="height: 280px; width: 100%;" class="d-flex align-items-center justify-content-center position-relative">
                                        @if($totalSkrining == 0)
                                            <div class="text-center py-5"><i class="bi bi-pie-chart text-gray-300 d-block mb-2" style="font-size: 3rem;"></i><p class="text-gray-400 text-sm mb-0">Data belum tersedia</p></div>
                                        @else
                                            <canvas id="skriningChart"></canvas>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- ================= PETA SEBARAN, KEPADATAN & CLUSTERING PUSKESMAS ================= --}}
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        @include('partials.peta_sebaran')
                    </div>
                </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('DOMContentLoaded', function () {
            // 1. GRAFIK PUSKESMAS VERTIKAL (GROUPED - 3 DATASET)
            const kanvasPuskesmas = document.getElementById('puskesmasChart');
            if (kanvasPuskesmas) {
                const ctx = kanvasPuskesmas.getContext('2d');
                const labelsPuskesmas = {!! json_encode($puskesmasLabels) !!};
                const dataPeserta     = {!! json_encode($puskesmasData) !!};
                const dataDeteksi     = {!! json_encode($deteksiData) !!};
                const dataFaktor      = {!! json_encode($faktorData) !!};

                // Fungsi buat gradient vertikal
                function buatGradient(ctx, r, g, b) {
                    const grad = ctx.createLinearGradient(0, 0, 0, 320);
                    grad.addColorStop(0,   `rgba(${r},${g},${b},0.95)`);
                    grad.addColorStop(1,   `rgba(${r},${g},${b},0.35)`);
                    return grad;
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labelsPuskesmas,
                                                    datasets: [
                                {
                                    label: 'Pasien Terdaftar',
                                    data: dataPeserta,
                                    backgroundColor: buatGradient(ctx, 59, 130, 246),
                                    borderColor: 'rgba(59,130,246,1)',
                                    borderWidth: 0,
                                    borderRadius: { topLeft: 6, topRight: 6 },
                                    borderSkipped: false,
                                    barThickness: 18,
                                }
                            ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',        // Tooltip muncul untuk semua dataset sekaligus
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
                                        const ikon = ctx.datasetIndex === 0 ? '👥'
                                                   : ctx.datasetIndex === 1 ? '🩺'
                                                   : '⚠️';
                                        const val = ctx.raw.toLocaleString('id-ID');
                                        return `  ${ikon} ${ctx.dataset.label}: ${val} data`;
                                    },
                                    afterBody: function(ctx) {
                                        const total = ctx.reduce((s, c) => s + c.raw, 0);
                                        return [`  ────────────────`, `  📊 Total: ${total.toLocaleString('id-ID')} data`];
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
                                        // Potong nama jika terlalu panjang
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
            }

                    // 2. GRAFIK DONAT (STATISTIK PTM)
                    const kanvasDonat = document.getElementById('skriningChart');
                    if (kanvasDonat) {
                        new Chart(kanvasDonat.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: ['Normal', 'Dicurigai PTM', 'Risiko Tinggi'],
                                datasets: [{
                                    data: [{{ $skNormal }}, {{ $skDicurigai }}, {{ $skRisiko }}],
                                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                });
            </script>
@endpush