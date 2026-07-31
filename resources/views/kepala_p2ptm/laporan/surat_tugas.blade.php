@extends('layouts.master')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: auto;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-text-fill text-dark me-2"></i>Laporan Surat Perintah Tugas (SPT)
            </h2>
            <p class="text-muted mb-0">Daftar Surat Perintah Tugas luar pegawai yang telah disetujui oleh Kepala P2PTM.</p>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('kepala.laporan.surat_tugas') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control bg-light border-0 rounded-3" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control bg-light border-0 rounded-3" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Bulan Pelaksanaan</label>
                    <select name="month" class="form-select bg-light border-0 rounded-3">
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
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                    @if(request('start_date') || request('end_date') || request('month'))
                        <a href="{{ route('kepala.laporan.surat_tugas') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL SPT DISETUJUI --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-check me-2 text-dark"></i>Daftar Surat Perintah Tugas (Disetujui)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem; border-color: #e2e8f0;">
                <thead class="table-light text-secondary">
                    <tr class="align-middle">
                        <th class="text-center py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="min-width: 180px;">Nomor SPT</th>
                        <th class="py-3" style="min-width: 180px;">Nama Pegawai</th>
                        <th class="py-3" style="min-width: 200px;">Tujuan / Lokasi</th>
                        <th class="py-3" style="min-width: 160px;">Waktu Pelaksanaan</th>
                        <th class="py-3" style="min-width: 250px;">Agenda / Maksud</th>
                        <th class="text-center py-3" style="min-width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTugas as $surat)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration + ($suratTugas->currentPage() - 1) * $suratTugas->perPage() }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-bold">
                                    <i class="bi bi-hash me-1"></i>{{ $surat->nomor_surat ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark d-block mb-0">{{ $surat->pegawai->nama_pegawai ?? '-' }}</strong>
                                <small class="text-muted"><i class="bi bi-card-heading me-1"></i>NIP. {{ $surat->pegawai->nip ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="text-dark fw-semibold d-block">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    @if($surat->puskesmas_id)
                                        {{ Str::startsWith($surat->puskesmas->nama_puskesmas ?? '', 'Puskesmas') ? $surat->puskesmas->nama_puskesmas : 'Puskesmas ' . ($surat->puskesmas->nama_puskesmas ?? '-') }}
                                    @else
                                        {{ $surat->lokasi_tujuan }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="badge bg-dark-subtle text-dark border border-dark-subtle px-2 py-1.5 fw-semibold d-inline-block">
                                    <i class="bi bi-calendar-event me-1"></i> 
                                    @if($surat->tanggal_mulai == $surat->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d M Y') }} <i class="bi bi-arrow-right mx-1"></i> {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 text-dark text-truncate-2" style="font-size: 0.85rem; max-width: 280px;" title="{{ $surat->maksud_tujuan }}">
                                    "{{ $surat->maksud_tujuan }}"
                                </p>
                            </td>
                            <td class="text-center py-2">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    <button type="button" class="btn btn-xs btn-outline-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $surat->id }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>

                                    <a href="{{ route('pengguna.surat_tugas.print', $surat->id) }}" target="_blank" class="btn btn-xs btn-success fw-bold text-white">
                                        <i class="bi bi-printer"></i> Cetak SPT
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada Surat Perintah Tugas (SPT) yang disetujui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suratTugas->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $suratTugas->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL --}}
@foreach($suratTugas as $surat)
    <div class="modal fade" id="modalDetail{{ $surat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-text me-2"></i>Detail Surat Perintah Tugas (SPT)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tr>
                                <td class="text-muted py-2" style="width: 35%;"><i class="bi bi-hash me-2"></i>Nomor Surat (SPT)</td>
                                <td class="py-2" style="width: 2%;">:</td>
                                <td class="fw-bold py-2 text-dark font-monospace">{{ $surat->nomor_surat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-person-badge me-2"></i>Pemohon / Petugas Pelaksana</td>
                                <td class="py-2">:</td>
                                <td class="fw-bold py-2 text-dark">{{ $surat->pegawai->nama_pegawai ?? '-' }} <span class="badge bg-secondary ms-1">NIP. {{ $surat->pegawai->nip ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2 align-top"><i class="bi bi-people me-2"></i>Anggota Tim (Pengikut)</td>
                                <td class="py-2 align-top">:</td>
                                <td class="py-2 align-top">
                                    @if($surat->pengikut && $surat->pengikut->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($surat->pengikut as $anggota)
                                                <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle px-2.5 py-1.5"><i class="bi bi-person me-1"></i>{{ $anggota->nama_pegawai }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border fst-italic">Berangkat Mandiri (Sendiri)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-geo-alt me-2"></i>Tujuan / Lokasi Tugas</td>
                                <td class="py-2">:</td>
                                <td class="py-2 fw-semibold text-dark">
                                    @if($surat->puskesmas_id)
                                        {{ Str::startsWith($surat->puskesmas->nama_puskesmas ?? '', 'Puskesmas') ? $surat->puskesmas->nama_puskesmas : 'Puskesmas ' . ($surat->puskesmas->nama_puskesmas ?? '-') }}
                                    @else
                                        {{ $surat->lokasi_tujuan }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-calendar3 me-2"></i>Waktu Pelaksanaan</td>
                                <td class="py-2">:</td>
                                <td class="py-2 fw-semibold text-dark">
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d M Y') }}
                                    @if($surat->tanggal_mulai != $surat->tanggal_selesai)
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i> {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-calendar-check me-2"></i>Tanggal Disetujui</td>
                                <td class="py-2">:</td>
                                <td class="py-2 text-dark fw-semibold">{{ $surat->tanggal_disetujui ? \Carbon\Carbon::parse($surat->tanggal_disetujui)->translatedFormat('d F Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Maksud & Agenda -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="text-dark fw-bold mb-2"><i class="bi bi-card-text me-2 text-dark"></i>Maksud & Agenda Kegiatan:</div>
                        <div class="border-start border-4 border-dark bg-light p-3 rounded text-dark text-wrap" style="line-height: 1.6; font-size: 0.95rem;">
                            {!! nl2br(e($surat->maksud_tujuan)) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <a href="{{ route('pengguna.surat_tugas.print', $surat->id) }}" target="_blank" class="btn btn-success rounded-pill px-4 text-white">
                        <i class="bi bi-printer me-1"></i> Cetak Dokumen SPT
                    </a>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

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
