@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bolder text-dark">Dashboard Eksekutif</h2>
        <p class="text-secondary">Pantauan kinerja P2PTM Provinsi Kalimantan Selatan</p>
    </div>

    {{-- BARIS 1: CARDS DENGAN GAYA MODERN --}}
    <div class="row g-4">
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
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase small fw-bold mb-1">{{ $card['title'] }}</p>
                        <h3 class="fw-bold mb-0">{{ $card['value'] }}</h3>
                    </div>
                    <div class="bg-{{ $card['color'] }}-subtle p-3 rounded-circle text-{{ $card['color'] }}">
                        <i class="bi {{ $card['icon'] }} fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- BARIS 2: PROGRESS BAR & INFORMASI --}}
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3">Target Pelaporan Puskesmas</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Aktivitas Puskesmas Bulan Ini</span>
                    <span class="fw-bold text-success">{{ $data['persentase'] }}%</span>
                </div>
                <div class="progress rounded-pill" style="height: 15px;">
                    <div class="progress-bar bg-success rounded-pill" role="progressbar" 
                         style="width: {{ $data['persentase'] }}%"></div>
                </div>
                <p class="small text-muted mt-3 italic">*Puskesmas yang sudah mengunggah laporan bulan berjalan.</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-success text-white">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('images/dinkes.png') }}" width="50" class="me-3">
                    <h6 class="mb-0 fw-bold">Dinas Kesehatan Kalsel</h6>
                </div>
                <p class="small opacity-75">Sistem ini memfasilitasi pengesahan dokumen digital dengan QR-Code terverifikasi.</p>
                <a href="{{ route('kepala.laporan.deteksi_dini') }}" class="btn btn-outline-light btn-sm rounded-pill mt-2">Lihat Laporan Terbaru</a>
            </div>
        </div>
    </div>
</div>
@endsection