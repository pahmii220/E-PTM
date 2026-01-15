@extends('layouts.master')

@section('title', 'Data Tindak Lanjut PTM')

@section('content')
<div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="fw-bold mb-0">Data Tindak Lanjut PTM</h2>

        @if(auth()->user()->role_name === 'petugas')
            @if($deteksiDiniTerbaru)
                <a href="{{ route('petugas.tindak_lanjut.create', $deteksiDiniTerbaru->id) }}"
                    class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Tambah Tindak Lanjut
                </a>
            @else
                <button class="btn btn-secondary" disabled>
                    Belum Ada Deteksi Dini
                </button>
            @endif
        @endif
    </div>
    <br>
    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <div class="row g-2">

                {{-- JENIS TINDAK LANJUT --}}
                <div class="col-md-3">
                    <select id="filterJenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="Edukasi">Edukasi</option>
                        <option value="Anjuran Gaya Hidup">Anjuran Gaya Hidup</option>
                        <option value="Rujukan">Rujukan</option>
                        <option value="Monitoring">Monitoring</option>
                        <option value="Tidak Ada">Tidak Ada</option>
                    </select>
                </div>

                {{-- STATUS --}}
                <div class="col-md-3">
                    <select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Sudah">Sudah</option>
                        <option value="Belum">Belum</option>
                    </select>
                </div>

                {{-- SEARCH --}}
                <div class="col-md-6">
                    <input type="text" id="customSearch" class="form-control"
                        placeholder="Cari nama peserta / catatan">
                </div>

            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-3 table-responsive">
            <table id="tindakLanjutTable" class="table table-striped table-hover align-middle text-center">
                <thead class="bg-success text-white">
                    <tr>
                        <th>No</th>
                        <th>Nama Peserta</th>
                        <th>Tgl Pemeriksaan</th>
                        <th>Jenis Tindak Lanjut</th>
                        <th>Tgl Tindak Lanjut</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($tindakLanjut as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>

                        <td class="text-start">
                            {{ $t->pasien->nama_lengkap ?? '-' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($t->deteksiDini->tanggal_pemeriksaan)->format('d-m-Y') }}
                        </td>

                        <td>
                            @php
                                $jenisMap = [
                                    'edukasi' => 'Edukasi',
                                    'anjuran_gaya_hidup' => 'Anjuran Gaya Hidup',
                                    'rujukan' => 'Rujukan',
                                    'monitoring' => 'Monitoring',
                                    'tidak_ada' => 'Tidak Ada'
                                ];
                            @endphp
                            {{ $jenisMap[$t->jenis_tindak_lanjut] ?? '-' }}
                        </td>

                        <td>
                            {{ $t->tanggal_tindak_lanjut
                                ? \Carbon\Carbon::parse($t->tanggal_tindak_lanjut)->format('d-m-Y')
                                : '-' }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $t->status_tindak_lanjut === 'sudah' ? 'success' : 'warning' }}">
                                {{ ucfirst($t->status_tindak_lanjut) }}
                            </span>
                        </td>

                        <td class="text-start">
                            {{ $t->catatan_petugas ?? '-' }}
                        </td>

                        {{-- AKSI --}}
                        <td>
                            @if(auth()->user()->role_name === 'petugas')
                                <a href="{{ route('petugas.tindak_lanjut.edit',$t->id) }}"
                                    class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('petugas.tindak_lanjut.destroy',$t->id) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin hapus data?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-info">Monitoring</span>
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

    const table = $('#tindakLanjutTable').DataTable({
        responsive: true,
        order: [[2,'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: { next: "›", previous: "‹" }
        }
    });

    $('#filterJenis').change(() => table.column(3).search($('#filterJenis').val()).draw());
    $('#filterStatus').change(() => table.column(5).search($('#filterStatus').val()).draw());
    $('#customSearch').keyup(() => table.search($('#customSearch').val()).draw());

});
</script>

<style>
table th, table td { vertical-align: middle !important; }
.table td.text-start { text-align: left !important; }
</style>
@endsection
