@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1400px; background-color: #f8fafc;">
        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="text-2xl font-semibold text-gray-800">Laporan Data Pegawai P2PTM</h2>
                <p class="text-gray-500 text-sm mt-1">Data rekam pegawai Dinas Kesehatan yang bertugas di wilayah kerja P2PTM.</p>
            </div>
        </div>

        {{-- KONTEN TABEL DATA PEGAWAI --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 min-h-[400px]">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Laporan Data Pegawai Dinkes P2PTM</h4>
                    <small class="text-muted">Data pegawai Dinas Kesehatan yang bertugas di tingkat Kabupaten/Kota.</small>
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalSahkanLaporanPegawai"
                    class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4 fw-bold">
                    <i class="bi bi-printer me-1"></i> Cetak & sahkan Laporan
                </button>
            </div>

            <!-- MODAL PENGESAHAN LAPORAN PEGAWAI -->
            <div class="modal fade" id="modalSahkanLaporanPegawai" tabindex="-1" aria-labelledby="modalSahkanPegawaiLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg">
                        <form action="{{ route('kepala.laporan.eksekutif.cetak_pegawai') }}" method="GET" target="_blank">
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
                                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalSahkanPegawaiLabel">Pengesahan Laporan Data Pegawai</h5>
                                        <small class="text-muted">Digital Signature HMAC-SHA256 & QR Code Resmi</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body px-4 py-3">
                                <div class="mb-2">
                                    <label for="catatan_pegawai" class="form-label fw-bold text-dark small mb-1">
                                        <i class="bi bi-chat-left-text-fill text-success me-1"></i> Catatan / Arahan Kepala Bidang P2PTM:
                                    </label>
                                    <textarea class="form-control rounded-3" id="catatan_pegawai" name="catatan_pengesahan" rows="3" placeholder="Masukkan catatan resmi pengesahan..." style="font-size: 0.85rem;" required>Data kepegawaian dan tenaga teknis P2PTM telah diverifikasi dan disahkan.</textarea>
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-info-circle me-1"></i> Catatan ini akan otomatis terkunci dalam tanda tangan digital dan tampil saat QR Code di-scan.
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light rounded-pill px-3 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold btn-sm shadow-sm" onclick="setTimeout(function(){ bootstrap.Modal.getInstance(document.getElementById('modalSahkanLaporanPegawai')).hide(); }, 300);">
                                    <i class="bi bi-printer-fill me-1"></i> Sahkan &amp; Cetak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th class="text-start">NIP</th>
                            <th class="text-start">Nama Pegawai</th>
                            <th class="text-start">Jabatan</th>
                            <th class="text-start">Bidang</th>
                            <th>Wilayah Tugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataPegawai ?? [] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start fw-bold text-dark">{{ $row->nip ?? '-' }}</td>
                                <td class="text-start text-dark">{{ $row->nama_pegawai ?? '-' }}</td>
                                <td class="text-start fw-semibold">{{ $row->jabatan ?? '-' }}</td>
                                <td class="text-start text-muted">{{ $row->bidang ?? '-' }}</td>
                                <td>{{ $row->kabupaten_kota ?? 'Banjarmasin' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data pegawai dinkes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
