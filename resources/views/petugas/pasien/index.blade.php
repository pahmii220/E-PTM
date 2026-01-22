@extends('layouts.master')

@section('title', 'Data Peserta')

@section('content')
<div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="fw-bold mb-0">Daftar Peserta</h2>

        <div class="d-flex gap-2">
            <a href="{{ route('petugas.pasien.create') }}" class="btn btn-success">
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
                        <option value="Ditolak">Ditolak</option>
                        <option value="Tertunda">Tertunda</option>
                    </select>
                </div>

                @if(in_array(auth()->user()->role_name, ['admin','pengguna']))
                <div class="col-md-3">
                    <select id="filterPuskesmas" class="form-select">
                        <option value="">Semua Puskesmas</option>
                        @foreach($pasien->pluck('puskesmas.nama_puskesmas')->unique()->filter() as $pkm)
                            <option value="{{ $pkm }}">{{ $pkm }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3">
                    <input type="text" id="customSearch" class="form-control"
                        placeholder="Cari nama / alamat / RM">
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
            <table id="pasienTable" class="table table-striped table-hover align-middle text-center">
                <thead class="bg-success text-white">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No RM</th>
                        <th>Tgl Lahir</th>
                        <th>JK</th>
                        <th>Alamat</th>
                        <th>Kontak</th>
                        <th>Puskesmas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($pasien as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="text-start">{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->no_rekam_medis }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') }}</td>

                        <td>
                            <span class="badge bg-{{ $p->jenis_kelamin === 'Laki-laki' ? 'primary' : 'pink' }}">
                                {{ $p->jenis_kelamin }}
                            </span>
                        </td>

                        <td class="text-start">{{ Str::limit($p->alamat, 40) }}</td>
                        <td>{{ $p->kontak }}</td>
                        <td>{{ $p->puskesmas->nama_puskesmas ?? '-' }}</td>

                        <td>
                            @if($p->verification_status === 'approved')
                                <span class="badge bg-success">Diterima</span>
                            @elseif($p->verification_status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning text-dark">Tertunda</span>
                            @endif
                        </td>

                        <td>
    @if($p->verification_status === 'approved' && auth()->user()->role_name !== 'admin')
        {{-- 🔒 TERKUNCI --}}
        <span class="badge bg-secondary">
             Terkunci
        </span>
    @else
        {{-- ✏️ EDIT --}}
        <a href="{{ route('petugas.pasien.edit',$p->id) }}"
            class="btn btn-sm btn-warning me-1">
            <i class="bi bi-pencil-square"></i>
        </a>

        {{-- 🗑️ DELETE --}}
        <form action="{{ route('petugas.pasien.destroy',$p->id) }}"
            method="POST" class="d-inline"
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

    const basePrintUrl = "{{ route('pengguna.verifikasi.print.pasien') }}";

    // ✅ INIT DATATABLE (INI YANG HILANG)
    const table = $('#pasienTable').DataTable({
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

    // ✅ FILTER JENIS KELAMIN
    $('#filterGender').on('change', function () {
        table.column(4).search(this.value).draw();
    });

    // ✅ FILTER STATUS
    $('#filterStatus').on('change', function () {
        table.column(8).search(this.value).draw();
        updatePrintUrl();
    });

    // ✅ FILTER PUSKESMAS (HANYA JIKA ADA)
    if ($('#filterPuskesmas').length) {
        $('#filterPuskesmas').on('change', function () {
            table.column(7).search(this.value).draw();
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
.badge.bg-pink { background:#e83e8c }

@media print {
    body * { visibility: hidden; }

    #printHeader,
    #printHeader *,
    #pasienTable,
    #pasienTable * {
        visibility: visible;
    }

    .btn,
    .card,
    .dataTables_filter,
    .dataTables_length,
    .dataTables_paginate {
        display: none !important;
    }

    #pasienTable {
        font-size: 12px;
    }
}
</style>
@endsection
