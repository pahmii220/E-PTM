@extends('layouts.master')

@section('title', 'Halaman Laporan')

@section('content')
    <div class="container py-5">

        <div class="text-center mb-5">
            <h3 class="fw-bold text-gray-800">Halaman Laporan</h3>
            <p class="text-muted mb-0">
                Cetak laporan data PTM secara lengkap dan terverifikasi
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            <!-- LAPORAN PESERTA -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.pasien', ['status' => 'all']) }}" target="_blank"
                    class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-primary-soft">
                            <i class="bi bi-people-fill text-primary"></i>
                        </div>
                        <h5>Laporan Peserta</h5>
                        <p>Cetak seluruh data peserta</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN DETEKSI DINI -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.deteksi') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-danger-soft">
                            <i class="bi bi-heart-pulse-fill text-danger"></i>
                        </div>
                        <h5>Laporan Deteksi Dini</h5>
                        <p>Cetak hasil pemeriksaan deteksi dini PTM</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN FAKTOR RISIKO -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.faktor') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-warning-soft">
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        </div>
                        <h5>Laporan Faktor Risiko</h5>
                        <p>Cetak data faktor risiko pasien</p>
                    </div>
                </a>
            </div>

            <!-- ✅ LAPORAN TINDAK LANJUT (BARU) -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.tindak_lanjut') }}" target="_blank"
                    class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-success-soft">
                            <i class="bi bi-clipboard-check-fill text-success"></i>
                        </div>
                        <h5>Laporan Tindak Lanjut</h5>
                        <p>Cetak data tindak lanjut PTM</p>
                    </div>
                </a>
            </div>

            <!-- ✅ LAPORAN REKAP PER PUSKESMAS -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.rekap.puskesmas.print') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-info-soft">
                            <i class="bi bi-bar-chart-fill text-info"></i>
                        </div>
                        <h5>Rekap Puskesmas</h5>
                        <p>Rekap data PTM per puskesmas</p>
                    </div>
                </a>
            </div>



        </div>
    </div>

    <style>
        .report-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 36px 28px;
            text-align: center;
            height: 100%;
            border: 1px solid #eef1f4;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 32px;
        }

        .bg-primary-soft {
            background: rgba(13, 110, 253, .12);
        }

        .bg-danger-soft {
            background: rgba(220, 53, 69, .12);
        }

        .bg-warning-soft {
            background: rgba(255, 193, 7, .18);
        }

        .bg-success-soft {
            background: rgba(25, 135, 84, .12);
        }

        .report-card h5 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 6px;
        }

        .report-card p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 0;
        }
    </style>
@endsection