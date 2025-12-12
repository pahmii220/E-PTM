@extends('layouts.master')

@section('title', 'Halaman Laporan')

@section('content')
    <div class="container py-4">

        <h3 class="mb-4 fw-bold text-center">Laporan</h3>

        <div class="row g-4 justify-content-center">

            <!-- LAPORAN PASIEN -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.pasien', ['status' => 'all']) }}" target="_blank"
                    class="text-decoration-none">
                    <div class="report-card">
                        <i class="bi bi-people-fill icon text-primary"></i>
                        <h5 class="mt-3 fw-semibold">Laporan Peserta</h5>
                        <p class="text-muted small">Cetak seluruh data Peserta</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN DETEKSI DINI -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.faktor') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <i class="bi bi-heart-pulse-fill icon text-danger"></i>
                        <h5 class="mt-3 fw-semibold">Laporan Deteksi Dini</h5>
                        <p class="text-muted small">Cetak seluruh pemeriksaan deteksi dini</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN FAKTOR RISIKO -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.deteksi') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <i class="bi bi-exclamation-triangle-fill icon text-warning"></i>
                        <h5 class="mt-3 fw-semibold">Laporan Faktor Risiko</h5>
                        <p class="text-muted small">Cetak faktor risiko pasien</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN PETUGAS -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('admin.data_petugas.print') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <i class="bi bi-person-badge-fill icon text-success"></i>
                        <h5 class="mt-3 fw-semibold">Laporan Petugas</h5>
                        <p class="text-muted small">Cetak seluruh data petugas</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN PUSKESMAS -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('admin.data_puskesmas.print') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <i class="bi bi-building-fill icon text-info"></i>
                        <h5 class="mt-3 fw-semibold">Laporan Puskesmas</h5>
                        <p class="text-muted small">Cetak seluruh data puskesmas</p>
                    </div>
                </a>
            </div>

        </div>

    </div>

    <style>
        .report-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 32px 25px;
            text-align: center;
            border: 1px solid #e6e6e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            transition: all 0.25s ease-in-out;
            height: 100%;
        }

        .report-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .icon {
            font-size: 48px;
        }

        h5 {
            margin-top: 12px;
            font-weight: 600;
        }
    </style>
@endsection