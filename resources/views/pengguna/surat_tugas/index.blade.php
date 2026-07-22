@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="text-2xl font-semibold text-gray-800">Pengajuan Surat Tugas Luar</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola riwayat tugas luar Anda dan unduh SPT resmi setelah disetujui.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle me-1"></i> Buat Pengajuan Baru
            </button>
        </div>
    </div>

    {{-- CARD TABEL PENGAJUAN --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Pengajuan Surat Tugas</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem; border-color: #e2e8f0;">
                <thead class="table-light text-secondary">
                    <tr class="align-middle">
                        <th class="text-center py-3" style="width: 45px;">No</th>
                        <th class="py-3" style="min-width: 160px;">Tanggal Tugas</th>
                        <th class="py-3" style="min-width: 180px;">Lokasi Tujuan</th>
                        <th class="py-3" style="min-width: 250px;">Agenda Kunjungan</th>
                        <th class="py-3" style="min-width: 170px;">Nomor Surat</th>
                        <th class="text-center py-3" style="width: 110px;">Status</th>
                        <th class="text-center py-3" style="min-width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratTugas as $surat)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $loop->iteration + ($suratTugas->currentPage() - 1) * $suratTugas->perPage() }}</td>
                            <td>
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold d-inline-block">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d-m-Y') }} 
                                    @if($surat->tanggal_mulai != $surat->tanggal_selesai)
                                        <br><span class="text-muted small">s/d</span> {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d-m-Y') }}
                                    @endif
                                </div>
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
                                <p class="mb-0 text-secondary text-truncate-2" style="font-size: 0.85rem; max-width: 280px;" title="{{ $surat->maksud_tujuan }}">
                                    "{{ $surat->maksud_tujuan }}"
                                </p>
                            </td>
                            <td>
                                @if($surat->status_persetujuan == 'disetujui' && $surat->nomor_surat)
                                    <span class="badge bg-light text-dark border"><i class="bi bi-hash"></i> {{ $surat->nomor_surat }}</span>
                                @else
                                    <span class="text-muted small fst-italic">Belum Terbit</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($surat->status_persetujuan == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1"></i> Menunggu</span>
                                @elseif($surat->status_persetujuan == 'disetujui')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5" title="{{ $surat->catatan_kepala }}"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                @endif
                            </td>
                            <td class="text-center py-2">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    <button type="button" class="btn btn-xs btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalLacak{{ $surat->id }}" title="Lacak Status">
                                        <i class="bi bi-geo-alt"></i> Lacak Status
                                    </button>
                                    
                                    @if($surat->status_persetujuan == 'disetujui')
                                        <a href="{{ route('pengguna.surat_tugas.print', $surat->id) }}" target="_blank" class="btn btn-xs btn-dark fw-bold">
                                            <i class="bi bi-printer"></i> Cetak SPT
                                        </a>
                                    @elseif(in_array($surat->status_persetujuan, ['pending', 'ditolak']))
                                        <button class="btn btn-xs btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $surat->id }}" title="Edit/Perbaiki Pengajuan">
                                            <i class="bi bi-pencil-square"></i> {{ $surat->status_persetujuan == 'ditolak' ? 'Perbaiki' : 'Edit' }}
                                        </button>
                                        <form action="{{ route('pengguna.surat_tugas.destroy', $surat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan/menghapus pengajuan tugas luar ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" title="Hapus Pengajuan">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada riwayat pengajuan tugas luar.
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

{{-- MODAL LACAK DIPINDAHKAN KE LUAR TABEL AGAR TIDAK MERUSAK HTML DOM TABEL --}}
@foreach($suratTugas as $surat)
    <div class="modal fade" id="modalLacak{{ $surat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-primary"><i class="bi bi-map me-2"></i>Lacak Status Surat Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="tracking-timeline">
                        <!-- Step 1: Diajukan -->
                        <div class="tracking-step completed">
                            <div class="tracking-icon"><i class="bi bi-file-earmark-plus"></i></div>
                            <h6 class="fw-bold mb-1">Pengajuan Dibuat</h6>
                            <p class="text-muted small mb-0">{{ $surat->created_at->format('d M Y, H:i') }} WITA</p>
                        </div>

                        <!-- Step 2: Persetujuan Kepala -->
                        @if($surat->status_persetujuan == 'pending')
                            <div class="tracking-step active">
                                <div class="tracking-icon"><i class="bi bi-hourglass-split"></i></div>
                                <h6 class="fw-bold mb-1 text-primary">Menunggu Persetujuan</h6>
                                <p class="text-muted small mb-0">Sedang ditinjau oleh Kepala P2PTM</p>
                            </div>
                            <div class="tracking-step">
                                <div class="tracking-icon"><i class="bi bi-printer"></i></div>
                                <h6 class="fw-bold mb-1 text-muted">Disetujui & SPT Resmi Terbit</h6>
                            </div>
                        @elseif($surat->status_persetujuan == 'ditolak')
                            <div class="tracking-step rejected">
                                <div class="tracking-icon"><i class="bi bi-x-lg"></i></div>
                                <h6 class="fw-bold mb-1 text-danger">Pengajuan Ditolak</h6>
                                <p class="text-muted small mb-0">Alasan: {{ $surat->catatan_kepala ?? 'Tidak disebutkan' }}</p>
                            </div>
                        @elseif($surat->status_persetujuan == 'disetujui')
                            <div class="tracking-step completed">
                                <div class="tracking-icon"><i class="bi bi-check-lg"></i></div>
                                <h6 class="fw-bold mb-1">Disetujui Kepala P2PTM</h6>
                                <p class="text-muted small mb-0">{{ optional($surat->tanggal_disetujui)->format('d M Y, H:i') ?? '-' }}</p>
                            </div>
                            
                            <!-- Step 3: SPT Terbit -->
                            <div class="tracking-step completed">
                                <div class="tracking-icon"><i class="bi bi-printer"></i></div>
                                <h6 class="fw-bold mb-1">SPT Resmi Terbit & Selesai</h6>
                                <p class="text-muted small mb-0">No: {{ $surat->nomor_surat }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('pengguna.surat_tugas.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTambahLabel">Pengajuan Surat Tugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_mulai" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_selesai" required>
                    </div>
                    <div class="col-12" x-data="{ jenisLokasi: 'puskesmas' }">
                        <label class="form-label fw-semibold">Tujuan Tugas Luar</label>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="jenis_tujuan" id="radPusk" value="puskesmas" x-model="jenisLokasi">
                              <label class="form-check-label" for="radPusk">Ke Puskesmas</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="jenis_tujuan" id="radLainnya" value="lainnya" x-model="jenisLokasi">
                              <label class="form-check-label" for="radLainnya">Lokasi Lainnya</label>
                            </div>
                        </div>

                        <div x-show="jenisLokasi === 'puskesmas'" x-transition>
                            <select class="form-select" name="puskesmas_id" x-bind:required="jenisLokasi === 'puskesmas'">
                                <option value="">-- Pilih Puskesmas Tujuan --</option>
                                @foreach($puskesmasList as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_puskesmas }} ({{ $p->nama_kabupaten }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="jenisLokasi === 'lainnya'" style="display:none;" x-transition>
                            <input type="text" class="form-control" name="lokasi_tujuan" placeholder="Masukkan Lokasi Tujuan" x-bind:required="jenisLokasi === 'lainnya'">
                        </div>
                    </div>

                    
                    <div class="col-12">
                        <label class="form-label">Maksud dan Tujuan / Agenda <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="maksud_tujuan" rows="3" required placeholder="Contoh: Melakukan monitoring pelaksanaan Posbindu PTM dan sosialisasi bahaya merokok"></textarea>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-semibold">Anggota Tim / Pengikut (Opsional)</label>
                        <p class="text-muted text-sm mb-2">Pilih pegawai lain yang ikut serta dalam tugas luar ini. Kosongkan jika berangkat sendiri.</p>
                        <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background-color: #f8f9fa;">
                            @forelse($pegawaiList as $pegawai)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="pengikut_ids[]" value="{{ $pegawai->id }}" id="pengikut_{{ $pegawai->id }}">
                                    <label class="form-check-label" for="pengikut_{{ $pegawai->id }}">
                                        {{ $pegawai->nama_pegawai }} <small class="text-muted">({{ $pegawai->jabatan ?? 'NIP: '.$pegawai->nip }})</small>
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted fst-italic">Belum ada data pegawai lain.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Ajukan Permohonan</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
@foreach($suratTugas as $surat)
    @if($surat->status_persetujuan == 'pending')
    <div class="modal fade" id="modalEdit{{ $surat->id }}" tabindex="-1" aria-labelledby="modalEditLabel{{ $surat->id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form action="{{ route('pengguna.surat_tugas.update', $surat->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold" id="modalEditLabel{{ $surat->id }}">Edit Pengajuan Surat Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_mulai" value="{{ $surat->tanggal_mulai }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_selesai" value="{{ $surat->tanggal_selesai }}" required>
                        </div>
                        <div class="col-12" x-data="{ jenisLokasi: '{{ $surat->puskesmas_id ? 'puskesmas' : 'lainnya' }}' }">
                            <label class="form-label fw-semibold">Tujuan Tugas Luar</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="jenis_tujuan" id="radPuskEdit{{ $surat->id }}" value="puskesmas" x-model="jenisLokasi">
                                  <label class="form-check-label" for="radPuskEdit{{ $surat->id }}">Ke Puskesmas</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="jenis_tujuan" id="radLainnyaEdit{{ $surat->id }}" value="lainnya" x-model="jenisLokasi">
                                  <label class="form-check-label" for="radLainnyaEdit{{ $surat->id }}">Lokasi Lainnya</label>
                                </div>
                            </div>

                            <div x-show="jenisLokasi === 'puskesmas'" x-transition>
                                <select class="form-select" name="puskesmas_id" x-bind:required="jenisLokasi === 'puskesmas'">
                                    <option value="">-- Pilih Puskesmas Tujuan --</option>
                                    @foreach($puskesmasList as $p)
                                        <option value="{{ $p->id }}" {{ $surat->puskesmas_id == $p->id ? 'selected' : '' }}>{{ $p->nama_puskesmas }} ({{ $p->nama_kabupaten }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="jenisLokasi === 'lainnya'" style="display:none;" x-transition>
                                <input type="text" class="form-control" name="lokasi_tujuan" value="{{ $surat->lokasi_tujuan }}" placeholder="Masukkan Lokasi Tujuan" x-bind:required="jenisLokasi === 'lainnya'">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Maksud dan Tujuan / Agenda <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="maksud_tujuan" rows="3" required>{{ $surat->maksud_tujuan }}</textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Anggota Tim / Pengikut (Opsional)</label>
                            <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto; background-color: #f8f9fa;">
                                @php
                                    $pengikutIds = $surat->pengikut->pluck('id')->toArray();
                                @endphp
                                @forelse($pegawaiList as $pegawai)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="pengikut_ids[]" value="{{ $pegawai->id }}" id="pengikut_edit_{{ $surat->id }}_{{ $pegawai->id }}" {{ in_array($pegawai->id, $pengikutIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pengikut_edit_{{ $surat->id }}_{{ $pegawai->id }}">
                                            {{ $pegawai->nama_pegawai }} <small class="text-muted">({{ $pegawai->jabatan ?? 'NIP: '.$pegawai->nip }})</small>
                                        </label>
                                    </div>
                                @empty
                                    <div class="text-muted fst-italic">Belum ada data pegawai lain.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
      </div>
    </div>
    @endif
@endforeach

@endsection

@push('styles')
<style>
.tracking-timeline {
    position: relative;
    padding-left: 2.5rem;
    margin-bottom: 0;
}
.tracking-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.tracking-step {
    position: relative;
    padding-bottom: 1.5rem;
}
.tracking-step:last-child {
    padding-bottom: 0;
}
.tracking-step:last-child::before {
    content: '';
    position: absolute;
    left: -2.5rem;
    top: 32px;
    bottom: 0;
    width: 2px;
    background: #fff; /* Hide the line after the last item */
}
.tracking-icon {
    position: absolute;
    left: -2.5rem;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 2px solid #e2e8f0;
    color: #94a3b8;
    z-index: 1;
}
.tracking-step.active .tracking-icon {
    background: #eef2ff;
    border-color: #4f46e5;
    color: #4f46e5;
}
.tracking-step.completed .tracking-icon {
    background: #10b981;
    border-color: #10b981;
    color: #fff;
}
.tracking-step.rejected .tracking-icon {
    background: #ef4444;
    border-color: #ef4444;
    color: #fff;
}
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
@endpush
