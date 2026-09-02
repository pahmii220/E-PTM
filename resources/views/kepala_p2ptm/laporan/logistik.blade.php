@extends('layouts.master')
@section('title', 'Laporan Logistik & Alokasi Alkes PTM')

@section('content')
<div class="container-fluid py-4" style="max-width: 1450px; margin: auto;">
    
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-box-seam-fill text-warning me-2"></i>Laporan Logistik &amp; Alokasi Alkes PTM
            </h2>
            <p class="text-muted mb-0">Memantau riwayat alokasi alat kesehatan dan logistik yang disiapkan pegawai berdasarkan Laporan Hasil Monitoring disetujui.</p>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('kepala.laporan.perlengkapan_tugas') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label text-muted small fw-bold mb-1">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control border-2" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label text-muted small fw-bold mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control border-2" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label text-muted small fw-bold mb-1">Bulan Pelaksanaan</label>
                    <select name="month" class="form-select border-2">
                        <option value="">-- Semua Bulan --</option>
                        @php
                            $bulanIndo = ['1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                        @endphp
                        @foreach($bulanIndo as $num => $nama)
                            <option value="{{ sprintf('%02d', $num) }}" {{ request('month') == sprintf('%02d', $num) ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('kepala.laporan.perlengkapan_tugas') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-list-check me-2 text-primary"></i>Tabel Laporan Alokasi Logistik PTM
            </h5>
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                Total: {{ count($dataPerlengkapan ?? []) }} Data
            </span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="text-center py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="min-width: 130px;">Tanggal</th>
                        <th class="py-3" style="min-width: 200px;">Puskesmas Tujuan &amp; Pelapor</th>
                        <th class="py-3" style="min-width: 230px;">Judul Laporan &amp; Rekomendasi</th>
                        <th class="py-3" style="min-width: 220px;">Rincian Alat / Logistik</th>
                        <th class="py-3" style="min-width: 240px;">Catatan / Keterangan</th>
                        <th class="text-center py-3" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataPerlengkapan ?? [] as $index => $row)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            
                            <!-- Tanggal -->
                            <td>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-semibold d-inline-block shadow-sm">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }} WITA
                                </small>
                            </td>

                            <!-- Puskesmas & Pelapor -->
                            <td>
                                @if($row->laporanMonitoring)
                                    @php
                                        $pkmNama = $row->laporanMonitoring->puskesmas->nama_puskesmas ?? '-';
                                        if(!Str::startsWith($pkmNama, 'Puskesmas')) {
                                            $pkmNama = 'Puskesmas ' . $pkmNama;
                                        }
                                        $kec = $row->laporanMonitoring->puskesmas->kecamatan ?? '-';
                                    @endphp
                                    <div class="fw-bold text-dark mb-0.5">
                                        <i class="bi bi-hospital-fill text-danger me-1"></i>
                                        {{ $pkmNama }}
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">
                                        Kec. {{ $kec }}
                                    </small>
                                    <small class="text-secondary d-block mt-1" style="font-size: 0.78rem;">
                                        <i class="bi bi-person-fill text-primary me-1"></i>
                                        Pelapor: {{ $row->laporanMonitoring->pegawai->nama_lengkap ?? ($row->laporanMonitoring->pegawai->nama_pegawai ?? 'Pegawai Dinkes') }}
                                    </small>
                                @else
                                    <div class="fw-bold text-dark mb-0.5">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        {{ $row->suratTugas->lokasi_tujuan ?? '-' }}
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.78rem;">
                                        SPT: {{ $row->suratTugas->nomor_surat ?? '-' }}
                                    </small>
                                @endif
                            </td>

                            <!-- Judul Laporan & Rekomendasi -->
                            <td>
                                @if($row->laporanMonitoring)
                                    <div class="fw-semibold text-primary mb-1 text-wrap" style="line-height: 1.35; max-width: 230px;">
                                        {{ $row->laporanMonitoring->judul_laporan }}
                                    </div>
                                    <div class="text-secondary text-wrap p-2 rounded-3 bg-light border border-light-subtle" style="font-size: 0.8rem; line-height: 1.35; max-width: 230px;" title="{{ $row->laporanMonitoring->rekomendasi_tindakan }}">
                                        <i class="bi bi-chat-left-quote text-warning me-1"></i>
                                        "{{ Str::limit($row->laporanMonitoring->rekomendasi_tindakan, 90) }}"
                                    </div>
                                @else
                                    <div class="text-secondary text-wrap" style="font-size: 0.825rem; max-width: 230px;">
                                        {{ $row->suratTugas->maksud_tujuan ?? '-' }}
                                    </div>
                                @endif
                            </td>

                            <!-- Rincian Alat / Logistik -->
                            <td style="max-width: 240px;">
                                @php $totalItems = count($row->items); @endphp
                                @if($totalItems > 0)
                                    @php
                                        $firstItem = $row->items->first();
                                        $remainingItems = $row->items->slice(1);
                                    @endphp
                                    <div class="d-flex flex-column gap-1.5" style="max-width: 230px;">
                                        <div class="p-2 rounded-3 bg-white border border-gray-300 shadow-2xs text-dark" style="font-size: 0.8rem; line-height: 1.35;">
                                            <div class="d-flex justify-content-between align-items-start gap-1 flex-wrap">
                                                <span class="fw-semibold text-dark text-wrap me-1">
                                                    <i class="bi bi-box-seam text-warning me-1"></i>{{ $firstItem->nama_barang }}
                                                </span>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold flex-shrink-0" style="font-size: 0.725rem;">
                                                    {{ $firstItem->jumlah }} {{ $firstItem->satuan ?? 'Unit' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($totalItems > 1)
                                            <div class="dropdown">
                                                <button class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold dropdown-toggle shadow-2xs" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false" 
                                                        style="font-size: 0.75rem;">
                                                    +{{ $totalItems - 1 }} item lainnya
                                                </button>
                                                <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2" style="font-size: 0.8rem; min-width: 240px;">
                                                    <li class="dropdown-header fw-bold text-dark border-bottom pb-1.5 mb-1" style="font-size: 0.75rem;">
                                                        <i class="bi bi-boxes text-warning me-1"></i> Rincian Alat Lainnya ({{ $totalItems - 1 }})
                                                    </li>
                                                    @foreach($remainingItems as $remItem)
                                                        <li class="px-2 py-1.5 d-flex justify-content-between align-items-center border-bottom border-light">
                                                            <span class="text-dark fw-medium text-wrap me-2" style="max-width: 150px;">{{ $remItem->nama_barang }}</span>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold flex-shrink-0">
                                                                {{ $remItem->jumlah }} {{ $remItem->satuan ?? 'Unit' }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic"><i class="bi bi-info-circle me-1"></i> Tidak ada rincian item</span>
                                @endif
                            </td>

                            <!-- Catatan / Keterangan Logistik -->
                            <td>
                                @if($row->catatan)
                                    <div class="p-2 rounded-3 bg-warning-subtle text-dark border border-warning-subtle text-wrap" style="font-size: 0.8rem; line-height: 1.35; max-width: 240px;">
                                        <i class="bi bi-sticky-fill text-warning me-1"></i>
                                        {{ $row->catatan }}
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <!-- Aksi Cetak -->
                            <td class="text-center">
                                <a href="{{ route('kepala.laporan.perlengkapan_tugas.cetak', $row->id) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" 
                                   title="Cetak Dokumen Fisik Laporan Logistik">
                                    <i class="bi bi-printer-fill"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
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
