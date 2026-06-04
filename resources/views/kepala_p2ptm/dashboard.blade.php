@extends('layouts.master')

@section('title', 'Dashboard Eksekutif')

@section('content')
@php 
    use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;

    $waktuSekarang = Carbon::now('Asia/Makassar');
    $jam = $waktuSekarang->format('H');

    // Logika Ucapan
    if ($jam >= 5 && $jam < 12) { $ucapan = 'Selamat Pagi'; $ikonUcapan = 'bi-brightness-alt-high-fill text-yellow-300'; } 
    elseif ($jam >= 12 && $jam < 15) { $ucapan = 'Selamat Siang'; $ikonUcapan = 'bi-sun-fill text-yellow-400'; } 
    elseif ($jam >= 15 && $jam < 18) { $ucapan = 'Selamat Sore'; $ikonUcapan = 'bi-cloud-sun-fill text-orange-300'; } 
    else { $ucapan = 'Selamat Malam'; $ikonUcapan = 'bi-moon-stars-fill text-blue-200'; }

    // Logic Tanggal Bahasa Indonesia (Anti-Error)
    $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');
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
                <h2 class="fw-bold mb-0 tracking-wide">{{ $ucapan }}, {{ Auth::user()->username ?? 'Kepala P2PTM' }}!</h2>
            </div>
            <p class="text-blue-100 mb-0 mt-2 fs-5">Berikut adalah ringkasan data Penyakit Tidak Menular (PTM) dan antrean verifikasi hari ini.</p>
        </div>

        <div class="relative z-10 d-none d-md-block text-end">
            <div class="text-blue-100 small text-uppercase fw-bold opacity-75 mb-1">Tanggal Hari Ini</div>
            <div class="fs-4 fw-bold">{{ $tanggalIndo }}</div>
        </div>
    </div>

    {{-- BARIS 1: STATISTIK UTAMA --}}
    <div class="row g-4 mb-4">
        @php 
            $cards = [
                ['title' => 'Total Peserta', 'value' => $data['totalPeserta'], 'icon' => 'bi-people', 'color' => 'primary'],
                ['title' => 'Deteksi Dini', 'value' => $data['totalDeteksi'], 'icon' => 'bi-activity', 'color' => 'success'],
                ['title' => 'Faktor Risiko', 'value' => $data['totalRisiko'], 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                ['title' => 'Puskesmas', 'value' => $data['totalPuskesmas'], 'icon' => 'bi-hospital', 'color' => 'info']
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-xl-3 col-md-6">
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
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-blue-50 text-blue-600 p-2 rounded-3 me-3"><i class="bi bi-bar-chart-fill fs-4"></i></div>
                    <h4 class="fw-bold mb-0 text-dark">Volume Data</h4>
                </div>
                <div class="mx-auto" style="height: 250px; width: 100%; max-width: 400px;">
                    <canvas id="monitorChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-purple-50 text-purple-600 p-2 rounded-3 me-3"><i class="bi bi-pie-chart-fill fs-4"></i></div>
                    <h4 class="fw-bold mb-0 text-dark">Proporsi Skrining</h4>
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
    </div>

    {{-- BARIS 3: TARGET PELAPORAN --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 transition-hover">
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <h4 class="fw-bold text-dark mb-0">Target Pelaporan Puskesmas</h4>
                    <span class="fw-bold text-success fs-3">{{ $data['persentase'] }}%</span>
                </div>
                <div class="progress rounded-pill bg-slate-200" style="height: 16px;">
                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $data['persentase'] }}%"></div>
                </div>
                <p class="small text-muted mt-3 mb-0 fs-6">*Persentase Puskesmas yang telah mengunggah data bulan berjalan.</p>
            </div>
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
            
            // Bar Chart
            new Chart(document.getElementById('monitorChart'), {
                type: 'bar',
                data: {
                    labels: ['Peserta', 'Deteksi', 'Risiko'],
                    datasets: [{
                        data: [{{ $data['totalPeserta'] }}, {{ $data['totalDeteksi'] }}, {{ $data['totalRisiko'] }}],
                        backgroundColor: ['#3b82f6', '#22c55e', '#ef4444'],
                        borderRadius: 8,
                        barThickness: 30
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // Doughnut Chart
            new Chart(document.getElementById('skriningChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'Dicurigai', 'Risiko'],
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
        });
    </script>
@endpush