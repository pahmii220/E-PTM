@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard P2PTM</h1>
                <p class="text-muted">Ringkasan data pengendalian penyakit tidak menular.</p>
            </div>
        </div>

        {{-- BARIS 1: SUMMARY CARDS --}}
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 py-2 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Peserta</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalPeserta'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-people fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 py-2 border-start border-success border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Deteksi Dini
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalDeteksi'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-activity fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 py-2 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Data Risiko</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalRisiko'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 py-2 border-start border-info border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Puskesmas Terdaftar
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['totalPuskesmas'] }}</div>
                            </div>
                            <div class="col-auto"><i class="bi bi-hospital fs-2 text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2: WELCOME CARD --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/dinkes.png') }}" alt="Logo Dinkes" width="60" class="me-4">
                            <div>
                                <h5 class="fw-bold text-success mb-1">Selamat Datang, Bapak/Ibu Kepala Bidang P2P</h5>
                                <p class="text-muted mb-0">Sistem telah terintegrasi dengan data real-time dari seluruh
                                    Puskesmas. Pilih menu laporan di sidebar untuk melakukan verifikasi dan cetak dokumen
                                    resmi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection