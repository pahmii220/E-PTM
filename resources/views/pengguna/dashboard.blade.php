@extends('layouts.master')

@section('title', 'Dashboard Pengguna — PTM Monitor')

@section('content')
    @php use Illuminate\Support\Facades\Route; @endphp

    <div class="container py-3">
        {{-- ================= ALERT PROFIL BELUM LENGKAP ================= --}}
@if(
    auth()->check() &&
    auth()->user()->role_name === 'pengguna' &&
    !auth()->user()->profilDinkesLengkap()
)
    <div id="alert-profil"
         class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <strong>Profil Belum Lengkap</strong><br>
            Silakan lengkapi profil dan identitas Anda untuk melanjutkan.
        </div>

        <a href="{{ route('pengguna.pegawai_dinkes.edit', auth()->id()) }}"
           class="btn btn-warning btn-sm">
            Lengkapi Profil
        </a>
    </div>
@endif



            <div class="mb-4">
                <h1 class="h3 mb-1 fw-bold text-dark">Dashboard Pengguna</h1>
                <div class="text-muted">Ringkasan cepat data deteksi dini PTM</div>
            </div>

            <div class="row g-3 mb-5">

                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 kpi-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-text-wrapper">
                                    <span class="kpi-label">Total Peserta</span>
                                    <div class="kpi-value pb-1">{{ number_format($totalPasien ?? 0) }}</div>
                                    <span class="kpi-sub">Terdaftar</span>
                                </div>
                                <div class="kpi-icon-wrapper bg-primary-soft text-primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 kpi-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-text-wrapper">
                                    <span class="kpi-label">Total Deteksi</span>
                                    <div class="kpi-value pb-1">{{ number_format($totalDeteksi ?? 0) }}</div>
                                    <span class="kpi-sub">Rekam Medis</span>
                                </div>
                                <div class="kpi-icon-wrapper bg-info-soft text-info">
                                    <i class="bi bi-file-medical-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 kpi-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-text-wrapper">
                                    <span class="kpi-label">Faktor Risiko</span>
                                    <div class="kpi-value pb-1">{{ number_format($totalFaktor ?? 0) }}</div>
                                    <span class="kpi-sub">Terdata</span>
                                </div>
                                <div class="kpi-icon-wrapper bg-danger-soft text-danger">
                                    <i class="bi bi-heart-pulse-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card shadow-sm border-0 kpi-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-text-wrapper">
                                    <span class="kpi-label">Pending</span>
                                    <div class="kpi-value pb-1">{{ number_format($pendingTotal ?? 0) }}</div>
                                    <span class="kpi-sub">Verifikasi</span>
                                </div>
                                <div class="kpi-icon-wrapper bg-warning-soft text-warning">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 fw-bold">Monitoring (7 Hari)</h5>
                                <small class="text-muted">
                                    {{ $lastUpdatedAt?->format('d M Y') ?? now()->format('d M Y') }}
                                </small>
                            </div>

                            <div style="height: 300px; width: 100%;">
                                <canvas id="monitorChart"></canvas>
                            </div>

                            <br>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column">
                            <h6 class="text-muted mb-4 fw-bold text-uppercase small">Status Verifikasi</h6>

                            @php $sumVer = array_sum($verifCounts ?? [0]); @endphp

                            <div class="mb-4">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-success">Diterima</span>
                                    <span class="fw-bold">{{ $verifCounts['approved'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $sumVer ? ($verifCounts['approved'] / $sumVer) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-danger">Ditolak</span>
                                    <span class="fw-bold">{{ $verifCounts['rejected'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $sumVer ? ($verifCounts['rejected'] / $sumVer) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-secondary">Tertunda</span>
                                    <span class="fw-bold">{{ $verifCounts['pending'] ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-secondary" style="width: {{ $sumVer ? ($verifCounts['pending'] / $sumVer) * 100 : 0 }}%"></div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                @if (Route::has('pengguna.verifikasi.index'))
                                    <a href="{{ route('pengguna.verifikasi.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                        Kelola Data
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection


@push('styles')
    <style>
        /* 1. FORCE VISIBLE OVERFLOW (KUNCI PERBAIKAN) */
        .kpi-card .card-body {
    overflow: visible;
}


        /* 2. AREA TEKS */
        .kpi-text-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Padding extra untuk jaga-jaga */
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .kpi-label {
            font-size: 0.85rem;
            color: #6c757d;
            display: block;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        /* 3. PERBAIKAN UTAMA PADA VALUE */
        .kpi-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #212529;
            /* Line-height besar agar kaki huruf tidak kepotong */
            line-height: 1.4 !important;
            /* Margin negatif sedikit jika ingin rapat ke atas, tapi aman di bawah */
            margin-bottom: 4px;
            display: block;
        }

        .kpi-sub {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        /* 4. AREA IKON */
        .kpi-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
            margin-left: 12px;
        }

        /* Warna Soft */
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.12); }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.12); }
        .bg-info-soft    { background-color: rgba(13, 202, 240, 0.12); }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.15); }
        .bg-danger-soft  { background-color: rgba(220, 53, 69, 0.12); }

        /* Responsive */
        @media (max-width: 576px) {
            .kpi-value { font-size: 1.4rem; }
            .kpi-icon-wrapper { width: 42px; height: 42px; font-size: 1.3rem; }
        }
    </style>
@endpush

@push('scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* =========================
             * ALERT PROFIL BELUM LENGKAP
             * ========================= */
            const alertBox = document.getElementById('alert-profil');
            if (alertBox) {
                // tampil selama 10 detik
                setTimeout(() => {
                    alertBox.style.transition = 'opacity 1s ease';
                    alertBox.style.opacity = '0';

                    // hapus setelah animasi
                    setTimeout(() => {
                        alertBox.remove();
                    }, 1000);
                }, 10000); // 10 detik
            }

            /* =========================
             * CHART DASHBOARD
             * ========================= */
            const ctx = document.getElementById('monitorChart');
            if (!ctx) return;

            const dataValues = [
                Number({!! json_encode($totalDeteksi ?? 0) !!}),
                Number({!! json_encode($totalFaktor ?? 0) !!}),
                Number({!! json_encode($pendingTotal ?? 0) !!}),
                Number({!! json_encode($totalPasien ?? 0) !!})
            ];

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Deteksi Dini', 'Faktor Resiko', 'Pending', 'Peserta'],
                    datasets: [{
                        label: 'Jumlah',
                        data: dataValues,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(16, 185, 129, 0.7)'
                        ],
                        borderRadius: 6,
                        barThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4] }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

        });
    </script>
@endpush
