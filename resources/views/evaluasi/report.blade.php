@extends('layouts.master')

@section('title', 'Laporan Penerimaan Sistem')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1200px;">

        {{-- HEADER DENGAN TOMBOL CETAK DI KANAN --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-graph-up-arrow me-2"></i>Laporan Ke-10: Evaluasi Kemudahan & Penerimaan Sistem
            </h4>
            <a href="{{ route('pengguna.evaluasi.cetak') }}" target="_blank"
                class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="bi bi-printer-fill me-2"></i> Cetak Dokumen Fisik
            </a>
        </div>
        <br>

        {{-- BARIS KARTU INFORMASI UTAMA --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h6 class="text-muted fw-bold mb-1">Total Pegawai Berpartisipasi</h6>
                    <h2 class="fw-bold text-primary mb-0">{{ $totalResponden }} <span class="fs-6 text-muted">Orang</span>
                    </h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h6 class="text-muted fw-bold mb-1">Rata-rata Skor SUS Aplikasi</h6>
                    <h2 class="fw-bold text-success mb-0">{{ $rataRataSkor }} <span class="fs-6 text-muted">/ 100</span>
                    </h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-white"
                    style="background: linear-gradient(135deg, #10b981, #059669);">
                    <h6 class="text-white-50 fw-bold mb-1">Tingkat Kelayakan Sistem</h6>
                    <h4 class="fw-bold mb-0 mt-1">{{ $predikat }}</h4>
                </div>
            </div>
        </div>

        {{-- TABEL RINCIAN FEEDBACK --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-light py-3 px-4 border-0">
                <h6 class="fw-bold mb-0 text-dark">Daftar Tanggapan Masuk</h6>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Pegawai</th>
                            <th class="text-center" width="150">Skor</th>
                            <th>Kritik & Saran</th>
                            <th width="180">Waktu Pengisian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semuaData as $index => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $row->user->pegawaiDinkes->nama_pegawai ?? 'Pegawai/Petugas' }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">{{ $row->skor_sus }}</span>
                                </td>
                                <td class="text-secondary" style="white-space: normal; max-width: 400px;">
                                    {{ $row->saran ?? '-' }}
                                </td>
                                <td class="text-muted small">{{ $row->created_at->format('d M Y - H:i') }} Wita</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data evaluasi yang dimasukkan oleh
                                    pegawai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection