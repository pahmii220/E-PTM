@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0">Verifikasi Data Peserta</h4>
                    <small class="text-muted">Fokus pada persetujuan data identitas dasar peserta</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('pengguna.verifikasi.print.pasien', ['status' => $status]) }}"
                            class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" target="_blank">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                        <form method="GET">
                            <select name="status" class="form-select form-select-sm rounded-pill shadow-sm"
                                onchange="this.form.submit()">
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Tertunda</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Diterima</option>
                                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================= TABLE ================= --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive p-3"> 
                    <table id="petugasTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Lengkap</th>
                                <th>No RM</th>
                                <th>Kontak</th>
                                <th>Status Identitas</th>
                                <th>Tanggal Entry</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                @php
                                    $tglLahirFormatted = '-';
                                    if (!empty($row->tanggal_lahir)) {
                                        try {
                                            $tglLahirFormatted = \Carbon\Carbon::parse($row->tanggal_lahir)->format('d-m-Y');
                                        } catch (\Exception $e) {
                                            $tglLahirFormatted = $row->tanggal_lahir;
                                        }
                                    }

                                    // KARENA SEKARANG FOKUS PESERTA, KITA HANYA AMBIL DATA PESERTA SAJA (Lebih ringan & cepat!)
                                    $dataArray = [
                                        "nama" => $row->nama_lengkap ?? '-',
                                        "rm" => $row->no_rekam_medis ?? '-',
                                        "tgl_lahir" => $tglLahirFormatted,
                                        "jk" => $row->jenis_kelamin ?? '-',
                                        "alamat" => $row->alamat ?? '-',
                                        "kontak" => $row->kontak ?? '-',
                                        "puskesmas" => optional($row->puskesmas)->nama_puskesmas ?? '-'
                                    ];
                                    $amanData = base64_encode(json_encode($dataArray));
                                @endphp

                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold text-dark">{{ $row->nama_lengkap }}</td>
                                    <td>{{ $row->no_rekam_medis }}</td>
                                    <td>{{ $row->kontak }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $row->status_verifikasi }}">
                                            <i class="bi {{ $row->status_verifikasi == 'approved' ? 'bi-check-circle' : ($row->status_verifikasi == 'rejected' ? 'bi-x-circle' : 'bi-clock-history') }}"></i>
                                            {{ ucfirst($row->status_verifikasi) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $row->dibuat_pada->format('d-m-Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        {{-- Tombol Detail Muncul Kalau Sudah Selesai Diverifikasi --}}
                                        @if ($row->status_verifikasi !== 'pending')
                                            <a href="{{ route('pengguna.verifikasi.pasien.show', $row->id) }}"
                                                class="btn btn-outline-primary btn-sm rounded-circle me-1" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif

                                        {{-- Tombol Verifikasi Muncul Kalau Status Masih Tertunda --}}
                                        @if ($row->status_verifikasi === 'pending')
                                            <button class="btn btn-success btn-sm rounded-circle me-1" title="Setujui Identitas" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="pasien" data-action="approve"
                                                data-pasien="{{ $amanData }}">
                                                <i class="bi bi-check-lg"></i>
                                            </button>

                                            <button class="btn btn-danger btn-sm rounded-circle" title="Tolak Identitas" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="pasien" data-action="reject"
                                                data-pasien="{{ $amanData }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada data peserta</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= MODAL VERIFIKASI (VERSI CLEAN/BERSIH KHUSUS PESERTA) ================= --}}
        <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
                    @csrf
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                        <div class="modal-header text-white px-4 py-3" style="background-color: #059669;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-person-lines-fill me-2"></i> Verifikasi Identitas Peserta</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4 bg-light">
                            <input type="hidden" name="id" id="modal_id">
                            <input type="hidden" name="type" id="modal_type">
                            <input type="hidden" name="action" id="modal_action">
                            <input type="hidden" name="status" id="modal_status">

                            {{-- KOTAK DATA PESERTA --}}
                            <div class="bg-white p-4 rounded-4 shadow-sm border mb-4">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Rincian Identitas Dasar</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nama Lengkap</label>
                                        <input type="text" id="disp_nama" class="form-control bg-light text-dark fw-bold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nomor Rekam Medis / NIK</label>
                                        <input type="text" id="disp_rm" class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Tanggal Lahir</label>
                                        <input type="text" id="disp_tgl_lahir" class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Jenis Kelamin</label>
                                        <input type="text" id="disp_jk" class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold mb-1">Alamat Domisili</label>
                                        <textarea id="disp_alamat" rows="2" class="form-control bg-light text-dark fw-semibold" readonly></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nomor HP / Kontak</label>
                                        <input type="text" id="disp_kontak" class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Puskesmas Perujuk</label>
                                        <input type="text" id="disp_puskesmas" class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- CATATAN VERIFIKASI --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-2">Catatan Verifikator</label>
                                <textarea name="note" id="verifyNote" rows="2" class="form-control shadow-sm"
                                    placeholder="Berikan alasan jika ditolak, atau catatan khusus jika disetujui..."></textarea>
                            </div>

                            {{-- NAVIGASI LINTAS BERKAS --}}
                            <div class="p-3 bg-white border border-info rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-info-emphasis"><i class="bi bi-link-45deg"></i> Periksa Data Lanjutan</h6>
                                    <small class="text-muted">Arahkan ke modul lain untuk memverifikasi data medis peserta ini.</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pengguna.verifikasi.faktor') }}" class="btn btn-sm btn-outline-warning rounded-pill fw-bold px-3">
                                        <i class="bi bi-activity"></i> Faktor Risiko
                                    </a>
                                    <a href="{{ route('pengguna.verifikasi.deteksi') }}" class="btn btn-sm btn-outline-info rounded-pill fw-bold px-3">
                                        <i class="bi bi-heart-pulse"></i> Deteksi Dini
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- FOOTER MODAL --}}
                        <div class="modal-footer bg-white border-top py-3 px-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="verifySubmit" class="btn px-4 fw-bold rounded-pill shadow-sm">Simpan Identitas</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var verifyModal = document.getElementById('verifyModal');

                    if (verifyModal) {
                        verifyModal.addEventListener('show.bs.modal', function (event) {
                            var button = event.relatedTarget;

                            try {
                                var base64Data = button.getAttribute('data-pasien');
                                var dataPasien = JSON.parse(atob(base64Data));

                                var id = button.getAttribute('data-id');
                                var type = button.getAttribute('data-type') || 'pasien';
                                var action = button.getAttribute('data-action');

                                // Set Hidden Input Form
                                document.getElementById('modal_id').value = id;
                                document.getElementById('modal_type').value = type;
                                document.getElementById('modal_action').value = action;
                                document.getElementById('modal_status').value = (action === 'approve') ? 'approved' : 'rejected';

                                // Masukkan Data ke Input Field
                                document.getElementById('disp_nama').value = dataPasien.nama || '-';
                                document.getElementById('disp_rm').value = dataPasien.rm || '-';
                                document.getElementById('disp_tgl_lahir').value = dataPasien.tgl_lahir || '-';

                                let jk = dataPasien.jk;
                                document.getElementById('disp_jk').value = (jk === 'L' || jk === 'Laki-laki') ? 'Laki-laki' : ((jk === 'P' || jk === 'Perempuan') ? 'Perempuan' : jk);

                                document.getElementById('disp_alamat').value = dataPasien.alamat;
                                document.getElementById('disp_kontak').value = dataPasien.kontak;
                                document.getElementById('disp_puskesmas').value = dataPasien.puskesmas;

                                // Atur Tampilan Tombol
                                let noteInput = document.getElementById('verifyNote');
                                let submitBtn = document.getElementById('verifySubmit');

                                if (action === 'approve') {
                                    noteInput.value = 'Data identitas valid dan disetujui.';
                                    submitBtn.textContent = 'Setujui';
                                    submitBtn.className = 'btn btn-success px-4 fw-bold rounded-pill shadow-sm';
                                } else {
                                    noteInput.value = '';
                                    noteInput.placeholder = 'Mohon perbaiki penulisan nama/NIK/Alamat...';
                                    submitBtn.textContent = 'Tolak';
                                    submitBtn.className = 'btn btn-danger px-4 fw-bold rounded-pill shadow-sm';
                                }
                            } catch (e) {
                                console.error("Terjadi kesalahan parsing JSON:", e);
                            }
                        });
                    }
                });
            </script>
        @endpush

        <style>
            .table th { font-weight: 600; }
            .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; font-size: 0.85rem; font-weight: 600; }
            .status-pending { background-color: #eef2f7; color: #475569; }
            .status-approved { background-color: #e6f6ef; color: #047857; }
            .status-rejected { background-color: #fdecec; color: #b91c1c; }

            .form-control[readonly] {
                border: 1px solid #e2e8f0;
                background-color: #f8fafc !important;
                color: #1f2937 !important;
                cursor: default;
            }
        </style>
@endsection