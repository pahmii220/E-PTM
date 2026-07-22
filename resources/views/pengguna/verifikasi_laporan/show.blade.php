@extends('layouts.master')

@section('title', 'Detail Laporan - {{ $puskesmas->nama_puskesmas }}')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px; margin:auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <a href="{{ route('pengguna.verifikasi_laporan.index', ['bulan' => $bulanInput, 'kota' => $kota ?? '', 'kecamatan' => $kecamatan ?? '']) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Puskesmas
            </a>
            <h3 class="fw-bold mb-0">
                <i class="bi bi-hospital me-2 text-primary"></i>{{ $puskesmas->nama_puskesmas }}
            </h3>
            <p class="text-muted small mb-0">
                <i class="bi bi-geo-alt me-1"></i>{{ $puskesmas->kecamatan }}, {{ $puskesmas->nama_kabupaten }}
                &nbsp;·&nbsp;
                <i class="bi bi-calendar3 me-1"></i>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}</strong>
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2 align-items-center">
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-eye me-2"></i>Mode Pemantauan</span>
            <a href="{{ route('pengguna.laporan_monitoring.index') }}" class="btn btn-teal rounded-pill px-3 shadow-sm font-semibold">
                <i class="bi bi-file-earmark-medical me-1"></i> Buat Laporan Monitoring
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif



    <style>
        .premium-card {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .premium-card:hover {
            box-shadow: 0 15px 35px -10px rgba(0,0,0,0.12);
        }
        .premium-header-pending {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-bottom: 2px solid #ffda6a;
        }
        .premium-header-approved {
            background: linear-gradient(135deg, #d1e7dd 0%, #a3cfbb 100%);
            border-bottom: 2px solid #75b798;
        }
        .premium-table th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
            background-color: #f8f9fa;
        }
        .premium-table td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f5;
        }
        .premium-table tbody tr {
            transition: all 0.2s ease;
        }
        .premium-table tbody tr:hover {
            background-color: #f8f9fc !important;
            transform: scale(1.001);
        }
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>


    {{-- TABEL LAPORAN PTM --}}
    <div class="card premium-card mb-4">
        <div class="card-header premium-header-approved pt-3 pb-3 border-0">
            <h6 class="fw-bold mb-0 text-success-emphasis fs-5"><i class="bi bi-card-list me-2 text-success"></i>Data Laporan PTM Bulan Ini</h6>
            <small class="text-muted">Semua data pemeriksaan PTM yang dikirimkan oleh puskesmas.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">No</th>
                            <th class="px-3">Tanggal Pemeriksaan</th>
                            <th class="px-3">Nama Pasien</th>
                            <th class="px-3">No RM</th>
                            <th class="px-3">Umur</th>
                            <th class="px-3">Jenis Kelamin</th>
                            <th class="px-3 text-center">TD (mmHg)</th>
                            <th class="px-3 text-center">Gula (mg/dL)</th>
                            <th class="px-3 text-center">Kol (mg/dL)</th>
                            <th class="px-3 text-center">IMT</th>
                            <th class="px-3">Faktor Risiko</th>
                            <th class="px-3">Diagnosa & Jenis Penyakit PTM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $no => $row)
                        <tr>
                            <td class="px-3 text-muted">{{ $no + 1 }}</td>
                            <td class="px-3 text-center">{{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                            <td class="px-3 fw-semibold">{{ $row->peserta->nama_lengkap ?? '-' }}</td>
                            <td class="px-3 text-muted">{{ $row->peserta->no_rekam_medis ?? '-' }}</td>
                            <td class="px-3 text-center">{{ $row->peserta ? \Carbon\Carbon::parse($row->peserta->tanggal_lahir)->age : '-' }}</td>
                            <td class="px-3">{{ $row->peserta->jenis_kelamin ?? '-' }}</td>
                            <td class="px-3 text-center">
                                @php
                                    $sistolik = null;
                                    $diastolik = null;
                                    if ($row->tekanan_darah && strpos($row->tekanan_darah, '/') !== false) {
                                        [$sistolik, $diastolik] = explode('/', $row->tekanan_darah);
                                    }
                                @endphp
                                <span class="{{ ($sistolik > 140 || $diastolik > 90) ? 'text-danger fw-bold' : '' }}">
                                    {{ $row->tekanan_darah ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                <span class="{{ $row->gula_darah > 126 ? 'text-danger fw-bold' : '' }}">
                                    {{ $row->gula_darah ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                <span class="{{ $row->kolesterol > 200 ? 'text-danger fw-bold' : '' }}">
                                    {{ $row->kolesterol ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                <span class="{{ $row->imt > 25 ? 'text-warning fw-bold' : '' }}">
                                    {{ $row->imt ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3" style="max-width:130px;">
                                @if(optional($row->faktorRisiko)->merokok === 'Ya')
                                    <span class="badge bg-danger-subtle text-danger mb-1 d-block">Merokok</span>
                                @endif
                                @if(optional($row->faktorRisiko)->kurang_aktivitas_fisik === 'Ya')
                                    <span class="badge bg-warning-subtle text-warning d-block">Kurang Fisik</span>
                                @endif
                                @if(!optional($row->faktorRisiko)->merokok && !optional($row->faktorRisiko)->kurang_aktivitas_fisik)
                                    <span class="badge bg-success-subtle text-success">Aman</span>
                                @endif
                            </td>
                            <td class="px-3" style="max-width:130px; white-space:normal;">
                                @if($row->diagnosa_penyakit)
                                    @foreach(explode(',', $row->diagnosa_penyakit) as $diag)
                                        @php
                                            $diagTrim = trim($diag);
                                            $badgeColor = match($diagTrim) {
                                                'Hipertensi' => 'bg-danger text-white',
                                                'Diabetes Melitus' => 'bg-warning text-dark',
                                                'Stroke' => 'bg-dark text-white',
                                                'Penyakit Jantung' => 'bg-info text-dark',
                                                'Obesitas' => 'bg-primary text-white',
                                                'Normal' => 'bg-success text-white',
                                                default => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeColor }} mb-1 d-inline-block" style="font-size:10px;">{{ $diagTrim }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-archive"></i></div>
                                <h6 class="text-muted fw-bold">Belum ada riwayat persetujuan.</h6>
                                <p class="text-muted small mb-0">Data yang telah Anda setujui akan muncul di sini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>
@endsection
