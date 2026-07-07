@extends('layouts.master')

@section('title', 'Verifikasi Data')

@section('content')
    <div class="container py-4">
        <h2 class="fw-bold mb-4">Dashboard Verifikasi</h2>
        <br>

        <div class="row g-4">
            {{-- Kartu Peserta Pending --}}
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.peserta', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card p-4 shadow-sm border-0 h-100 transition-hover" style="transition: transform 0.2s;">
                        <h5>Peserta (Tertunda)</h5>
                        <h2 class="text-primary">{{ $pendingPeserta }}</h2>
                        <small class="text-muted">Klik untuk lihat daftar</small>
                    </div>
                </a>
            </div>

            {{-- Kartu Deteksi Pending --}}
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.deteksi', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card p-4 shadow-sm border-0 h-100 transition-hover" style="transition: transform 0.2s;">
                        <h5>Deteksi (Tertunda)</h5>
                        <h2 class="text-warning">{{ $pendingDeteksi }}</h2>
                        <small class="text-muted">Klik untuk lihat daftar</small>
                    </div>
                </a>
            </div>

            {{-- Kartu Faktor Risiko Pending --}}
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.faktor', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card p-4 shadow-sm border-0 h-100 transition-hover" style="transition: transform 0.2s;">
                        <h5>Faktor Risiko (Tertunda)</h5>
                        <h2 class="text-danger">{{ $pendingFaktor }}</h2>
                        <small class="text-muted">Klik untuk lihat daftar</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Tambahkan style sederhana agar kartu bereaksi saat di-hover --}}
    <style>
        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
    </style>
@endsection