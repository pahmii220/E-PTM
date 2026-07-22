@extends('layouts.master')
@section('title', 'Laporan Logistik & Alokasi Alkes PTM')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="text-2xl font-semibold text-gray-800">
                <i class="bi bi-box-seam text-warning me-2"></i>Laporan Logistik &amp; Alokasi Alkes PTM
            </h2>
            <p class="text-gray-500 text-sm mt-1">Memantau riwayat alokasi alat kesehatan dan logistik yang disiapkan pegawai berdasarkan Laporan Hasil Monitoring disetujui.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('kepala.laporan.perlengkapan_tugas') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Bulan Pelaksanaan</label>
                    <select name="month" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('kepala.laporan.perlengkapan_tugas') }}" class="btn btn-light px-4">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Tabel Laporan Alokasi Logistik PTM</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 45px;">No</th>
                        <th style="min-width: 180px;">Puskesmas Tujuan &amp; Pegawai</th>
                        <th style="min-width: 250px;">Judul Laporan &amp; Rekomendasi</th>
                        <th style="min-width: 140px;">Tanggal</th>
                        <th style="min-width: 250px;">Rincian Alat / Logistik</th>
                        <th class="text-center" style="width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataPerlengkapan ?? [] as $index => $row)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                @if($row->laporanMonitoring)
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-hospital-fill text-danger me-1"></i>
                                        Puskesmas {{ $row->laporanMonitoring->puskesmas->nama_puskesmas ?? '-' }}
                                    </div>
                                    <small class="text-muted d-block">Pelapor: {{ $row->laporanMonitoring->pegawai->nama_lengkap ?? 'Pegawai Dinkes' }}</small>
                                @else
                                    <div class="fw-bold text-dark">{{ $row->suratTugas->lokasi_tujuan ?? '-' }}</div>
                                    <small class="text-muted d-block">SPT: {{ $row->suratTugas->nomor_surat ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($row->laporanMonitoring)
                                    <div class="fw-semibold text-primary">{{ $row->laporanMonitoring->judul_laporan }}</div>
                                    <small class="text-secondary text-truncate-2" style="font-size: 0.825rem; max-width: 280px;" title="{{ $row->laporanMonitoring->rekomendasi_tindakan }}">
                                        "{{ $row->laporanMonitoring->rekomendasi_tindakan }}"
                                    </small>
                                @else
                                    <small class="text-secondary">{{ $row->suratTugas->maksud_tujuan ?? '-' }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold d-inline-block">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <ul class="mb-0 ps-3" style="font-size: 0.875rem;">
                                    @forelse($row->items as $item)
                                        <li class="fw-medium text-dark">{{ $item->nama_barang }} <span class="badge bg-light text-dark border ms-1">{{ $item->jumlah }} {{ $item->satuan ?? 'Unit' }}</span></li>
                                    @empty
                                        <li><i class="text-muted small">Tidak ada rincian</i></li>
                                    @endforelse
                                </ul>
                                @if($row->catatan)
                                    <small class="text-muted fst-italic d-block mt-1"><i class="bi bi-chat-left-text me-1"></i> Catatan: {{ $row->catatan }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('kepala.laporan.perlengkapan_tugas.cetak', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" style="width:34px; height:34px; padding:0; line-height:32px;" title="Cetak Dokumen Laporan Logistik">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada data laporan alokasi logistik PTM.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
