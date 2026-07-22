@extends('layouts.master')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: auto;">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-check-fill text-success me-2"></i>Persetujuan Tugas Luar
            </h2>
            <p class="text-muted mb-0">Tinjau pengajuan tugas luar pegawai dan terbitkan Surat Perintah Tugas (SPT).</p>
        </div>
    </div>

    {{-- TABEL PERSETUJUAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Daftar Pengajuan Surat Tugas</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem; border-color: #e2e8f0;">
                <thead class="table-light text-secondary">
                    <tr class="align-middle">
                        <th class="text-center py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="min-width: 180px;">Nama Pegawai</th>
                        <th class="py-3" style="min-width: 200px;">Tujuan / Lokasi</th>
                        <th class="py-3" style="min-width: 160px;">Waktu Pelaksanaan</th>
                        <th class="py-3" style="min-width: 250px;">Agenda / Maksud</th>
                        <th class="text-center py-3" style="width: 110px;">Status</th>
                        <th class="text-center py-3" style="min-width: 190px;">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTugas as $surat)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration + ($suratTugas->currentPage() - 1) * $suratTugas->perPage() }}</td>
                            <td>
                                <strong class="text-dark d-block mb-1">{{ $surat->pegawai->nama_pegawai ?? '-' }}</strong>
                                <small class="text-muted"><i class="bi bi-card-heading me-1"></i>NIP. {{ $surat->pegawai->nip ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="text-primary fw-semibold d-block">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    @if($surat->puskesmas_id)
                                        Puskesmas {{ $surat->puskesmas->nama_puskesmas }}
                                    @else
                                        {{ $surat->lokasi_tujuan }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold d-inline-block">
                                    <i class="bi bi-calendar-event me-1"></i> 
                                    @if($surat->tanggal_mulai == $surat->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} <i class="bi bi-arrow-right mx-1"></i> {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 text-secondary text-truncate-2" style="font-size: 0.85rem; max-width: 280px;" title="{{ $surat->maksud_tujuan }}">
                                    "{{ $surat->maksud_tujuan }}"
                                </p>
                            </td>
                            <td class="text-center">
                                @if($surat->status_persetujuan == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5">Menunggu</span>
                                @elseif($surat->status_persetujuan == 'disetujui')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5">Disetujui</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5" title="{{ $surat->catatan_kepala }}">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center py-2">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    <button type="button" class="btn btn-xs btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $surat->id }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>

                                    @if($surat->status_persetujuan == 'pending')
                                        <form action="{{ route('kepala.surat_tugas.setujui', $surat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success fw-bold text-white" onclick="return confirm('Setujui pengajuan ini dan terbitkan SPT?')">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-xs btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $surat->id }}">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    @elseif($surat->status_persetujuan == 'disetujui')
                                        <a href="{{ route('pengguna.surat_tugas.print', $surat->id) }}" target="_blank" class="btn btn-xs btn-dark fw-bold">
                                            <i class="bi bi-printer"></i> Cetak SPT
                                        </a>
                                    @endif

                                    <form action="{{ route('kepala.surat_tugas.destroy', $surat->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data surat tugas ini secara permanen?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada pengajuan surat tugas luar dari pegawai.
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

{{-- MODAL DETAIL & TOLAK DIPINDAHKAN KE LUAR TABEL AGAR TIDAK MERUSAK HTML DOM TABEL --}}
@foreach($suratTugas as $surat)
    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetail{{ $surat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="bi bi-file-earmark-text me-2"></i>Detail Pengajuan Tugas Luar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tr>
                                <td class="text-muted py-2" style="width: 35%;"><i class="bi bi-person-badge me-2"></i>Pemohon Utama</td>
                                <td class="py-2" style="width: 2%;">:</td>
                                <td class="fw-bold py-2 text-dark">{{ $surat->pegawai->nama_pegawai ?? '-' }} <span class="badge bg-secondary ms-1">NIP. {{ $surat->pegawai->nip ?? '-' }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2 align-top"><i class="bi bi-people me-2"></i>Anggota Tim (Pengikut)</td>
                                <td class="py-2 align-top">:</td>
                                <td class="py-2 align-top">
                                    @if($surat->pengikut && $surat->pengikut->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($surat->pengikut as $anggota)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1.5"><i class="bi bi-person me-1"></i>{{ $anggota->nama_pegawai }}</span>
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
                                <td class="py-2 fw-semibold text-primary">
                                    @if($surat->puskesmas_id)
                                        Puskesmas {{ $surat->puskesmas->nama_puskesmas }}
                                    @else
                                        {{ $surat->lokasi_tujuan }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-calendar3 me-2"></i>Waktu Pelaksanaan</td>
                                <td class="py-2">:</td>
                                <td class="py-2 fw-semibold text-success">
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }}
                                    @if($surat->tanggal_mulai != $surat->tanggal_selesai)
                                        <i class="bi bi-arrow-right mx-1 text-muted"></i> {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2"><i class="bi bi-clock-history me-2"></i>Tanggal Pengajuan</td>
                                <td class="py-2">:</td>
                                <td class="py-2 text-muted">{{ $surat->created_at->format('d-m-Y H:i') }} WITA</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Maksud & Agenda -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="text-dark fw-bold mb-2"><i class="bi bi-card-text me-2 text-primary"></i>Maksud & Agenda Kegiatan:</div>
                        <div class="border-start border-4 border-primary bg-light p-3 rounded text-dark text-wrap" style="line-height: 1.6; font-size: 0.95rem;">
                            {!! nl2br(e($surat->maksud_tujuan)) !!}
                        </div>
                    </div>

                    @if($surat->status_persetujuan == 'ditolak' && $surat->catatan_kepala)
                        <div class="mt-3 p-3 bg-danger-subtle border border-danger-subtle rounded text-danger">
                            <strong><i class="bi bi-exclamation-triangle me-1"></i> Catatan Penolakan:</strong> {{ $surat->catatan_kepala }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    @if($surat->status_persetujuan == 'pending')
        <div class="modal fade" id="modalTolak{{ $surat->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('kepala.surat_tugas.tolak', $surat->id) }}" method="POST">
                    @csrf
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Tolak Pengajuan Surat Tugas</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <p class="mb-3">Berikan alasan penolakan untuk pengajuan pegawai <strong>{{ $surat->pegawai->nama_pegawai ?? '-' }}</strong>:</p>
                            <textarea class="form-control" name="catatan_kepala" rows="3" required placeholder="Contoh: Jadwal bentrok dengan rapat koordinasi dinas..."></textarea>
                        </div>
                        <div class="modal-footer border-0 pb-4 justify-content-end">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Pengajuan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
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
