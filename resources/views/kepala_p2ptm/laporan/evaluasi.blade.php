@extends('layouts.master')
@section('title', 'Laporan Evaluasi Sistem')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="text-2xl font-semibold text-gray-800">Laporan Evaluasi Sistem (SUS)</h2>
            <p class="text-gray-500 text-sm mt-1">Laporan dari hasil kuesioner pengguna tentang kemudahan sistem.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kepala.laporan.evaluasi.cetak') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-4">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold">Total Responden</h6>
                        <h3 class="mb-0 fw-black text-dark">{{ $totalResponden }} <span class="fs-6 text-muted fw-normal">Petugas Puskesmas</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle {{ $rataRataSkor >= 70 ? 'bg-success' : 'bg-warning' }} bg-opacity-10 p-3 me-4">
                        <i class="bi bi-star text-{{ $rataRataSkor >= 70 ? 'success' : 'warning' }} fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold">Rata-rata Skor SUS</h6>
                        <h3 class="mb-0 fw-black text-dark">{{ $rataRataSkor }} <span class="fs-6 text-{{ $rataRataSkor >= 70 ? 'success' : 'warning' }} fw-bold">({{ $predikat }})</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">Detail Respons Petugas Puskesmas</h5>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Petugas</th>
                    <th>Puskesmas</th>
                    <th class="text-center">Skor SUS</th>
                    <th>Waktu Pengisian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semuaData ?? [] as $index => $sus)
                    <tr>
                        <td class="ps-4">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $sus->user->Nama_Lengkap ?? $sus->user->username ?? 'Anonim' }}</td>
                        <td>
                            @if($sus->user->role_name === 'petugas')
                                {{ optional(optional($sus->user->petugas)->puskesmas)->nama_puskesmas ?? 'Puskesmas (Data Belum Lengkap)' }}
                            @else
                                {{ optional($sus->user->pegawaiDinkes)->bidang ?? 'Dinas Kesehatan' }}
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $sus->skor_sus >= 70 ? 'bg-success' : 'bg-warning' }} rounded-pill px-3 py-2">
                                {{ $sus->skor_sus }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $sus->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data evaluasi sistem.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
