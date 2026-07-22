@extends('layouts.master')

@section('title', 'Monitoring Laporan PTM')

@section('content')
<div class="container-fluid py-4" style="max-width:1300px; margin:auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i> Monitoring Laporan PTM
            </h2>
            <p class="text-muted mb-0 small">Pantau status pengiriman laporan dari setiap Puskesmas per bulan.</p>
        </div>
        {{-- FILTER BULAN --}}
        <form method="GET" action="{{ route('pengguna.monitoring.index') }}" class="d-flex gap-2 align-items-end">
            <div>
                <label class="form-label fw-bold small text-muted mb-1">Periode (Bulan)</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    {{-- ====================== SUMMARY CARDS ====================== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="fs-1 fw-bold text-dark">{{ $totalPkm }}</div>
                <div class="text-muted small fw-semibold">Total Puskesmas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top: 4px solid #0d6efd !important;">
                <div class="fs-1 fw-bold text-primary">{{ $totalSudahKirim }}</div>
                <div class="text-muted small fw-semibold">Sudah Mengirim</div>
                <div class="mt-1">
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-primary" style="width:{{ $totalPkm > 0 ? round(($totalSudahKirim/$totalPkm)*100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $totalPkm > 0 ? round(($totalSudahKirim/$totalPkm)*100) : 0 }}% dari total</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top: 4px solid #ffc107 !important;">
                <div class="fs-1 fw-bold text-warning">{{ $totalDraft }}</div>
                <div class="text-muted small fw-semibold">Ada Data, Belum Kirim</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top: 4px solid #dc3545 !important;">
                <div class="fs-1 fw-bold text-danger">{{ $totalBelum }}</div>
                <div class="text-muted small fw-semibold">Belum Ada Data</div>
            </div>
        </div>
    </div>

    {{-- LEGENDA --}}
    <div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
        <span class="fw-bold text-muted small">Keterangan Status:</span>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-check-circle-fill me-1"></i> Diverifikasi
        </span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-hourglass-split me-1"></i> Sudah Kirim (Pending)
        </span>
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-file-earmark me-1"></i> Ada Data, Belum Dikirim
        </span>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-x-circle me-1"></i> Belum Ada Laporan
        </span>
    </div>

    {{-- ====================== PER KOTA ====================== --}}
    @foreach($perKota as $namaKota => $puskesmasDiKota)
    @php
        $totalKota    = $puskesmasDiKota->count();
        $sudahKota    = $puskesmasDiKota->whereIn('status_laporan', ['pending','approved'])->count();
        $persen       = $totalKota > 0 ? round(($sudahKota/$totalKota)*100) : 0;
        $perKecamatan = $puskesmasDiKota->groupBy('kecamatan');
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        {{-- Header Kota --}}
        <div class="card-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-white">
                    <h5 class="mb-1 fw-bold">
                        <i class="bi bi-building-fill me-2"></i>{{ $namaKota ?? 'Tidak diketahui' }}
                    </h5>
                    <small class="opacity-75">{{ $totalKota }} Puskesmas · {{ $perKecamatan->count() }} Kecamatan</small>
                </div>
                <div class="text-end">
                    <div class="text-white fw-bold fs-5">{{ $sudahKota }}/{{ $totalKota }}</div>
                    <small class="text-white opacity-75">Puskesmas telah melapor</small>
                    <div class="progress mt-1" style="height:8px; min-width:140px; background:rgba(255,255,255,0.25);">
                        <div class="progress-bar bg-{{ $persen == 100 ? 'success' : ($persen >= 50 ? 'warning' : 'danger') }}"
                             style="width:{{ $persen }}%; transition: width 1s;">
                        </div>
                    </div>
                    <small class="text-white opacity-75">{{ $persen }}% laporan masuk</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- PER KECAMATAN --}}
            @foreach($perKecamatan as $namaKec => $puskesmasDiKec)
            @php
                $totalKec = $puskesmasDiKec->count();
                $sudahKec = $puskesmasDiKec->whereIn('status_laporan', ['pending','approved'])->count();
            @endphp
            <div class="border-bottom">
                {{-- Sub-header Kecamatan --}}
                <div class="px-4 py-2 bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-secondary small">
                        <i class="bi bi-geo-alt me-1"></i>Kec. {{ $namaKec ?? '-' }}
                    </span>
                    <span class="badge {{ $sudahKec == $totalKec ? 'bg-success' : ($sudahKec > 0 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill">
                        {{ $sudahKec }}/{{ $totalKec }} melapor
                    </span>
                </div>

                {{-- Daftar Puskesmas di Kecamatan ini --}}
                @foreach($puskesmasDiKec as $pkm)
                @php
                    $icon  = match($pkm->status_laporan) {
                        'approved' => ['icon'=>'bi-check-circle-fill','color'=>'text-success','label'=>'Diverifikasi','badge'=>'success'],
                        'pending'  => ['icon'=>'bi-hourglass-split','color'=>'text-primary','label'=>'Menunggu Verifikasi','badge'=>'primary'],
                        'draft'    => ['icon'=>'bi-file-earmark-fill','color'=>'text-warning','label'=>'Belum Dikirim','badge'=>'warning'],
                        default    => ['icon'=>'bi-x-circle-fill','color'=>'text-danger','label'=>'Belum Ada Laporan','badge'=>'danger'],
                    };
                @endphp
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom border-light hover-row">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi {{ $icon['icon'] }} {{ $icon['color'] }} fs-5"></i>
                        <div>
                            <div class="fw-semibold text-dark">{{ $pkm->nama_puskesmas }}</div>
                            <small class="text-muted">
                                @if($pkm->jumlah_data > 0)
                                    {{ $pkm->jumlah_data }} data pemeriksaan pada periode ini
                                @else
                                    Tidak ada data pada periode ini
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-{{ $icon['badge'] }}-subtle text-{{ $icon['badge'] }} border border-{{ $icon['badge'] }}-subtle rounded-pill px-3 py-2">
                            {{ $icon['label'] }}
                        </span>
                        @if(in_array($pkm->status_laporan, ['pending','approved']))
                        <a href="{{ route('pengguna.verifikasi_laporan.show', ['puskesmas' => $pkm->id, 'bulan' => $bulan]) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

</div>

<style>
.hover-row:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
