@extends('layouts.master')

@section('title', 'Tinjau Hasil Monitoring')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px; margin:auto;">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span style="background:linear-gradient(135deg,#6366f1,#4f46e5); width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-shield-check text-white fs-5"></i>
                </span>
                Tinjau Hasil Monitoring
            </h2>
            <p class="text-muted mb-0 small">Tinjau dan setujui laporan hasil monitoring yang diajukan oleh Pegawai Dinkes.</p>
        </div>
        <div>
            <a href="{{ route('kepala.laporan_monitoring.cetak_semua', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
               target="_blank" class="btn btn-teal rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-printer me-2"></i> Cetak Laporan
            </a>
        </div>
    </div>

    {{-- ===== FILTER TANGGAL ===== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('kepala.laporan_monitoring.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-calendar-event me-1 text-primary"></i> Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-select border-0 bg-light rounded-3" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-calendar-event me-1 text-primary"></i> Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-select border-0 bg-light rounded-3" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold w-100">
                        <i class="bi bi-funnel-fill me-1"></i> Filter Data
                    </button>
                    @if(request('start_date') || request('end_date'))
                    <a href="{{ route('kepala.laporan_monitoring.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ===== TABEL DAFTAR LAPORAN ===== --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th class="border-0 rounded-start-3 px-4 py-3 text-uppercase text-secondary small fw-bold">Tanggal & Pengaju</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold">Puskesmas Terkait</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold">Kesimpulan / Temuan</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold text-center">Status</th>
                            <th class="border-0 rounded-end-3 px-4 py-3 text-uppercase text-secondary small fw-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $row)
                            <tr class="shadow-sm" style="background-color: #fff; transition: all 0.2s;">
                                <td class="rounded-start-3 px-4 py-3 border-0">
                                    <div class="fw-bold text-dark">{{ $row->created_at->format('d M Y') }}</div>
                                    <div class="small text-muted"><i class="bi bi-person me-1"></i>{{ $row->pegawai->nama_pegawai ?? ($row->pegawai->user->Nama_Lengkap ?? '-') }}</div>
                                </td>
                                <td class="px-4 py-3 border-0">
                                    <span class="fw-bold text-primary">{{ $row->puskesmas->nama_puskesmas }}</span>
                                </td>
                                <td class="px-4 py-3 border-0">
                                    <span class="d-block fw-bold">{{ $row->judul_laporan }}</span>
                                </td>
                                <td class="px-4 py-3 border-0 text-center">
                                    @if($row->status_laporan === 'pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Menunggu ACC</span>
                                    @elseif($row->status_laporan === 'disetujui')
                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="rounded-end-3 px-4 py-3 border-0 text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTinjau{{ $row->id }}">
                                            @if($row->status_laporan === 'pending')
                                                <i class="bi bi-eye me-1"></i> Tinjau & Keputusan
                                            @else
                                                <i class="bi bi-search me-1"></i> Detail
                                            @endif
                                        </button>
                                        @if($row->status_laporan === 'disetujui')
                                            <!-- <a href="{{ route('kepala.laporan_monitoring.cetak', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm" title="Cetak Laporan Monitoring">
                                                <i class="bi bi-printer"></i> Cetak
                                            </a> -->
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- MODAL TINJAU / KEPUTUSAN --}}
                            <div class="modal fade" id="modalTinjau{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow-lg">
                                        <div class="modal-header border-bottom-0 bg-primary text-white rounded-top-4 p-4">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-clipboard-check me-2"></i>Tinjau Laporan Hasil Monitoring</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('kepala.laporan_monitoring.acc', $row->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted small">Pelapor / Pegawai Dinkes</p>
                                                        <p class="fw-bold mb-1">{{ $row->pegawai->nama_pegawai ?? ($row->pegawai->user->Nama_Lengkap ?? 'Pegawai Dinkes') }}</p>
                                                        <p class="mb-1 text-muted small">Kategori Temuan</p>
                                                        <p class="fw-semibold text-primary mb-0"><i class="bi bi-tag-fill me-1"></i>{{ $row->kategori_temuan ?? 'Pemantauan Wilayah Puskesmas' }}</p>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <p class="mb-1 text-muted small">Puskesmas Tujuan</p>
                                                        <p class="fw-bold text-primary mb-1">{{ $row->puskesmas->nama_puskesmas }}</p>
                                                        <p class="mb-1 text-muted small">Tanggal Kunjungan &amp; SPT</p>
                                                        <p class="small text-secondary mb-0">
                                                            📅 {{ \Carbon\Carbon::parse($row->tanggal_kunjungan ?? $row->created_at)->translatedFormat('d M Y') }}
                                                            | SPT: <strong>{{ $row->nomor_spt ?? '-' }}</strong>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="bg-light p-3 rounded-3 mb-3 border-start border-4 border-primary">
                                                    <p class="mb-1 text-muted small text-uppercase">Judul Temuan</p>
                                                    <h5 class="fw-bold mb-0">{{ $row->judul_laporan }}</h5>
                                                </div>
                                                
                                                <div class="bg-light p-3 rounded-3 mb-3">
                                                    <p class="mb-1 text-muted small text-uppercase">Deskripsi</p>
                                                    <p class="mb-0">{{ $row->deskripsi_temuan }}</p>
                                                </div>

                                                <div class="bg-light p-3 rounded-3 mb-4">
                                                    <p class="mb-1 text-muted small text-uppercase">Rekomendasi / Kesimpulan</p>
                                                    <p class="mb-0">{{ $row->rekomendasi_tindakan }}</p>
                                                </div>

                                                <hr>
                                                
                                                @if($row->status_laporan === 'pending')
                                                    <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Keputusan Kepala P2PTM</h6>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small fw-bold">Berikan Catatan (Opsional)</label>
                                                        <textarea name="catatan_kepala" class="form-control border-2" rows="2" placeholder="Tulis instruksi tambahan jika ada..."></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small fw-bold">Pilih Keputusan</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="status_laporan" id="acc{{ $row->id }}" value="disetujui" required>
                                                                <label class="form-check-label text-success fw-bold" for="acc{{ $row->id }}">Setujui</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="status_laporan" id="tolak{{ $row->id }}" value="ditolak" required>
                                                                <label class="form-check-label text-danger fw-bold" for="tolak{{ $row->id }}">Tolak / Revisi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert {{ $row->status_laporan === 'disetujui' ? 'alert-success' : 'alert-danger' }} border-0 mb-0">
                                                        <h6 class="fw-bold mb-1">
                                                            Laporan telah {{ $row->status_laporan === 'disetujui' ? 'DISETUJUI' : 'DITOLAK' }}
                                                        </h6>
                                                        <p class="mb-0 small text-muted"><strong>Catatan Anda:</strong> {{ $row->catatan_kepala ?? '-' }}</p>
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="modal-footer border-top-0 px-4 pb-4">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                                @if($row->status_laporan === 'pending')
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="bi bi-save me-2"></i>Simpan Keputusan</button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" width="120" class="opacity-50 mb-3">
                                    <p class="text-muted fw-medium mb-0">Belum ada antrean laporan monitoring.</p>
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
