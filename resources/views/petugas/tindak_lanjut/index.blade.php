@extends('layouts.master')

@section('title', 'Data Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-3 px-4" style="max-width: 1400px; margin: 0 auto;">

        {{-- Judul --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Data Tindak Lanjut PTM</h2>

        @if(auth()->user()->role_name === 'petugas')
    @if($deteksiDiniTerbaru)
        <a href="{{ route('petugas.tindak_lanjut.create', $deteksiDiniTerbaru->id) }}"
            class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-plus-circle"></i> Tambah Tindak Lanjut
        </a>
    @else
        <button class="btn btn-secondary" disabled>
            Belum ada Deteksi Dini
        </button>
    @endif
@endif



        </div>

        <br>

        {{-- Notifikasi sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Card tabel --}}
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="tindakLanjutTable" class="table table-striped table-hover align-middle text-center mb-0">
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
                            @foreach($tindakLanjut as $index => $t)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>

                                                        <td class="text-start">
                                                            {{ $t->pasien->nama_lengkap ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ \Carbon\Carbon::parse(
        $t->deteksiDini->tanggal_pemeriksaan
    )->format('d-m-Y') }}
                                                        </td>

                                                        <td>
                                                            @php
    $jenis = [
        'edukasi' => 'Edukasi',
        'anjuran_gaya_hidup' => 'Anjuran Gaya Hidup',
        'rujukan' => 'Rujukan',
        'monitoring' => 'Monitoring',
        'tidak_ada' => 'Tidak Ada'
    ];
                                                            @endphp
                                                            {{ $jenis[$t->jenis_tindak_lanjut] ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ $t->tanggal_tindak_lanjut
        ? \Carbon\Carbon::parse($t->tanggal_tindak_lanjut)->format('d-m-Y')
        : '-' }}
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $t->status_tindak_lanjut == 'sudah' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($t->status_tindak_lanjut) }}
                                                            </span>
                                                        </td>

                                                        <td class="text-start">
                                                            {{ $t->catatan_petugas ?? '-' }}
                                                        </td>

                                                        <td>
    @if(auth()->user()->role_name === 'petugas')
        <a href="{{ route('petugas.tindak_lanjut.edit', $t->id) }}"
            class="btn btn-warning btn-sm me-1">
            <i class="bi bi-pencil-square"></i>
        </a>

        <form action="{{ route('petugas.tindak_lanjut.destroy', $t->id) }}"
            method="POST" class="d-inline"
            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @else
        <span class="text-muted small">Monitoring</span>
    @endif
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
            $('#tindakLanjutTable').DataTable({
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
            margin-top: -10px;
        }

        table th,
        table td {
            vertical-align: middle !important;
        }

        .table td.text-start {
            text-align: left !important;
        }
    </style>
@endsection