@extends('layouts.master')

@section('title', 'Monitoring Kepatuhan Puskesmas')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px; margin:auto;">
    <style>
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { font-size: 11px; }
            .card { box-shadow: none !important; border: none !important; }
            .card-header, .btn, form#filterForm, .nav-tabs, .d-flex.justify-content-between.mb-4 { display: none !important; }
            .table-responsive { max-height: none !important; overflow: visible !important; }
            table { width: 100% !important; page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            #matriks { display: block !important; opacity: 1 !important; visibility: visible !important; }
        }
    </style>

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span style="background:linear-gradient(135deg,#0f766e,#0d9488); width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-shield-check text-white fs-5"></i>
                </span>
                Monitoring PTM Puskesmas
            </h2>
            <p class="text-muted mb-0 small">Pantau kepatuhan pengiriman data laporan PTM dari setiap Puskesmas dan kirimkan pengingat jika diperlukan.</p>
        </div>
        @if($kotaFilter)
        <div class="text-end">
            <span class="text-muted small d-block">Periode aktif</span>
            <span class="fw-bold text-teal fs-5">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}</span>
        </div>
        @endif
    </div>

    {{-- ===== WIDGET PERINGATAN DINI EPIDEMIOLOGI ===== --}}
    @include('partials.early_warning_card')

    {{-- ===== PANEL FILTER LENGKAP ===== --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-header border-0 rounded-top-4 py-3 px-4" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-success d-flex align-items-center gap-2">
                    <i class="bi bi-funnel-fill"></i> Filter Pencarian Lanjutan
                </span>
                <a href="{{ route('pengguna.verifikasi_laporan.index', ['reset' => 1]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="sessionStorage.removeItem('verifikasi_active_tab')">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('pengguna.verifikasi_laporan.index') }}" id="filterForm">

                {{-- Baris 1: Filter Wilayah & Periode --}}
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <p class="fw-bold small text-muted mb-2 text-uppercase" style="letter-spacing:.5px;">
                            <i class="bi bi-geo me-1 text-primary"></i> Filter Wilayah & Periode
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                            <select name="bulan" class="form-select border-start-0 ps-0" onchange="this.form.submit()">
                                @foreach([
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ] as $num => $name)
                                    <option value="{{ $num }}" {{ $bulanInput == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">Kota / Kabupaten</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-building text-muted"></i></span>
                            <select name="kota" class="form-select border-start-0 ps-0" onchange="this.form.submit()">
                                <option value="">-- Pilih Kota --</option>
                                @foreach($kotaList as $k)
                                    <option value="{{ $k }}" {{ $kotaFilter == $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">Kecamatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                            <select name="kecamatan" class="form-select border-start-0 ps-0"
                                    {{ !$kotaFilter ? 'disabled' : '' }} onchange="this.form.submit()">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatanList as $kec)
                                    <option value="{{ $kec }}" {{ $kecFilter == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>



            </form>
        </div>
    </div>



    {{-- ===== KONTEN UTAMA ===== --}}
    @if(!$kotaFilter)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <div class="mb-3" style="font-size:4rem;">🏙️</div>
                <h5 class="text-muted fw-semibold">Pilih Kota/Kabupaten terlebih dahulu</h5>
                <p class="text-muted small">Gunakan filter di atas untuk mulai melihat laporan Puskesmas.</p>
            </div>
        </div>

    @elseif($puskesmasList->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <div class="mb-3" style="font-size:4rem;">🔍</div>
                <h5 class="text-muted fw-semibold">Tidak ada data ditemukan</h5>
                <p class="text-muted small">Coba ubah filter pencarian Anda.</p>
            </div>
        </div>

    @else
        {{-- BUNGKUS DENGAN NAV TABS --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0 flex-nowrap overflow-x-auto" id="verifikasiTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 px-4 border-0 rounded-0 fw-semibold text-secondary d-flex align-items-center gap-2" 
                            id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab" 
                            style="border-bottom: 3px solid transparent !important;" 
                            onclick="resetTabs(); this.style.borderBottomColor='#0f766e'; this.classList.remove('text-secondary'); this.classList.add('text-teal');">
                            <i class="bi bi-list-check fs-5"></i> Status Laporan Masuk
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-secondary d-flex align-items-center gap-2" 
                            id="matriks-tab" data-bs-toggle="tab" data-bs-target="#matriks" type="button" role="tab"
                            style="border-bottom: 3px solid transparent !important;" 
                            onclick="resetTabs(); this.style.borderBottomColor='#0f766e'; this.classList.remove('text-secondary'); this.classList.add('text-teal');">
                            <i class="bi bi-grid-3x3-gap-fill fs-5"></i> Matriks Sebaran Penyakit
                        </button>
                    </li>
                    <!-- <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-secondary d-flex align-items-center gap-2" 
                            id="demografi-tab" data-bs-toggle="tab" data-bs-target="#demografi" type="button" role="tab"
                            style="border-bottom: 3px solid transparent !important;" 
                            onclick="resetTabs(); this.style.borderBottomColor='#0f766e'; this.classList.remove('text-secondary'); this.classList.add('text-teal');">
                            <i class="bi bi-person-lines-fill fs-5"></i> Demografi Usia
                        </button>
                    </li> -->
                </ul>
            </div>

            <div class="card-body p-0 bg-light bg-opacity-25">
                <div class="tab-content" id="verifikasiTabContent">
                    
                    {{-- TAB 1: STATUS LAPORAN --}}
                    <div class="tab-pane fade show active" id="status" role="tabpanel">
                        {{-- SUMMARY STATS HEADER --}}
                        <div class="p-4 bg-white border-bottom">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2.5 rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                                            <i class="bi bi-hospital"></i>
                                        </div>
                                        <div>
                                            <span class="d-block text-muted small fw-semibold">Puskesmas</span>
                                            <strong class="fs-5 text-dark">{{ $puskesmasList->count() }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-3">
                                        <div class="bg-info bg-opacity-10 text-info p-2.5 rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div>
                                            <span class="d-block text-muted small fw-semibold">Total Pasien</span>
                                            <strong class="fs-5 text-dark">{{ $puskesmasList->sum('jumlah_data') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25 d-flex align-items-center gap-3">
                                        <div class="bg-danger text-white p-2.5 rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div>
                                            <span class="d-block text-danger small fw-bold">Risiko Tinggi</span>
                                            <strong class="fs-5 text-danger">{{ $puskesmasList->sum('jml_risiko_tinggi') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25 d-flex align-items-center gap-3">
                                        <div class="bg-warning text-dark p-2.5 rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                        </div>
                                        <div>
                                            <span class="d-block text-dark small fw-bold">Dicurigai PTM</span>
                                            <strong class="fs-5 text-dark">{{ $puskesmasList->sum('jml_dicurigai') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 bg-white" style="font-size:13px;">
                                <thead>
                                    <tr class="bg-light text-uppercase text-muted" style="font-size:11px; letter-spacing:0.5px;">
                                        <th class="ps-4 py-3">Puskesmas</th>
                                        <th class="py-3">Kecamatan</th>
                                        <th class="text-center py-3">Total &amp; Rincian Skrining</th>
                                        <th class="text-center py-3">Status Laporan</th>
                                        <th class="text-center pe-4 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($puskesmasList as $pkm)
                                    @php
                                        $cfg = match($pkm->status_laporan) {
                                            'approved' => ['color'=>'success', 'icon'=>'bi-check-circle-fill', 'label'=>'Laporan Diterima',      'bg'=>'success-subtle'],
                                            'pending'  => ['color'=>'success', 'icon'=>'bi-check-circle-fill', 'label'=>'Laporan Diterima',      'bg'=>'success-subtle'],
                                            'draft'    => ['color'=>'secondary','icon'=>'bi-file-earmark-fill','label'=>'Ada Data, Belum Kirim', 'bg'=>'secondary-subtle'],
                                            default    => ['color'=>'danger',  'icon'=>'bi-x-circle-fill',    'label'=>'Belum Ada Laporan',      'bg'=>'danger-subtle'],
                                        };
                                    @endphp
                                    <tr class="border-bottom">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <div style="width:8px; height:8px; background:var(--bs-{{ $cfg['color'] }}); border-radius:50%; flex-shrink:0;"></div>
                                                <span class="fw-bold text-dark fs-6">{{ $pkm->nama_puskesmas }}</span>
                                                @php
                                                    $alertKec = collect($earlyWarningData['alerts'] ?? [])->firstWhere('kecamatan', $pkm->kecamatan);
                                                @endphp
                                                @if($alertKec)
                                                    <span class="badge bg-rose-100 text-rose-700 border border-rose-200 rounded-pill px-2.5 py-0.5" style="font-size: 0.68rem; font-weight:700;" title="Peringatan Dini: Lonjakan tren kasus di Kec. {{ $pkm->kecamatan }} (+{{ $alertKec['persentase'] }}%)">
                                                        <i class="bi bi-shield-fill-exclamation text-danger me-1"></i>Wilayah Lonjakan (+{{ $alertKec['persentase'] }}%)
                                                    </span>
                                                @elseif(($pkm->jml_risiko_tinggi ?? 0) >= 3)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight:600;" title="Memiliki {{ $pkm->jml_risiko_tinggi }} Kasus Risiko Tinggi">
                                                        <i class="bi bi-fire text-danger me-1"></i>Kasus Risiko Tinggi
                                                    </span>
                                                @endif
                                                @if(($pkm->jml_laporan_monitoring ?? 0) > 0)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-pill px-2.5 py-1" style="font-size: 0.68rem; font-weight:600; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalDetailMonitoringPkm{{ $pkm->id }}" title="Klik untuk melihat Detail Laporan Hasil Monitoring (5 Ringkasan Utama)">
                                                        <i class="bi bi-check-circle-fill text-success me-1"></i>Sudah Dimonitoring <i class="bi bi-eye-fill ms-1"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-muted py-3">{{ $pkm->kecamatan }}</td>
                                        <td class="text-center py-3">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div class="mb-1">
                                                    <span class="fw-bold fs-6 text-dark">{{ $pkm->jumlah_data ?: '0' }}</span>
                                                    <span class="text-muted small">Pasien</span>
                                                </div>
                                                @if(($pkm->jml_risiko_tinggi ?? 0) > 0 || ($pkm->jml_dicurigai ?? 0) > 0 || ($pkm->jml_normal ?? 0) > 0)
                                                <div class="d-flex justify-content-center gap-1.5 flex-wrap">
                                                    @if(($pkm->jml_risiko_tinggi ?? 0) > 0)
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight:600;" title="{{ $pkm->jml_risiko_tinggi }} Pasien Risiko Tinggi">
                                                            <i class="bi bi-exclamation-circle-fill me-1 text-danger"></i>{{ $pkm->jml_risiko_tinggi }} Risiko Tinggi
                                                        </span>
                                                    @endif
                                                    @if(($pkm->jml_dicurigai ?? 0) > 0)
                                                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight:600;" title="{{ $pkm->jml_dicurigai }} Pasien Dicurigai PTM">
                                                            <i class="bi bi-info-circle-fill me-1 text-warning"></i>{{ $pkm->jml_dicurigai }} Dicurigai
                                                        </span>
                                                    @endif
                                                    @if(($pkm->jml_normal ?? 0) > 0)
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight:600;" title="{{ $pkm->jml_normal }} Pasien Normal">
                                                            <i class="bi bi-check-circle-fill me-1 text-success"></i>{{ $pkm->jml_normal }} Normal
                                                        </span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $cfg['bg'] }} text-{{ $cfg['color'] }} border border-{{ $cfg['color'] }}-subtle rounded-pill px-2 py-1">
                                                <i class="bi {{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            @if(in_array($pkm->status_laporan, ['pending', 'approved']))
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('pengguna.verifikasi_laporan.show', [
                                                    'puskesmas' => $pkm->id,
                                                    'bulan'     => $bulanInput,
                                                    'kota'      => $kotaFilter,
                                                    'kecamatan' => $kecFilter,
                                                ]) }}"
                                                   class="btn btn-sm btn-primary rounded-pill px-3">
                                                    <i class="bi bi-eye me-1"></i> Tinjau
                                                </a>
                                                <a href="{{ route('pengguna.laporan_monitoring.index', ['puskesmas_id' => $pkm->id]) }}"
                                                   class="btn btn-sm btn-teal rounded-pill px-3 fw-semibold shadow-sm"
                                                   title="Buat Laporan Hasil Monitoring untuk {{ Str::startsWith($pkm->nama_puskesmas ?? '', 'Puskesmas') ? $pkm->nama_puskesmas : 'Puskesmas ' . $pkm->nama_puskesmas }}">
                                                    <i class="bi bi-file-earmark-text me-1"></i> Laporan Monitoring
                                                </a>
                                            </div>
                                            @elseif($pkm->status_laporan === 'draft')
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                <a href="{{ route('pengguna.verifikasi_laporan.show', [
                                                    'puskesmas' => $pkm->id,
                                                    'bulan'     => $bulanInput,
                                                    'kota'      => $kotaFilter,
                                                    'kecamatan' => $kecFilter,
                                                ]) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5" title="Pratinjau Data Draf">
                                                    <i class="bi bi-eye me-1"></i> Tinjau
                                                </a>
                                                <form action="{{ route('pengguna.verifikasi_laporan.pengingat', $pkm->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Kirim notifikasi pengingat ke {{ Str::startsWith($pkm->nama_puskesmas ?? '', 'Puskesmas') ? $pkm->nama_puskesmas : 'Puskesmas ' . $pkm->nama_puskesmas }} agar segera mengajukan laporan?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning rounded-pill px-2.5 fw-semibold text-dark shadow-sm" title="Ingatkan Petugas untuk Mengirim Laporan">
                                                        <i class="bi bi-bell-fill me-1"></i> Kirim Pengingat
                                                    </button>
                                                </form>
                                                <a href="{{ route('pengguna.laporan_monitoring.index', ['puskesmas_id' => $pkm->id]) }}"
                                                   class="btn btn-sm btn-teal rounded-pill px-2.5 fw-semibold shadow-sm"
                                                   title="Buat Laporan Hasil Monitoring">
                                                    <i class="bi bi-file-earmark-text me-1"></i> Laporan Monitoring
                                                </a>
                                            </div>
                                            @else
                                            <form action="{{ route('pengguna.verifikasi_laporan.pengingat', $pkm->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Kirim notifikasi pengingat ke {{ Str::startsWith($pkm->nama_puskesmas ?? '', 'Puskesmas') ? $pkm->nama_puskesmas : 'Puskesmas ' . $pkm->nama_puskesmas }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark shadow-sm">
                                                    <i class="bi bi-bell-fill me-1"></i> Kirim Pengingat
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- MODAL DETAIL LAPORAN MONITORING (5 POIN UTAMA) --}}
                                    @if(($pkm->jml_laporan_monitoring ?? 0) > 0 && isset($pkm->laporan_monitoring_terakhir))
                                    @php $lapMon = $pkm->laporan_monitoring_terakhir; @endphp
                                    <div class="modal fade" id="modalDetailMonitoringPkm{{ $pkm->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg">
                                                <div class="modal-header border-bottom-0 bg-teal text-white rounded-top-4 p-4">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-medical me-2"></i>Detail Laporan Hasil Monitoring</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="p-3 bg-teal bg-opacity-10 border border-teal-subtle rounded-3 mb-4 d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="d-block text-muted small fw-bold">Puskesmas Tujuan</span>
                                                            <h6 class="fw-bold text-teal mb-0">{{ $pkm->nama_puskesmas }}</h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <span class="d-block text-muted small fw-bold">Status Laporan</span>
                                                            @if($lapMon->status_laporan === 'disetujui')
                                                                <span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-check-circle-fill me-1"></i> Disetujui Kepala</span>
                                                            @elseif($lapMon->status_laporan === 'pending')
                                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan</span>
                                                            @else
                                                                <span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 border">
                                                                <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-calendar-event me-1 text-teal"></i> Tanggal Kunjungan Lapangan</label>
                                                                <p class="fw-bold mb-0 text-dark">{{ \Carbon\Carbon::parse($lapMon->tanggal_kunjungan ?? $lapMon->created_at)->translatedFormat('d F Y') }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 border">
                                                                <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-tag-fill me-1 text-teal"></i> Kategori Temuan Pemantauan</label>
                                                                <p class="fw-bold mb-0 text-dark">{{ $lapMon->kategori_temuan ?? 'Pemantauan Wilayah Rutin' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                                        <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-file-earmark-text-fill me-1 text-teal"></i> Judul Laporan / Kesimpulan Utama</label>
                                                        <p class="fw-bold mb-0 text-dark">{{ $lapMon->judul_laporan }}</p>
                                                    </div>

                                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                                        <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-card-text me-1 text-teal"></i> Deskripsi Temuan Lapangan</label>
                                                        <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $lapMon->deskripsi_temuan }}</p>
                                                    </div>

                                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                                        <label class="form-label fw-bold text-muted small mb-1"><i class="bi bi-lightbulb-fill me-1 text-teal"></i> Rekomendasi & Usulan Tindakan</label>
                                                        <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $lapMon->rekomendasi_tindakan }}</p>
                                                    </div>

                                                    @if($lapMon->catatan_kepala)
                                                    <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                                                        <label class="form-label fw-bold text-warning mb-1"><i class="bi bi-chat-quote-fill me-1"></i> Catatan Kepala P2PTM</label>
                                                        <p class="mb-0 text-dark fst-italic">"{{ $lapMon->catatan_kepala }}"</p>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer border-top-0 bg-light p-3">
                                                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: MATRIKS SEBARAN PENYAKIT --}}
                    <div class="tab-pane fade" id="matriks" role="tabpanel">
                        <div class="p-4 bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    @php
                                        $bulanIndo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                                        $namaBulan = $bulanIndo[\Carbon\Carbon::parse($startDate)->format('m')] . ' ' . \Carbon\Carbon::parse($startDate)->format('Y');
                                    @endphp
                                    <h6 class="fw-bold text-gray-800 mb-1">Matriks Sebaran Penyakit (Bulan: {{ $namaBulan }})</h6>
                                    <p class="text-muted small mb-0">Distribusi PTM berdasarkan Wilayah, Kelompok Usia, dan Jenis Penyakit untuk Kota/Kab. {{ $kotaFilter }}.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pengguna.verifikasi_laporan.export_excel', ['bulan' => $bulanInput, 'kota' => $kotaFilter, 'kecamatan' => $kecFilter]) }}" class="btn btn-sm btn-success fw-semibold shadow-sm rounded-pill px-3">
                                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
                                    </a>
                                    {{-- <a href="{{ route('pengguna.verifikasi_laporan.cetak_pdf', ['bulan' => $bulanInput, 'kota' => $kotaFilter, 'kecamatan' => $kecFilter]) }}" target="_blank" class="btn btn-sm btn-danger fw-semibold shadow-sm rounded-pill px-3">
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF
                                    </a> --}}
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive bg-white" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-hover table-bordered text-center align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="sticky-top" style="background-color: #f8fafc; z-index: 2;">
                                    <tr>
                                        <th rowspan="2" class="align-middle" style="background-color: #0f766e; color: white; border-color: #0d9488; width: 5%;">No</th>
                                        <th rowspan="2" class="align-middle text-start" style="background-color: #0f766e; color: white; border-color: #0d9488; min-width: 150px;">Wilayah Puskesmas</th>
                                        <th colspan="{{ count($penyakitList) }}" style="background-color: #f59e0b; color: white; border-color: #d97706;">Berdasarkan Jenis Penyakit Terdeteksi</th>
                                        <th rowspan="2" class="align-middle" style="background-color: #10b981; color: white; border-color: #059669;">Total Pasien</th>
                                    </tr>
                                    <tr>
                                        @foreach($penyakitList as $p)
                                            <th style="background-color: #fef3c7; color: #b45309; min-width: 100px;">{{ $p }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($matriksLaporan as $row)
                                        <tr>
                                            <td class="fw-semibold text-gray-500">{{ $loop->iteration }}</td>
                                            <td class="text-start fw-bold text-gray-800">{{ $row['puskesmas'] }}</td>
                                            
                                            @foreach($penyakitList as $p)
                                                <td class="text-gray-600 {{ $row['ptm'][$p] > 0 ? 'fw-bold text-danger bg-danger bg-opacity-10' : '' }}">{{ $row['ptm'][$p] }}</td>
                                            @endforeach
                                            
                                            <td class="fw-bold fs-6 text-success bg-light">{{ $row['total_pasien'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 2 + count($penyakitList) + 1 }}" class="text-muted py-5 text-center">
                                                <i class="bi bi-inbox fs-1 text-gray-300 d-block mb-2"></i>
                                                Belum ada data skrining PTM yang masuk pada periode ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($matriksLaporan) > 0)
                                <tfoot class="fw-bold sticky-bottom bg-light">
                                    <tr>
                                        <td colspan="2" class="text-end py-3">TOTAL KESELURUHAN :</td>
                                        
                                        @foreach($penyakitList as $p)
                                            @php
                                                $totalPenyakit = $matriksLaporan->sum(function($row) use ($p) {
                                                    return $row['ptm'][$p] ?? 0;
                                                });
                                            @endphp
                                            <td class="{{ $totalPenyakit > 0 ? 'text-danger fs-6' : 'text-gray-500' }}">{{ $totalPenyakit }}</td>
                                        @endforeach

                                        <td class="fs-6 text-success">{{ $matriksLaporan->sum('total_pasien') }}</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: DEMOGRAFI USIA --}}
                    <div class="tab-pane fade" id="demografi" role="tabpanel">
                        <div class="p-4 bg-white border-bottom">
                            <h6 class="fw-bold text-gray-800 mb-1">Demografi Usia Pasien PTM</h6>
                            <p class="text-muted small mb-0">Total pasien terdeteksi berdasarkan pengelompokan usia untuk Kota/Kab. {{ $kotaFilter }}.</p>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card bg-info bg-opacity-10 border-info border-opacity-25 shadow-sm rounded-3">
                                        <div class="card-body text-center">
                                            <h6 class="text-info fw-bold mb-1">REMAJA (< 18 Thn)</h6>
                                            <h2 class="fw-bold text-dark mb-0">{{ $kelompokUsia['remaja'] }}</h2>
                                            <small class="text-muted">Pasien</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 shadow-sm rounded-3">
                                        <div class="card-body text-center">
                                            <h6 class="text-primary fw-bold mb-1">DEWASA (18-44 Thn)</h6>
                                            <h2 class="fw-bold text-dark mb-0">{{ $kelompokUsia['dewasa'] }}</h2>
                                            <small class="text-muted">Pasien</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning bg-opacity-10 border-warning border-opacity-25 shadow-sm rounded-3">
                                        <div class="card-body text-center">
                                            <h6 class="text-warning text-darken fw-bold mb-1">PRA LANSIA (45-59 Thn)</h6>
                                            <h2 class="fw-bold text-dark mb-0">{{ $kelompokUsia['pra_lansia'] }}</h2>
                                            <small class="text-muted">Pasien</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger bg-opacity-10 border-danger border-opacity-25 shadow-sm rounded-3">
                                        <div class="card-body text-center">
                                            <h6 class="text-danger fw-bold mb-1">LANSIA (>= 60 Thn)</h6>
                                            <h2 class="fw-bold text-dark mb-0">{{ $kelompokUsia['lansia'] }}</h2>
                                            <small class="text-muted">Pasien</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>

<script>
function resetTabs() {
    document.querySelectorAll('#verifikasiTab .nav-link').forEach(function(el) {
        el.style.borderBottomColor = 'transparent';
        el.classList.remove('text-teal');
        el.classList.add('text-secondary');
    });
}

function switchVerifikasiTab(tabId) {
    resetTabs();
    const btn = document.getElementById(tabId);
    if(btn) {
        btn.style.borderBottomColor = '#0f766e';
        btn.classList.remove('text-secondary');
        btn.classList.add('text-teal');
        sessionStorage.setItem('verifikasi_active_tab', tabId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#verifikasiTab .nav-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            switchVerifikasiTab(this.id);
        });
    });

    const savedTabId = sessionStorage.getItem('verifikasi_active_tab');
    if (savedTabId && document.getElementById(savedTabId)) {
        const tabTrigger = new bootstrap.Tab(document.getElementById(savedTabId));
        tabTrigger.show();
        switchVerifikasiTab(savedTabId);
    } else {
        let activeTab = document.querySelector('#verifikasiTab .nav-link.active');
        if(activeTab) {
            switchVerifikasiTab(activeTab.id);
        }
    }
});
</script>

<style>
.text-teal { color: #0f766e !important; }
.text-darken { color: #997404 !important; } /* for warning text contrast */
.nav-tabs .nav-link { margin-bottom: -1px; }
.nav-tabs .nav-link:hover { border-bottom-color: #0f766e !important; opacity: 0.8; }
</style>
@endsection
