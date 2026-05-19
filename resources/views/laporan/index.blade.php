@extends('layouts.master')

@section('title', 'Halaman Laporan')

@section('content')

    <div class="container py-5">

        <div class="text-center mb-5">
            <h3 class="fw-bold text-gray-800">
                <i class="bi bi-file-earmark-text me-2"></i>
                Halaman Laporan
            </h3>
            <p class="text-muted mb-0">
                Cetak laporan data Penyakit Tidak Menular (PTM) secara lengkap dan terverifikasi
            </p>

            <div class="mt-3">
                <hr style="width:120px;margin:auto;border:2px solid #198754;">
            </div>
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
                        <p>Cetak seluruh data peserta PTM</p>
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
                        <p>Hasil pemeriksaan deteksi dini PTM</p>
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
                        <p>Data faktor risiko penyakit tidak menular</p>
                    </div>
                </a>
            </div>

            <!-- LAPORAN TINDAK LANJUT -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.verifikasi.print.tindak_lanjut') }}" target="_blank"
                    class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-success-soft">
                            <i class="bi bi-clipboard-check-fill text-success"></i>
                        </div>
                        <h5>Laporan Tindak Lanjut</h5>
                        <p>Data tindak lanjut pemeriksaan PTM</p>
                    </div>
                </a>
            </div>

            <!-- REKAP PUSKESMAS -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.rekap.puskesmas.print') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-info-soft">
                            <i class="bi bi-bar-chart-fill text-info"></i>
                        </div>
                        <h5>Rekap Puskesmas</h5>
                        <p>Rekap data PTM berdasarkan puskesmas</p>
                    </div>
                </a>
            </div>

            <!-- HASIL SKRINING PTM -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.laporan.status_ptm') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-secondary-soft">
                            <i class="bi bi-heart-pulse text-secondary"></i>
                        </div>
                        <h5>Laporan Hasil Skrining PTM</h5>
                        <p>Rekap peserta berdasarkan hasil skrining PTM</p>
                    </div>
                </a>
            </div>

            <!-- KELOMPOK USIA -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.laporan.kelompok_usia.print') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-purple-soft">
                            <i class="bi bi-person-lines-fill text-purple"></i>
                        </div>
                        <h5>PTM Berdasarkan Kelompok Usia</h5>
                        <p>Rekap peserta berdasarkan kelompok usia</p>
                    </div>
                </a>
            </div>

            <!-- KEGIATAN PTM -->
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('pengguna.laporan.kegiatan') }}" target="_blank" class="text-decoration-none">
                    <div class="report-card">
                        <div class="icon-box bg-dark-soft">
                            <i class="bi bi-calendar-event text-dark"></i>
                        </div>
                        <h5>Laporan Kegiatan PTM</h5>
                        <p>Rekap pelaksanaan kegiatan PTM</p>
                    </div>
                </a>
            </div>

        </div>
        ```

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
            transition: all .25s ease;
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
            font-size: 30px;
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

        .bg-info-soft {
            background: rgba(13, 202, 240, .15);
        }

        .bg-secondary-soft {
            background: rgba(108, 117, 125, .15);
        }

        .bg-purple-soft {
            background: rgba(111, 66, 193, .15);
        }

        .bg-dark-soft {
            background: rgba(33, 37, 41, .12);
        }

        .text-purple {
            color: #6f42c1;
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