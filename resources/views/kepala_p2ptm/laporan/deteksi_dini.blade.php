@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        @include('kepala_p2ptm.laporan.partials.tabs')


        {{-- ================= HEADER & FILTER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-0">Laporan Deteksi Dini PTM</h4>
                        <small class="text-muted">Tinjau hasil pemeriksaan deteksi dini (Tekanan Darah, Gula Darah, dll)
                            dari Puskesmas.</small>
                    </div>

                    <div class="col-md-7">
                        <form action="{{ route('kepala.laporan.deteksi_dini') }}" method="GET"
                            class="d-flex flex-wrap gap-2 justify-content-md-end align-items-end">
                            
                            {{-- Jenis Filter --}}
                            <div class="d-flex align-items-center gap-3 bg-white px-3 py-1 border rounded-pill shadow-sm mb-1">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="filter_type" id="type_bulan" value="bulanan" 
                                        {{ request('filter_type', 'bulanan') === 'bulanan' ? 'checked' : '' }} onchange="toggleFilterType()">
                                    <label class="form-check-label text-muted small mb-0 fw-semibold" for="type_bulan">Bulanan</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="filter_type" id="type_tanggal" value="tanggal" 
                                        {{ request('filter_type') === 'tanggal' ? 'checked' : '' }} onchange="toggleFilterType()">
                                    <label class="form-check-label text-muted small mb-0 fw-semibold" for="type_tanggal">Rentang Tanggal</label>
                                </div>
                            </div>

                            {{-- Input Bulanan --}}
                            <div id="filter_bulanan_inputs" class="d-flex gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">Bulan</small>
                                    <select name="bulan" class="form-select form-select-sm rounded-pill shadow-sm" style="min-width: 120px;">
                                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $nama)
                                            <option value="{{ $key + 1 }}" {{ request('bulan', date('m')) == ($key + 1) ? 'selected' : '' }}>{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Tahun</small>
                                    <input type="number" name="tahun"
                                        class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tahun', date('Y')) }}" style="width: 90px;">
                                </div>
                            </div>

                            {{-- Input Rentang Tanggal --}}
                            <div id="filter_tanggal_inputs" class="d-flex gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">Tanggal Awal</small>
                                    <input type="date" name="tgl_awal" class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tgl_awal') }}">
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Tanggal Akhir</small>
                                    <input type="date" name="tgl_akhir" class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tgl_akhir') }}">
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm px-3">
                                    <i class="bi bi-search"></i> Tampilkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <hr class="my-3 opacity-25">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalSahkanLaporanDeteksi"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4 fw-bold">
                        <i class="bi bi-printer me-1"></i> Cetak &amp; Sahkan Laporan
                    </button>
                </div>

                <!-- MODAL PENGESAHAN DETEKSI DINI -->
                <div class="modal fade" id="modalSahkanLaporanDeteksi" tabindex="-1" aria-labelledby="modalSahkanDeteksiLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="{{ route('kepala.laporan.deteksi_dini.cetak') }}" method="GET" target="_blank">
                                @foreach(request()->all() as $key => $val)
                                    @if($key !== 'catatan_pengesahan' && !is_array($val))
                                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                    @endif
                                @endforeach

                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                            <i class="bi bi-patch-check-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalSahkanDeteksiLabel">Pengesahan Laporan Deteksi Dini PTM</h5>
                                            <small class="text-muted">Digital Signature HMAC-SHA256 & QR Code Resmi</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body px-4 py-3">
                                    <div class="mb-2">
                                        <label for="catatan_deteksi" class="form-label fw-bold text-dark small mb-1">
                                            <i class="bi bi-chat-left-text-fill text-success me-1"></i> Catatan / Arahan Kepala Bidang P2PTM:
                                        </label>
                                        <textarea class="form-control rounded-3" id="catatan_deteksi" name="catatan_pengesahan" rows="3" placeholder="Masukkan catatan resmi pengesahan..." style="font-size: 0.85rem;" required>Rekapitulasi data hasil deteksi dini PTM telah diteliti dan diverifikasi sah.</textarea>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-info-circle me-1"></i> Catatan ini akan otomatis terkunci dalam tanda tangan digital dan tampil saat QR Code di-scan.
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-light rounded-pill px-3 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-sm shadow-sm" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('modalSahkanLaporanDeteksi')).hide(); }, 300);">
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
                            <th class="ps-4">No</th>
                            <th>Nama Pasien</th>
                            <th>Puskesmas</th>
                            <th>Tanggal Periksa</th>
                            <th>Tekanan Darah</th>
                            <th>Gula Darah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ $row->peserta->nama_lengkap ?? '-' }}</td>

                                {{-- Menampilkan nama puskesmas asal data --}}
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hospital"></i> {{ $row->puskesmas->nama_puskesmas ?? 'Puskesmas' }}
                                    </span>
                                </td>

                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan ?? $row->dibuat_pada)->format('d-m-Y') }}
                                </td>

                                {{-- Menampilkan Hasil Medis --}}
                                <td>{{ $row->tekanan_darah ?? '-' }} mmHg</td>
                                <td>{{ $row->gula_darah ?? '-' }} mg/dL</td>

                                <td>
                                    @php
    // Ambil teks dari database dan ubah ke huruf kecil untuk pengecekan
    $status = strtolower($row->hasil_skrining ?? '');
                                    @endphp

                                    @if(str_contains($status, 'normal'))
                                        <span class="badge rounded-pill bg-success-subtle text-success">
                                            {{ $row->hasil_skrining }}  
                                        </span>
                                    @elseif(str_contains($status, 'dicurigai'))
                                        {{-- Warna Kuning (Warning) --}}
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                            {{ $row->hasil_skrining }}
                                        </span>
                                    @elseif(str_contains($status, 'resiko') || str_contains($status, 'risiko'))
                                        {{-- Warna Merah (Danger) --}}
                                        <span class="badge rounded-pill bg-danger-subtle text-danger">
                                            {{ $row->hasil_skrining }}
                                        </span>
                                    @else
                                        {{-- Jika kosong atau status lain (Abu-abu) --}}
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                            {{ $row->hasil_skrining ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data pemeriksaan deteksi dini untuk periode bulan/tahun tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $data->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleFilterType() {
        const type = document.querySelector('input[name="filter_type"]:checked').value;
        const bulanSection = document.getElementById('filter_bulanan_inputs');
        const tanggalSection = document.getElementById('filter_tanggal_inputs');
        
        if (type === 'bulanan') {
            bulanSection.style.display = 'flex';
            tanggalSection.style.display = 'none';
        } else {
            bulanSection.style.display = 'none';
            tanggalSection.style.display = 'flex';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterType();
    });
</script>
@endpush