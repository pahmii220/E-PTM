@extends('layouts.master')

@section('title', 'Dashboard Pengguna — PTM Monitor')

@section('content')
    @php use Illuminate\Support\Facades\Route; @endphp

    <div class="container py-3">
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
                                    <span class="kpi-label">Total Pasien</span>
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

                            <div class="d-flex gap-5 mt-4 pt-3 border-top">
                                <div>
                                    <small class="text-muted d-block mb-1">Rata-rata / Hari</small>
                                    <div class="h5 fw-bold mb-0">{{ $avgPerDay ?? 0 }}</div>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Total Minggu Ini</small>
                                    <div class="h5 fw-bold mb-0">{{ $weeklyTotal ?? 0 }}</div>
                                </div>
                            </div>
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

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Recent Deteksi</h5>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    @php $q = request()->except('status'); @endphp
                                    <a href="{{ url()->current() . (count($q) ? '?' . http_build_query($q) : '') }}" 
                                       class="btn {{ empty($statusFilter) ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($q, ['status' => 'approved'])) }}" 
                                       class="btn {{ $statusFilter === 'approved' ? 'btn-primary' : 'btn-outline-secondary' }}">Approved</a>
                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($q, ['status' => 'pending'])) }}" 
                                       class="btn {{ $statusFilter === 'pending' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Pending</a>
                                </div>
                            </div>

                            @if ($recentDeteksi->isEmpty())
                                <div class="alert alert-light text-center">Belum ada data terbaru.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="ps-3">Pasien</th>
                                                <th>Waktu</th>
                                                <th class="text-end pe-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentDeteksi as $d)
                                                <tr class="border-bottom">
                                                    <td class="ps-3">
                                                        <div class="fw-bold text-dark">{{ optional($d->pasien)->nama ?? 'Tanpa Nama' }}</div>
                                                        <div class="small text-muted">ID: #{{ $d->id }}</div>
                                                    </td>
                                                    <td class="small text-muted">
                                                        {{ $d->created_at->format('d M Y, H:i') }}
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <span class="badge rounded-pill 
                                                            @if ($d->verification_status == 'approved') bg-success-soft text-success
                                                            @elseif($d->verification_status == 'rejected') bg-danger-soft text-danger
                                                            @else bg-warning-soft text-warning @endif">
                                                            {{ ucfirst($d->verification_status ?? 'pending') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('styles')
    <style>
        /* 1. FORCE VISIBLE OVERFLOW (KUNCI PERBAIKAN) */
        .kpi-card, .card-body {
            overflow: visible !important;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    labels: ['Deteksi', 'Faktor', 'Pending', 'Pasien'],
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
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endpush