@extends('layouts.master')

@section('title', 'Dashboard Admin — PTM Monitor')

@section('content')
    @php 
                use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\User;
use App\Models\Puskesmas;

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
// 2. DATA KPI (Modified)
// =======================================================================
$realTotalPasien = Pasien::count();
$realTotalDeteksi = DeteksiDiniPTM::count();
$realTotalPegawai = User::where('role_name', 'pegawai')->count();
$realTotalPetugas = User::where('role_name', 'petugas')->count();

// Data Grafik Puskesmas
$puskesmasList = Puskesmas::all();
$puskesmasLabels = $puskesmasList->pluck('nama_puskesmas')->toArray();
$puskesmasData = $puskesmasList->map(function ($p) {
    return Pasien::where('puskesmas_id', $p->id)->count();
})->toArray();

// Data Skrining
$skNormal = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
$skDicurigai = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
$skRisiko = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() + DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();
$totalSkrining = $skNormal + $skDicurigai + $skRisiko;
    @endphp
    <div class="container py-2">
            {{-- ================= SPANDUK SELAMAT DATANG ================= --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg mb-6 relative overflow-hidden text-white flex justify-between items-center">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20"><i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i></div>
            <div class="relative z-10">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi {{ $ikonUcapan }} fs-4"></i>
                    <h1 class="h4 fw-bold mb-0 tracking-wide">{{ $ucapan }}, {{ Auth::user()->username ?? 'Admin' }}!</h1>
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
                <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-people-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Total Peserta</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPasien) }}</h3></div></div></div>
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
                        <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="bi bi-hospital"></i></div>
                                <h5 class="mb-0 fw-bold text-gray-800">Pasien Terdaftar per Puskesmas</h5>
                            </div>
                        </div>
                        <div style="height: 280px; width: 100%;"><canvas id="puskesmasChart"></canvas></div>
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
        </div>
@endsection

@push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
    // 1. GRAFIK PUSKESMAS
    new Chart(document.getElementById('puskesmasChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($puskesmasLabels) !!},
            datasets: [{
                label: 'Jumlah Pasien',
                data: {!! json_encode($puskesmasData) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 8,
                // --- TAMBAHAN INI YANG MEMBUAT UKURANNYA SEDANG ---
                barPercentage: 0.5,       // Bar mengambil 50% dari ruang kategori
                categoryPercentage: 0.5,  // Kategori mengambil 50% dari ruang grafik
                maxBarThickness: 80       // Lebar maksimal batang tidak lebih dari 80px
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    ticks: { precision: 0 }, // Agar angka tidak desimal
                    grid: { borderDash: [4,4] } 
                },
                x: { grid: { display: false } }
            } 
        }
    });

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