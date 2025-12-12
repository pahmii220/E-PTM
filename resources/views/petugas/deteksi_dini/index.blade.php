@extends('layouts.master')

@section('title', 'Data Deteksi Dini PTM')

@section('content')
<div class="container-fluid py-3 px-4" style="max-width: 1400px; margin: 0 auto;">
    {{-- Judul dan tombol --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="fs-3 fw-bold text-gray-800 mb-0">Data Deteksi Dini PTM</h2>
        <a href="{{ route('petugas.deteksi_dini.create') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
        </a>
    </div>
    <br>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Card tabel --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="deteksiTable" class="table table-striped table-hover align-middle text-center mb-0">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Tekanan Darah</th>
                            <th>Gula Darah (mg/dL)</th>
                            <th>Kolesterol (mg/dL)</th>
                            <th>IMT</th>
                            <th>Hasil Skrining</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deteksi as $index => $d)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start">{{ $d->pasien->nama_lengkap ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
                                <td>{{ $d->tekanan_darah ?? '-' }}</td>
                                <td>{{ $d->gula_darah ?? '-' }}</td>
                                <td>{{ $d->kolesterol ?? '-' }}</td>
                                <td>{{ $d->imt ?? '-' }}</td>
                                <td>
                                    @php
                                        $color = match ($d->hasil_skrining) {
                                            'Normal' => 'success',
                                            'Risiko Tinggi' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $d->hasil_skrining }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#deteksiTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            info: true,
            searching: true,
            order: [[2, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
            }
        });
    });
</script>

<style>
    body {
        background-color: #f8fafc;
    }

    .container-fluid {
        margin-top: -10px; /* naikkan seluruh kontainer sedikit */
    }

    .card-body {
        padding-top: 20px;
        padding-bottom: 10px;
    }

    table th, table td {
        vertical-align: middle !important;
    }

    .table td.text-start {
        text-align: left !important;
    }
</style>
@endsection
