@extends('layouts.master')

@section('title', 'Verifikasi - Deteksi Dini')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">
        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0">Verifikasi Deteksi Dini</h4>
                    <small class="text-muted">Kelola persetujuan data deteksi dini dari petugas</small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('pengguna.verifikasi.print.deteksi', ['status' => $status ?? 'pending']) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>

                    {{-- FILTER STATUS --}}
                    <form method="GET" action="{{ route('pengguna.verifikasi.deteksi') }}">
                        <select name="status" class="form-select form-select-sm rounded-pill shadow-sm border-0"
                            onchange="this.form.submit()" style="width: 140px;">
                            <option value="pending" {{ ($status ?? 'pending') == 'pending' ? 'selected' : '' }}>Tertunda
                            </option>
                            <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="all" {{ ($status ?? '') == 'all' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive p-3">
                <table id="petugasTable" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Peserta</th>
                            <th>Tanggal</th>
                            <th>Tekanan</th>
                            <th>Gula</th>
                            <th>Puskesmas</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            @php
                                $dataArray = [
                                    "nama" => optional($row->pasien)->nama_lengkap ?? '-',
                                    "rm" => optional($row->pasien)->no_rekam_medis ?? '-',
                                    "tensi" => $row->tekanan_darah ?? '-',
                                    "gula" => $row->gula_darah ?? '-'
                                ];
                                $amanData = base64_encode(json_encode($dataArray));
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ optional($row->pasien)->nama_lengkap ?? '-' }}</td>
                                <td>{{ $row->tanggal_pemeriksaan?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $row->tekanan_darah ?? '-' }}</td>
                                <td>{{ $row->gula_darah ?? '-' }}</td>
                                <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $row->status_verifikasi }}">
                                        {{ ucfirst($row->status_verifikasi) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    @if ($row->status_verifikasi === 'pending')
                                        <button class="btn btn-success btn-sm rounded-circle me-1" title="Terima"
                                            data-bs-toggle="modal" data-bs-target="#verifyModal" data-id="{{ $row->id }}"
                                            data-type="deteksi" data-action="approve" data-pasien="{{ $amanData }}">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm rounded-circle" title="Tolak" data-bs-toggle="modal"
                                            data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="deteksi"
                                            data-action="reject" data-pasien="{{ $amanData }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-5 text-muted">Tidak ada data</td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                                <td class="d-none"></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL VERIFIKASI --}}
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
                @csrf
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-success text-white px-4 py-3">
                        <h5 class="modal-title fw-bold">Konfirmasi Verifikasi</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="modal_id">
                        <input type="hidden" name="type" id="modal_type">
                        <input type="hidden" name="action" id="modal_action">
                        <input type="hidden" name="status" id="modal_status">
                        <p class="mb-3">Apakah Anda yakin ingin <strong id="txt_action"></strong> data <strong>deteksi
                                dini</strong> pasien <strong id="disp_nama"></strong>?</p>
                        <textarea name="note" id="verifyNote" rows="2" class="form-control"
                            placeholder="Tuliskan catatan verifikasi (opsional)..."></textarea>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="verifySubmit" class="btn btn-success rounded-pill px-4">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // 1. Inisialisasi DataTable
                if ($('#petugasTable').length > 0) {
                    if ($.fn.DataTable.isDataTable('#petugasTable')) {
                        $('#petugasTable').DataTable().destroy();
                    }
                    $('#petugasTable').DataTable({
                        responsive: true,
                        autoWidth: false,
                        order: [[1, 'asc']]
                    });
                }

                // 2. Logika Modal
                var verifyModal = document.getElementById('verifyModal');
                if (verifyModal) {
                    verifyModal.addEventListener('show.bs.modal', function (event) {
                        var button = event.relatedTarget;
                        var dataPasien = JSON.parse(atob(button.getAttribute('data-pasien')));
                        var action = button.getAttribute('data-action');

                        document.getElementById('modal_id').value = button.getAttribute('data-id');
                        document.getElementById('modal_type').value = button.getAttribute('data-type');
                        document.getElementById('modal_action').value = action;
                        document.getElementById('modal_status').value = (action === 'approve') ? 'approved' : 'rejected';

                        document.getElementById('txt_action').innerText = (action === 'approve') ? 'menyetujui' : 'menolak';
                        document.getElementById('disp_nama').innerText = dataPasien.nama;

                        let noteInput = document.getElementById('verifyNote');
                        noteInput.value = (action === 'approve') ? "Data telah disetujui." : "";

                        let btn = document.getElementById('verifySubmit');
                        btn.className = (action === 'approve') ? 'btn btn-success rounded-pill px-4' : 'btn btn-danger rounded-pill px-4';
                        btn.textContent = (action === 'approve') ? 'Setujui' : 'Tolak';
                    });
                }
            });
        </script>
    @endpush
@endsection