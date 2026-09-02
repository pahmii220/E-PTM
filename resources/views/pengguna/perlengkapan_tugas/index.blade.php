@extends('layouts.master')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: auto;">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-box-seam-fill text-warning me-2"></i>Perlengkapan Logistik Kegiatan &amp; Alokasi Faskes
            </h2>
            <p class="text-muted mb-0">Daftar Laporan Hasil Monitoring yang telah disetujui (ACC) oleh Kepala P2PTM. Siapkan daftar alat medis &amp; logistik yang diusulkan ke Puskesmas.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filter Periode Bulan --}}
            <form method="GET" action="{{ route('pengguna.perlengkapan.index') }}" class="d-flex align-items-center gap-1 bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs fw-semibold text-gray-500 ms-2 me-1"><i class="bi bi-funnel-fill text-warning me-1"></i>Periode:</span>
                <select name="bulan" class="form-select form-select-sm border-0 bg-transparent fw-bold text-xs text-blue-900" style="min-width: 130px; cursor: pointer;" onchange="this.form.submit()">
                    <option value="semua" {{ $bulanInput == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach($listBulanIndo as $valBulan => $labelBulan)
                        <option value="{{ $valBulan }}" {{ $bulanInput == $valBulan ? 'selected' : '' }}>
                            {{ $labelBulan }} {{ $tahunInput }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- CARD TABEL PERLENGKAPAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-check2-circle me-2 text-success"></i>Laporan Hasil Monitoring Disetujui Kepala P2PTM</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem; border-color: #e2e8f0;">
                <thead class="table-light text-secondary">
                    <tr class="align-middle">
                        <th class="text-center py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="min-width: 200px;">Judul Laporan Monitoring</th>
                        <th class="py-3" style="min-width: 180px;">Puskesmas Tujuan</th>
                        <th class="py-3" style="min-width: 250px;">Rekomendasi / Usulan Logistik</th>
                        <th class="py-3" style="min-width: 140px;">Tanggal ACC</th>
                        <th class="text-center py-3" style="width: 130px;">Status Logistik</th>
                        <th class="text-center py-3" style="min-width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanMonitoring as $row)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration + ($laporanMonitoring->currentPage() - 1) * $laporanMonitoring->perPage() }}</td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $row->judul_laporan }}</span>
                                <small class="text-muted"><i class="bi bi-file-earmark-text me-1"></i> ID: #MON-{{ str_pad($row->id, 4, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <span class="text-primary fw-semibold d-block">
                                    <i class="bi bi-hospital-fill text-danger me-1"></i>
                                    {{ $row->puskesmas->nama_puskesmas ?? '-' }}
                                </span>
                                <small class="text-secondary">Kec. {{ $row->puskesmas->kecamatan ?? '-' }}</small>
                            </td>
                            <td>
                                <p class="mb-0 text-secondary text-truncate-2" style="font-size: 0.85rem; max-width: 280px;" title="{{ $row->rekomendasi_tindakan }}">
                                    "{{ $row->rekomendasi_tindakan }}"
                                </p>
                            </td>
                            <td>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold d-inline-block">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $row->tanggal_disetujui ? \Carbon\Carbon::parse($row->tanggal_disetujui)->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($row->perlengkapan)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5"><i class="bi bi-check-circle me-1"></i> Logistik Siap</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2.5 py-1.5"><i class="bi bi-dash-circle me-1"></i> Belum Diisi</span>
                                @endif
                            </td>
                            <td class="text-center py-2">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    @if($row->perlengkapan)
                                        <a href="{{ route('pengguna.perlengkapan.create', $row->id) }}" class="btn btn-xs btn-outline-primary fw-bold">
                                            <i class="bi bi-pencil-square"></i> Edit Alat
                                        </a>
                                        <!-- <a href="{{ route('pengguna.perlengkapan.print', $row->perlengkapan->id) }}" target="_blank" class="btn btn-xs btn-dark fw-bold">
                                            <i class="bi bi-printer"></i> Cetak Daftar
                                        </a> -->
                                    @else
                                        <a href="{{ route('pengguna.perlengkapan.create', $row->id) }}" class="btn btn-xs btn-success text-white fw-bold shadow-sm">
                                            <i class="bi bi-box-seam me-1"></i> + Input Logistik
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada Laporan Hasil Monitoring berstatus disetujui (ACC Kepala) saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($laporanMonitoring->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $laporanMonitoring->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn-xs {
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.3rem;
    }
</style>
@endsection
