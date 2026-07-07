@extends('layouts.master')

@section('title', 'Data Peserta')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Daftar Peserta</h2>

            <div class="d-flex gap-2">
                <a href="{{ route('petugas.peserta.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Peserta
                </a>
            </div>
        </div>
        <br>

        {{-- FILTER --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select id="filterGender" class="form-select">
                            <option value="">Semua Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select id="filterStatus" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak / Revisi</option>
                            <option value="Tertunda">Tertunda</option>
                        </select>
                    </div>

                    @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                        <div class="col-md-3">
                            <select id="filterPuskesmas" class="form-select">
                                <option value="">Semua Puskesmas</option>
                                @foreach($peserta->pluck('puskesmas.nama_puskesmas')->unique()->filter() as $pkm)
                                    <option value="{{ $pkm }}">{{ $pkm }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-3">
                        <input type="text" id="customSearch" class="form-control"
                            placeholder="Cari nama / alamat / NIK / RM">
                    </div>
                </div>
            </div>
        </div>

        {{-- HEADER CETAK --}}
        <div id="printHeader" class="d-none text-center mb-3">
            <h4 class="fw-bold mb-1">Laporan Data Peserta</h4>
            <div id="printSubTitle" class="text-muted"></div>
            <hr>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-lg border-0">
            <div class="card-body p-3 table-responsive">
                <table id="pesertaTable" class="table table-striped table-hover align-middle text-center">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Identitas Peserta</th>
                            <th>No RM</th>
                            <th>TTL</th>
                            <th>JK</th>
                            <th>Pekerjaan</th>
                            <th>Alamat & Kec</th>
                            <th>Kontak</th>
                            <th>Puskesmas</th>
                            <th style="min-width: 140px;">Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($peserta as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- NAMA & NIK --}}
                                <td class="text-start">
                                    <div class="fw-bold text-dark">{{ $p->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size: 12px;">NIK: {{ $p->nik ?? '-' }}</div>
                                </td>

                                <td>{{ $p->no_rekam_medis }}</td>

                                {{-- TEMPAT TANGGAL LAHIR --}}
                                <td>
                                    {{ $p->tempat_lahir ?? '-' }},<br>
                                    {{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $p->jenis_kelamin === 'Laki-laki' ? 'primary' : 'pink' }}">
                                        {{ $p->jenis_kelamin }}
                                    </span>
                                </td>

                                {{-- PEKERJAAN --}}
                                <td>{{ $p->pekerjaan ?? '-' }}</td>

                                {{-- ALAMAT & KECAMATAN --}}
                                <td class="text-start">
                                    {{ Str::limit($p->alamat, 30) }}<br>
                                    <span class="text-muted fw-semibold" style="font-size: 11px;">
                                        Kec. {{ $p->kecamatan ?? '-' }}
                                    </span>
                                </td>

                                <td>{{ $p->kontak }}</td>
                                <td>{{ $p->puskesmas->nama_puskesmas ?? '-' }}</td>


                                {{-- BAGIAN STATUS MENGGUNAKAN MODAL POP-UP --}}
                                <td>
                                    @if($p->status_verifikasi === 'approved')
                                        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle me-1"></i> Diterima
                                        </span>
                                        @if($p->diverifikasi_pada)
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                Proses:
                                                {{ \Carbon\Carbon::parse($p->dibuat_pada)->diffForHumans(\Carbon\Carbon::parse($p->diverifikasi_pada), true) }}
                                            </div>
                                        @endif

                                    @elseif($p->status_verifikasi === 'rejected')
                                        <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-x-circle me-1"></i> Revisi
                                        </span>

                                        @if($p->catatan_verifikasi)
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-outline-danger rounded-pill"
                                                    style="font-size: 10px; padding: 2px 10px;" data-bs-toggle="modal"
                                                    data-bs-target="#noteModal{{ $p->id }}">
                                                    <i class="bi bi-eye"></i> Lihat Catatan
                                                </button>
                                            </div>

                                            {{-- Modal / Pop-up Box --}}
                                            <div class="modal fade" id="noteModal{{ $p->id }}" tabindex="-1"
                                                aria-labelledby="noteModalLabel{{ $p->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-danger text-white border-0">
                                                            <h5 class="modal-title fs-6 fw-bold" id="noteModalLabel{{ $p->id }}">
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Catatan Revisi
                                                                Admin
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start p-4 text-dark"
                                                            style="white-space: normal; line-height: 1.6;">
                                                            <div class="mb-2 text-muted" style="font-size: 12px;">
                                                                Pesan untuk data peserta: <strong>{{ $p->nama_lengkap }}</strong>
                                                            </div>
                                                            <div class="p-3 bg-light border-start border-danger border-4 rounded">
                                                                {{ $p->catatan_verifikasi }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm px-4"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                            <a href="{{ route('petugas.peserta.edit', $p->id) }}"
                                                                class="btn btn-danger btn-sm px-4">
                                                                <i class="bi bi-pencil-square"></i> Perbaiki Data
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-hourglass-split me-1"></i> Tertunda
                                        </span>

                                    @endif
                                </td>

                                <td>
                                    @if($p->status_verifikasi === 'approved' && auth()->user()->role_name !== 'admin')
                                        {{-- 🔒 TERKUNCI --}}
                                        <span class="badge bg-secondary">
                                            Terkunci
                                        </span>
                                    @else
                                        {{-- ✏️ EDIT --}}
                                        <a href="{{ route('petugas.peserta.edit', $p->id) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- 🗑️ DELETE --}}
                                        <form action="{{ route('petugas.peserta.destroy', $p->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin hapus data?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DATATABLE --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {

            const basePrintUrl = "{{ route('pengguna.verifikasi.print.peserta') ?? '#' }}";

            // ✅ INIT DATATABLE
            const table = $('#pesertaTable').DataTable({
                responsive: true,
                order: [[1, 'asc']],

                "drawCallback": function (settings) {
                    var api = this.api();
                    api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                },

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // Kolom JK sekarang berada di index 4
            $('#filterGender').on('change', function () {
                table.column(4).search(this.value).draw();
            });

            // Kolom Status sekarang bergeser to index 9
            $('#filterStatus').on('change', function () {
                table.column(9).search(this.value).draw();
                updatePrintUrl();
            });

            // Kolom Puskesmas sekarang bergeser to index 8
            if ($('#filterPuskesmas').length) {
                $('#filterPuskesmas').on('change', function () {
                    table.column(8).search(this.value).draw();
                    updatePrintUrl();
                });
            }

            // ✅ SEARCH CUSTOM
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // ✅ UPDATE URL CETAK
            function updatePrintUrl() {
                let status = $('#filterStatus').val();
                let puskesmas = $('#filterPuskesmas').length
                    ? $('#filterPuskesmas').val()
                    : '';

                let params = new URLSearchParams();

                if (status) params.append('status', status);
                if (puskesmas) params.append('puskesmas', puskesmas);

                $('#btnPrint').attr(
                    'href',
                    basePrintUrl + (params.toString() ? '?' + params.toString() : '')
                );
            }

        });
    </script>

    <style>
        .badge.bg-pink {
            background: #e83e8c
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printHeader,
            #printHeader *,
            #pesertaTable,
            #pesertaTable * {
                visibility: visible;
            }

            .btn,
            .card,
            .dataTables_filter,
            .dataTables_length,
            .dataTables_paginate {
                display: none !important;
            }

            #pesertaTable {
                font-size: 12px;
            }
        }
    </style>
@endsection