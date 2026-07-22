@extends('layouts.master')

@section('title', 'Pemantauan Logistik PTM')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px; margin: auto;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-gray-800 mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i> Pemantauan Logistik
            </h3>
            <p class="text-muted small mb-0">Kelola dan pantau ketersediaan logistik alat kesehatan di setiap Puskesmas.</p>
        </div>
        <a href="{{ route('pengguna.logistik.create') }}" class="btn btn-success fw-semibold rounded-pill px-4 shadow-sm hover-lift">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data Logistik
        </a>
    </div>

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Puskesmas</th>
                            <th class="text-center">Strip Gula</th>
                            <th class="text-center">Strip Kolesterol</th>
                            <th class="text-center">Strip Asam Urat</th>
                            <th class="text-center">Blood Lancet</th>
                            <th class="text-center">Kapas Alkohol</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logistikData as $item)
                        <tr class="border-bottom">
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $item->puskesmas->nama_puskesmas }}</td>
                            
                            {{-- Pengecekan Kritis (< 50) --}}
                            <td class="text-center">
                                @if($item->strip_gula < 50)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $item->strip_gula }}
                                    </span>
                                @else
                                    <span class="fw-medium text-success">{{ $item->strip_gula }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->strip_kolesterol < 50)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $item->strip_kolesterol }}
                                    </span>
                                @else
                                    <span class="fw-medium text-success">{{ $item->strip_kolesterol }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->strip_asam_urat < 50)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $item->strip_asam_urat }}
                                    </span>
                                @else
                                    <span class="fw-medium text-success">{{ $item->strip_asam_urat }}</span>
                                @endif
                            </td>
                            <td class="text-center fw-medium text-gray-700">{{ $item->lancet }}</td>
                            <td class="text-center fw-medium text-gray-700">{{ $item->kapas_alkohol }}</td>
                            
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('pengguna.logistik.cetak_bast', $item->id) }}" class="btn btn-sm btn-outline-info rounded-circle" style="width:32px; height:32px; padding:0; line-height:30px;" title="Cetak Berita Acara (BAST)" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="{{ route('pengguna.logistik.edit', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px; height:32px; padding:0; line-height:30px;" title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pengguna.logistik.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data logistik ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width:32px; height:32px; padding:0; line-height:30px;" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-gray-300"></i>
                                Belum ada data logistik.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-lift { transition: transform 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
</style>
@endpush
