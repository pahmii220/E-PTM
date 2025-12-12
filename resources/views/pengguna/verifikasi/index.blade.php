@extends('layouts.master')

@section('content')
    <div class="container py-4">
        <h3>Verifikasi Data</h3>

        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.pasien') }}" class="card p-3 text-decoration-none">
                    <h5>Pasien Pending</h5>
                    <h2>{{ $pendingPasien }}</h2>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.deteksi') }}" class="card p-3 text-decoration-none">
                    <h5>Deteksi Dini Pending</h5>
                    <h2>{{ $pendingDeteksi }}</h2>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pengguna.verifikasi.faktor') }}" class="card p-3 text-decoration-none">
                    <h5>Faktor Risiko Pending</h5>
                    <h2>{{ $pendingFaktor }}</h2>
                </a>
            </div>
        </div>
    </div>
@endsection