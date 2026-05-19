@extends('layouts.master')

@section('title', 'Data Kegiatan PTM')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Data Kegiatan PTM</h2>

            <div class="d-flex gap-2">
                <a href="{{ route('petugas.kegiatan.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Kegiatan
                </a>
            </div>
        </div>

        <br>

        {{-- FILTER --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <div class="row g-2">

                    <div class="col-md-4">
                        <input type="text" id="customSearch" class="form-control" placeholder="Cari nama kegiatan / lokasi">
                    </div>

                    <div class="col-md-4">
                        <select id="filterJenis" class="form-select">
                            <option value="">Semua Jenis Kegiatan</option>
                            <option value="Posbindu PTM">Posbindu PTM</option>
                            <option value="Skrining PTM">Skrining PTM</option>
                            <option value="Penyuluhan">Penyuluhan</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="date" id="filterTanggal" class="form-control">
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-lg border-0">
            <div class="card-body p-3 table-responsive">

                <table id="kegiatanTable" class="table table-striped table-hover align-middle text-center">

                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Jumlah Peserta</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($kegiatan as $i => $k)
                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td class="text-start">
                                    {{ $k->nama_kegiatan }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $k->jenis_kegiatan }}
                                    </span>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') }}
                                </td>

                                <td class="text-start">
                                    {{ $k->lokasi }}
                                </td>

                                <td>
                                    {{ $k->jumlah_peserta ?? '-' }}
                                </td>

                                <td class="text-start">
                                    {{ \Str::limit($k->keterangan, 40) }}
                                </td>

                                <td>

                                    {{-- EDIT --}}
                                    <a href="{{ route('petugas.kegiatan.edit', $k->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('petugas.kegiatan.destroy', $k->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin hapus data kegiatan?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

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

            const table = $('#kegiatanTable').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // SEARCH CUSTOM
            $('#customSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            // FILTER JENIS
            $('#filterJenis').on('change', function () {
                table.column(2).search(this.value).draw();
            });

        });
    </script>

@endsection