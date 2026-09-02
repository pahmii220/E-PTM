@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        @include('kepala_p2ptm.laporan.partials.tabs')

        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-0">Laporan Tindak Lanjut PTM</h4>
                        <small class="text-muted">Tinjau data tindakan atau rujukan yang diberikan petugas kepada
                            pasien.</small>
                    </div>

                    <div class="col-md-7">
                        <form action="{{ route('kepala.laporan.tindak_lanjut') }}" method="GET"
                            class="d-flex flex-wrap gap-2 justify-content-md-end align-items-end">
                            <div>
                                <small class="text-muted d-block mb-1">Bulan</small>
                                <select name="bulan" class="form-select form-select-sm rounded-pill shadow-sm">
                                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $nama)
                                        <option value="{{ $key + 1 }}" {{ request('bulan', date('m')) == ($key + 1) ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Tahun</small>
                                <input type="number" name="tahun"
                                    class="form-control form-control-sm rounded-pill shadow-sm"
                                    value="{{ request('tahun', date('Y')) }}" style="width: 100px;">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm px-3">
                                    <i class="bi bi-search"></i> Tampilkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <hr class="my-3 opacity-25">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalSahkanLaporanTindakLanjut"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4 fw-bold">
                        <i class="bi bi-printer me-1"></i> Cetak &amp; Sahkan Laporan
                    </button>
                </div>

                <!-- MODAL PENGESAHAN TINDAK LANJUT -->
                <div class="modal fade" id="modalSahkanLaporanTindakLanjut" tabindex="-1" aria-labelledby="modalSahkanTLLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="{{ route('kepala.laporan.tindak_lanjut.cetak') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $val)
                                    @if($key !== 'catatan_pengesahan' && !is_array($val))
                                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                    @endif
                                @endforeach
                                @if(!request()->has('bulan'))
                                    <input type="hidden" name="bulan" value="{{ date('m') }}">
                                @endif
                                @if(!request()->has('tahun'))
                                    <input type="hidden" name="tahun" value="{{ date('Y') }}">
                                @endif

                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-patch-check-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalSahkanTLLabel">Pengesahan Laporan Tindak Lanjut PTM</h5>
                                            <small class="text-muted">Digital Signature HMAC-SHA256 & QR Code Resmi</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body px-4 py-3">
                                    <div class="mb-2">
                                        <label for="catatan_tl" class="form-label fw-bold text-dark small mb-1">
                                            <i class="bi bi-chat-left-text-fill text-success me-1"></i> Catatan / Arahan Kepala Bidang P2PTM:
                                        </label>
                                        <textarea class="form-control rounded-3" id="catatan_tl" name="catatan_pengesahan" rows="3" placeholder="Masukkan catatan resmi pengesahan..." style="font-size: 0.85rem;" required>Rekapitulasi intervensi dan tindak lanjut penanganan kasus PTM telah diverifikasi dan disahkan.</textarea>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-info-circle me-1"></i> Catatan ini akan otomatis terkunci dalam tanda tangan digital dan tampil saat QR Code di-scan.
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-light rounded-pill px-3 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-sm shadow-sm" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('modalSahkanLaporanTindakLanjut')).hide(); }, 300);">
                                        <i class="bi bi-printer-fill me-1"></i> Sahkan &amp; Cetak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:60px">No</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Tindak Lanjut</th>
                            <th>Jenis Tindak Lanjut</th>
                            <th>Keterangan/Catatan</th>
                            <th>Puskesmas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                <td class="text-muted small">
                                    {{ $row->tanggal_tindak_lanjut ? \Carbon\Carbon::parse($row->tanggal_tindak_lanjut)->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill">
                                        {{ $row->jenis_tindak_lanjut ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $row->catatan_petugas ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hospital"></i> {{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data tindak lanjut untuk periode tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $data->links() }}
        </div>
    </div>
@endsection