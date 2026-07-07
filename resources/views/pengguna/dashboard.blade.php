@extends('layouts.master')

@section('title', 'Dashboard Pegawai — PTM Monitor')

@section('content')
        @php 
            use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;

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

    // Logika Tanggal Indonesia Manual (Anti-Error)
    $namaHari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    $namaBulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

    // =======================================================================
    // 2. JALAN PINTAS MENGAMBIL DATA LANGSUNG DARI DATABASE (BYPASS CONTROLLER)
    // =======================================================================

    // Ambil Total KPI
    $realTotalPeserta = \App\Models\Peserta::count();
    $realTotalDeteksi = \App\Models\DeteksiDiniPTM::count();
    $realTotalFaktor = \App\Models\FaktorResikoPTM::count();
    $realPending = \App\Models\Peserta::where('status_verifikasi', 'pending')->count();
    $realApproved = \App\Models\Peserta::where('status_verifikasi', 'approved')->count();
    $realRejected = \App\Models\Peserta::where('status_verifikasi', 'rejected')->count();
    $realTotalVerif = $realApproved + $realRejected + $realPending;

    // Ambil Data Skrining PTM untuk Grafik Donat
    $skNormal = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() +
        \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();

    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;
        @endphp

        <div class="container py-2">

            {{-- ================= PERINGATAN PROFIL BELUM LENGKAP ================= --}}
            @if(auth()->check() && auth()->user()->role_name === 'pegawai' && !auth()->user()->profilDinkesLengkap())
                <div id="alert-profil" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow-sm mb-4 flex justify-between items-center transition-all duration-500">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 text-yellow-500 fs-3">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-yellow-800 text-sm font-bold mb-0">Profil Anda Belum Lengkap!</h3>
                            <p class="text-yellow-700 text-xs mt-1 mb-0">Silakan lengkapi profil dan identitas Anda untuk memaksimalkan penggunaan sistem.</p>
                        </div>
                    </div>
                    <a href="{{ route('pengguna.pegawai_dinkes.edit', auth()->id()) }}" class="btn btn-warning btn-sm fw-bold shadow-sm rounded-lg px-3">
                        Lengkapi Sekarang
                    </a>
                </div>
            @endif

            {{-- ================= SPANDUK SELAMAT DATANG ================= --}}
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg mb-6 relative overflow-hidden text-white flex justify-between items-center">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                    <i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i>
                </div>
                <div class="absolute bottom-0 right-32 -mb-10 opacity-10">
                    <i class="bi bi-circle-fill" style="font-size: 10rem;"></i>
                </div>

                <div class="relative z-10">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi {{ $ikonUcapan }} fs-4"></i>
                        <h1 class="h4 fw-bold mb-0 tracking-wide">{{ $ucapan }}, {{ Auth::user()->username ?? 'Pegawai Dinkes' }}!</h1>
                    </div>
                    <p class="text-blue-100 text-sm mb-0 mt-2">Berikut adalah ringkasan data Penyakit Tidak Menular (PTM) dan antrean verifikasi hari ini.</p>
                </div>

                <div class="relative z-10 hidden md:block text-right">
                    <div class="text-blue-100 text-xs fw-semibold uppercase tracking-wider mb-1">Tanggal Hari Ini</div>
                    <div class="fs-5 fw-bold">{{ $tanggalIndo }}</div>
                </div>
            </div>

            {{-- ================= KARTU INDIKATOR (KPI) ================= --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-50 text-blue-600 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Total Peserta</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPeserta) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-indigo-50 text-indigo-500 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-file-medical-fill"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Deteksi Dini</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalDeteksi) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-red-50 text-red-500 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-heart-pulse-fill"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Faktor Risiko</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalFaktor) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100 relative overflow-hidden">
                        @if($realPending > 0)
                            <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full m-3 animate-pulse shadow-sm border border-white"></div>
                        @endif
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-amber-50 text-amber-500 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Antrean Verifikasi</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realPending) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= PUSAT STATISTIK & ANALISIS DATA PTM ================= --}}
            <div class="row g-4">

                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="bi bi-bar-chart-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Rekapitulasi Status PTM</h5>
                                </div>
                            </div>
                            <div style="height: 280px; width: 100%;">
                                <canvas id="monitorChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="bg-purple-100 text-purple-600 p-2 rounded-lg"><i class="bi bi-pie-chart-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Proporsi Hasil Skrining PTM</h5>
                                </div>
                            </div>
                            <div style="height: 280px; width: 100%;" class="d-flex align-items-center justify-content-center position-relative">

                                @if($totalSkrining == 0)
                                    <div class="text-center py-5">
                                        <i class="bi bi-pie-chart text-gray-300 d-block mb-2" style="font-size: 3rem;"></i>
                                        <p class="text-gray-400 text-sm mb-0">Data hasil skrining belum tersedia</p>
                                    </div>
                                    <canvas id="skriningChart" class="d-none"></canvas>
                                @else
                                    <canvas id="skriningChart"></canvas>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="bg-green-100 text-green-600 p-2 rounded-lg"><i class="bi bi-shield-check"></i></div>
                                    <h6 class="mb-0 fw-bold text-gray-800">Ringkasan Status Verifikasi Laporan</h6>
                                </div>
                                <span class="badge bg-gray-100 text-gray-600 px-3 py-2 rounded-full fw-normal shadow-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i> Diperbarui: {{ $waktuSekarang->format('H:i') . ' WITA' }}
                                </span>
                            </div>

                            <div class="row g-4 align-items-center">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <div class="d-flex justify-content-between text-sm mb-1">
                                                <span class="fw-semibold text-gray-600">Diterima</span>
                                                <span class="fw-bold text-success">{{ $realApproved }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f3f4f6;">
                                                <div class="progress-bar bg-green-500" style="width: {{ $realTotalVerif ? ($realApproved / $realTotalVerif) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <div class="d-flex justify-content-between text-sm mb-1">
                                                <span class="fw-semibold text-gray-600">Ditolak</span>
                                                <span class="fw-bold text-danger">{{ $realRejected }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f3f4f6;">
                                                <div class="progress-bar bg-red-500" style="width: {{ $realTotalVerif ? ($realRejected / $realTotalVerif) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between text-sm mb-1">
                                                <span class="fw-semibold text-gray-600">Tertunda</span>
                                                <span class="fw-bold text-amber-500">{{ $realPending }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #f3f4f6;">
                                                <div class="progress-bar bg-amber-400" style="width: {{ $realTotalVerif ? ($realPending / $realTotalVerif) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 text-end">
                                    @if (Route::has('pengguna.verifikasi.index'))
                                        <a href="{{ route('pengguna.verifikasi.index') }}" class="btn btn-primary w-100 rounded-xl fw-semibold shadow-sm hover:bg-blue-600 transition duration-300 py-2 d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-box-arrow-in-right"></i> Kelola Verifikasi
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('styles')
    <style>
        .kpi-card {
            text-align: center !important;
        }
        .kpi-card .card-body {
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
        .kpi-card .w-14 {
            margin-bottom: 0.75rem !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. INISIALISASI GRAFIK BATANG (VOLUME DATA)
            const kanvasBatang = document.getElementById('monitorChart');
            if (kanvasBatang) {
                // Ambil data langsung dari variabel PHP yang bypass tadi
                const dataNilai = [
                    {{ $realTotalPeserta }},
                    {{ $realTotalDeteksi }},
                    {{ $realTotalFaktor }},
                    {{ $realPending }}
                ];

                new Chart(kanvasBatang.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Peserta', 'Deteksi Dini', 'Faktor Risiko', 'Antrean Verifikasi'],
                        datasets: [{
                            label: 'Total Data',
                            data: dataNilai,
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)', // Biru
                                'rgba(99, 102, 241, 0.8)', // Indigo
                                'rgba(239, 68, 68, 0.8)',  // Merah
                                'rgba(245, 158, 11, 0.8)'  // Kuning/Amber
                            ],
                            borderRadius: 8,
                            barThickness: 'flex',
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#f3f4f6', borderDash: [5, 5] } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. INISIALISASI GRAFIK DONAT (STATISTIK PTM)
            const kanvasDonat = document.getElementById('skriningChart');
            if (kanvasDonat && !kanvasDonat.classList.contains('d-none')) {
                // Ambil data skrining langsung dari PHP bypass
                const dataSkrining = [
                    {{ $skNormal }},
                    {{ $skDicurigai }},
                    {{ $skRisiko }}
                ];

                new Chart(kanvasDonat.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Normal', 'Dicurigai PTM', 'Risiko Tinggi'],
                        datasets: [{
                            data: dataSkrining,
                            backgroundColor: [
                                '#10b981', // Hijau Emerald (Normal)
                                '#f59e0b', // Amber/Kuning (Dicurigai)
                                '#ef4444'  // Merah (Risiko Tinggi)
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: { boxWidth: 12, font: { size: 12, weight: '500' }, padding: 15 }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
@endpush