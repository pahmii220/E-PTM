@extends('layouts.master')

@section('title', 'Data Faktor Risiko PTM')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Data Faktor Risiko PTM</h2>
            <a href="{{ route('petugas.faktor_resiko.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Data
            </a>
        </div>
        <br>

        {{-- FILTER --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <div class="row g-2">

                    {{-- MEROKOK --}}
                    <div class="col-md-2">
                        <select id="filterMerokok" class="form-select">
                            <option value="">Merokok</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>

                    {{-- ALKOHOL --}}
                    <div class="col-md-2">
                        <select id="filterAlkohol" class="form-select">
                            <option value="">Alkohol</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>

                    {{-- AKTIVITAS FISIK --}}
                    <div class="col-md-3">
                        <select id="filterAktivitas" class="form-select">
                            <option value="">Kurang Aktivitas Fisik</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-2">
                        <select id="filterStatus" class="form-select">
                            <option value="">Status</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak / Revisi</option>
                            <option value="Tertunda">Tertunda</option>
                        </select>
                    </div>

                    {{-- PUSKESMAS --}}
                    @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                        <div class="col-md-3">
                            <select id="filterPuskesmas" class="form-select">
                                <option value="">Semua Puskesmas</option>
                                @foreach($faktor->pluck('puskesmas.nama_puskesmas')->unique()->filter() as $pkm)
                                    <option value="{{ $pkm }}">{{ $pkm }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- SEARCH --}}
                    <div class="col-md-12 mt-2">
                        <input type="text" id="customSearch" class="form-control"
                            placeholder="Cari nama peserta / puskesmas">
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-lg border-0">
            <div class="card-body p-3 table-responsive">
                <table id="faktorTable" class="table table-striped table-hover align-middle text-center">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Tanggal</th>
                            <th>Merokok</th>
                            <th>Alkohol</th>
                            <th>Kurang Aktivitas</th>
                            <th>Puskesmas</th>
                            <th style="min-width: 140px;">Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($faktor as $i => $f)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $f->pasien->nama_lengkap ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($f->tanggal_pemeriksaan)->format('d-m-Y') }}</td>

                                <td>
                                    <span class="badge bg-{{ $f->merokok === 'Ya' ? 'danger' : 'success' }}">
                                        {{ $f->merokok }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $f->alkohol === 'Ya' ? 'danger' : 'success' }}">
                                        {{ $f->alkohol }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $f->kurang_aktivitas_fisik === 'Ya' ? 'warning' : 'success' }}">
                                        {{ $f->kurang_aktivitas_fisik }}
                                    </span>
                                </td>

                                <td class="text-start">{{ $f->puskesmas->nama_puskesmas ?? '-' }}</td>

                                {{-- BAGIAN STATUS MENGGUNAKAN MODAL POP-UP --}}
                                <td>
                                    @if($f->status_verifikasi === 'approved')
                                        <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle me-1"></i> Diterima
                                        </span>
                                        @if($f->diverifikasi_pada)
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                Proses:
                                                {{ \Carbon\Carbon::parse($f->dibuat_pada)->diffForHumans(\Carbon\Carbon::parse($f->diverifikasi_pada), true) }}
                                            </div>
                                        @endif

                                    @elseif($f->status_verifikasi === 'rejected')
                                        <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-x-circle me-1"></i> Revisi
                                        </span>

                                        @if($f->catatan_verifikasi)
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-outline-danger rounded-pill"
                                                    style="font-size: 10px; padding: 2px 10px;" data-bs-toggle="modal"
                                                    data-bs-target="#noteModal{{ $f->id }}">
                                                    <i class="bi bi-eye"></i> Lihat Catatan
                                                </button>
                                            </div>

                                            {{-- Modal / Pop-up Box --}}
                                            <div class="modal fade" id="noteModal{{ $f->id }}" tabindex="-1"
                                                aria-labelledby="noteModalLabel{{ $f->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-danger text-white border-0">
                                                            <h5 class="modal-title fs-6 fw-bold" id="noteModalLabel{{ $f->id }}">
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i> Catatan Revisi
                                                                Admin
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start p-4 text-dark"
                                                            style="white-space: normal; line-height: 1.6;">
                                                            <div class="mb-2 text-muted" style="font-size: 12px;">
                                                                Pesan untuk data risiko:
                                                                <strong>{{ $f->pasien->nama_lengkap ?? '-' }}</strong>
                                                            </div>
                                                            <div class="p-3 bg-light border-start border-danger border-4 rounded">
                                                                {{ $f->catatan_verifikasi }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm px-4"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                            <a href="{{ route('petugas.faktor_resiko.edit', $f->id) }}"
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

                                {{-- AKSI --}}
                                <td>
                                    @if(auth()->user()->role_name === 'admin')
                                        <a href="{{ route('petugas.faktor_resiko.edit', $f->id) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('petugas.faktor_resiko.destroy', $f->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin hapus data?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if(in_array($f->status_verifikasi, ['pending', 'rejected']))
                                            <a href="{{ route('petugas.faktor_resiko.edit', $f->id) }}"
                                                class="btn btn-sm btn-warning me-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('petugas.faktor_resiko.destroy', $f->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Yakin hapus data?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">Terkunci</span>
                                        @endif
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

            const table = $('#faktorTable').DataTable({
                responsive: true,
                order: [[2, 'desc']],

                // TAMBAHKAN KODE INI:
                "drawCallback": function (settings) {
                    var api = this.api();
                    // Mengupdate kolom ke-0 (No) setiap kali tabel digambar ulang
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

            $('#filterMerokok').change(() => table.column(3).search($('#filterMerokok').val()).draw());
            $('#filterAlkohol').change(() => table.column(4).search($('#filterAlkohol').val()).draw());
            $('#filterAktivitas').change(() => table.column(5).search($('#filterAktivitas').val()).draw());
            $('#filterStatus').change(() => table.column(7).search($('#filterStatus').val()).draw());
            $('#customSearch').keyup(() => table.search($('#customSearch').val()).draw());

            if ($('#filterPuskesmas').length) {
                $('#filterPuskesmas').change(() => {
                    table.column(6).search($('#filterPuskesmas').val()).draw();
                });
            }
        });
    </script>

    <style>
        table th,
        table td {
            vertical-align: middle !important;
        }
    </style>
@endsection