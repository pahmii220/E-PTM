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
                    <a href="{{ route('pengguna.verifikasi.print.peserta', ['status' => $status, 'puskesmas_id' => request('puskesmas_id')]) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>

                    {{-- Form Filter Ditambah Puskesmas --}}
                    <form method="GET" class="d-flex gap-2">
                        {{-- Filter Puskesmas --}}
                        <select name="puskesmas_id" class="form-select form-select-sm rounded-pill shadow-sm"
                            onchange="this.form.submit()" style="min-width: 160px;">
                            <option value="all">Semua Puskesmas</option>
                            @foreach($puskesmasList ?? [] as $p)
                                <option value="{{ $p->id }}" {{ request('puskesmas_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>

                         {{-- Filter Bulan --}}
    <select name="bulan"
        class="form-select form-select-sm rounded-pill shadow-sm"
        onchange="this.form.submit()"
        style="min-width: 140px;">
        <option value="all">Semua Bulan</option>
        @foreach([
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
            5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
            9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ] as $key => $nama)
            <option value="{{ $key }}"
                {{ request('bulan') == $key ? 'selected' : '' }}>
                {{ $nama }}
            </option>
        @endforeach
    </select>

                        {{-- Filter Status --}}
                        <select name="status" class="form-select form-select-sm rounded-pill shadow-sm"
                            onchange="this.form.submit()" style="min-width: 130px;">
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
                    <header>
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Identitas Peserta</th>
                                <th>No RM</th>
                                <th>Kontak</th>
                                <th>Puskesmas</th>
                                <th>Status</th>
                                <th>Tanggal Entry</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                                @php
                                    // 1. Logika Pemrosesan Data
                                    $tglLahirFormatted = '-';
                                    if (!empty($row->tanggal_lahir)) {
                                        try {
                                            $tglLahirFormatted = \Carbon\Carbon::parse($row->tanggal_lahir)->format('d-m-Y');
                                        } catch (\Exception $e) {
                                            $tglLahirFormatted = $row->tanggal_lahir;
                                        }
                                    }

                                    // 🚀 DATA JSON DIPERBARUI DENGAN FIELD BARU
                                    $dataArray = [
                                        "nama" => $row->nama_lengkap ?? '-',
                                        "nik" => $row->nik ?? '-',
                                        "rm" => $row->no_rekam_medis ?? '-',
                                        "tempat_lahir" => $row->tempat_lahir ?? '-',
                                        "tgl_lahir" => $tglLahirFormatted,
                                        "jk" => $row->jenis_kelamin ?? '-',
                                        "pekerjaan" => $row->pekerjaan ?? '-',
                                        "kecamatan" => $row->kecamatan ?? '-',
                                        "alamat" => $row->alamat ?? '-',
                                        "kontak" => $row->kontak ?? '-',
                                        "puskesmas" => optional($row->puskesmas)->nama_puskesmas ?? '-'
                                    ];
                                    $amanData = base64_encode(json_encode($dataArray));
                                @endphp

                                {{-- 2. Tampilan Baris Tabel --}}
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>

                                    {{-- Kolom Nama digabung NIK --}}
                                    <td class="text-start">
                                        <div class="fw-semibold text-dark">{{ $row->nama_lengkap }}</div>
                                        <div class="text-muted" style="font-size: 11px;">NIK: {{ $row->nik ?? '-' }}</div>
                                    </td>

                                    <td>{{ $row->no_rekam_medis }}</td>
                                    <td>{{ $row->kontak }}</td>
                                    <td class="text-secondary fw-medium">{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $row->status_verifikasi }}">
                                            <i
                                                class="bi {{ $row->status_verifikasi == 'approved' ? 'bi-check-circle' : ($row->status_verifikasi == 'rejected' ? 'bi-x-circle' : 'bi-clock-history') }}"></i>
                                            @if($row->status_verifikasi === 'approved')
                                                Diterima
                                            @elseif($row->status_verifikasi === 'rejected')
                                                Ditolak
                                            @else
                                                Tertunda
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $row->dibuat_pada->format('d-m-Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        @if ($row->status_verifikasi !== 'pending')
                                            <a href="{{ route('pengguna.verifikasi.peserta.show', $row->id) }}"
                                                class="btn btn-outline-primary btn-sm rounded-circle me-1" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif

                                        @if ($row->status_verifikasi === 'pending')
                                            <button class="btn btn-success btn-sm rounded-circle me-1" title="Setujui Identitas"
                                                data-bs-toggle="modal" data-bs-target="#verifyModal" data-id="{{ $row->id }}"
                                                data-type="peserta" data-action="approve" data-peserta="{{ $amanData }}">
                                                <i class="bi bi-check-lg"></i>
                                            </button>

                                            <button class="btn btn-danger btn-sm rounded-circle" title="Tolak Identitas"
                                                data-bs-toggle="modal" data-bs-target="#verifyModal" data-id="{{ $row->id }}"
                                                data-type="peserta" data-action="reject" data-peserta="{{ $amanData }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MODAL VERIFIKASI ================= --}}
        <div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="verifyForm" method="POST" action="{{ route('pengguna.verifikasi.process') }}">
                    @csrf
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                        <div class="modal-header text-white px-4 py-3" style="background-color: #059669;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-person-lines-fill me-2"></i> Verifikasi
                                Identitas Peserta</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body p-4 bg-light">
                            <input type="hidden" name="id" id="modal_id">
                            <input type="hidden" name="type" id="modal_type">
                            <input type="hidden" name="action" id="modal_action">
                            <input type="hidden" name="status" id="modal_status">

                            {{-- KOTAK DATA PESERTA DIPERBARUI --}}
                            <div class="bg-white p-4 rounded-4 shadow-sm border mb-4">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Rincian Identitas Dasar</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nama Lengkap</label>
                                        <input type="text" id="disp_nama" class="form-control bg-light text-dark fw-bold"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">NIK (Nomor Induk Kependudukan)</label>
                                        <input type="text" id="disp_nik" class="form-control bg-light text-dark fw-bold"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nomor Rekam Medis</label>
                                        <input type="text" id="disp_rm" class="form-control bg-light text-dark fw-semibold"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Tempat, Tanggal Lahir</label>
                                        <input type="text" id="disp_ttl" class="form-control bg-light text-dark fw-semibold"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Jenis Kelamin</label>
                                        <input type="text" id="disp_jk" class="form-control bg-light text-dark fw-semibold"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Pekerjaan</label>
                                        <input type="text" id="disp_pekerjaan"
                                            class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Kecamatan</label>
                                        <input type="text" id="disp_kecamatan"
                                            class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold mb-1">Nomor HP / Kontak</label>
                                        <input type="text" id="disp_kontak"
                                            class="form-control bg-light text-dark fw-semibold" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold mb-1">Alamat Domisili</label>
                                        <textarea id="disp_alamat" rows="2"
                                            class="form-control bg-light text-dark fw-semibold" readonly></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small fw-bold mb-1">Puskesmas Perujuk</label>
                                        <input type="text" id="disp_puskesmas"
                                            class="form-control border-success text-success fw-bold bg-success bg-opacity-10"
                                            readonly>
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
                            <div
                                class="p-3 bg-white border border-info rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-info-emphasis"><i class="bi bi-link-45deg"></i> Periksa
                                        Data Lanjutan</h6>
                                    <small class="text-muted">Arahkan ke modul lain untuk memverifikasi data medis peserta
                                        ini.</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('pengguna.verifikasi.faktor') }}" id="link_faktor"
                                        class="btn btn-sm btn-outline-warning rounded-pill fw-bold px-3">
                                        <i class="bi bi-activity"></i> Faktor Risiko
                                    </a>
                                    <a href="{{ route('pengguna.verifikasi.deteksi') }}" id="link_deteksi"
                                        class="btn btn-sm btn-outline-info rounded-pill fw-bold px-3">
                                        <i class="bi bi-heart-pulse"></i> Deteksi Dini
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- FOOTER MODAL --}}
                        <div class="modal-footer bg-white border-top py-3 px-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="verifySubmit" class="btn px-4 fw-bold rounded-pill shadow-sm">Simpan
                                Identitas</button>
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
                                var base64Data = button.getAttribute('data-peserta');
                                var dataPeserta = JSON.parse(atob(base64Data));

                                var id = button.getAttribute('data-id');
                                var type = button.getAttribute('data-type') || 'peserta';
                                var action = button.getAttribute('data-action');

                                // Set Hidden Input Form
                                document.getElementById('modal_id').value = id;
                                document.getElementById('modal_type').value = type;
                                document.getElementById('modal_action').value = action;
                                document.getElementById('modal_status').value = (action === 'approve') ? 'approved' : 'rejected';

                                // Set href tombol secara dinamis dengan parameter peserta_id & status pending
                                document.getElementById('link_faktor').href = "{{ route('pengguna.verifikasi.faktor') }}?peserta_id=" + id + "&status=pending";
                                document.getElementById('link_deteksi').href = "{{ route('pengguna.verifikasi.deteksi') }}?peserta_id=" + id + "&status=pending";

                                // 🚀 MASUKKAN DATA BARU KE INPUT FIELD MODAL
                                document.getElementById('disp_nama').value = dataPeserta.nama || '-';
                                document.getElementById('disp_nik').value = dataPeserta.nik || '-';
                                document.getElementById('disp_rm').value = dataPeserta.rm || '-';
                                document.getElementById('disp_ttl').value = (dataPeserta.tempat_lahir || '-') + ', ' + (dataPeserta.tgl_lahir || '-');

                                let jk = dataPeserta.jk;
                                document.getElementById('disp_jk').value = (jk === 'L' || jk === 'Laki-laki') ? 'Laki-laki' : ((jk === 'P' || jk === 'Perempuan') ? 'Perempuan' : jk);

                                document.getElementById('disp_pekerjaan').value = dataPeserta.pekerjaan || '-';
                                document.getElementById('disp_kecamatan').value = dataPeserta.kecamatan || '-';
                                document.getElementById('disp_alamat').value = dataPeserta.alamat;
                                document.getElementById('disp_kontak').value = dataPeserta.kontak;
                                document.getElementById('disp_puskesmas').value = dataPeserta.puskesmas;

                                // Atur Tampilan Tombol
                                let noteInput = document.getElementById('verifyNote');
                                let submitBtn = document.getElementById('verifySubmit');

                                if (action === 'approve') {
                                    noteInput.value = 'Data identitas valid dan disetujui.';
                                    submitBtn.textContent = 'Setujui Data';
                                    submitBtn.className = 'btn btn-success px-4 fw-bold rounded-pill shadow-sm';
                                } else {
                                    noteInput.value = '';
                                    noteInput.placeholder = 'Mohon perbaiki penulisan nama/NIK/Alamat...';
                                    submitBtn.textContent = 'Tolak & Minta Revisi';
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
            .table th {
                font-weight: 600;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 999px;
                font-size: 0.85rem;
                font-weight: 600;
            }

            .status-pending {
                background-color: #eef2f7;
                color: #475569;
            }

            .status-approved {
                background-color: #e6f6ef;
                color: #047857;
            }

            .status-rejected {
                background-color: #fdecec;
                color: #b91c1c;
            }

            .form-control[readonly] {
                border: 1px solid #e2e8f0;
                background-color: #f8fafc !important;
                color: #1f2937 !important;
                cursor: default;
            }
        </style>
    </div>
@endsection