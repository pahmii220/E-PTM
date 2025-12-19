@extends('layouts.master')

@section('title', 'Data Pasien')

@section('content')
    <div class="container-fluid py-1 px-4" style="max-width: 1400px; margin: auto; margin-top: -10px;">
        {{-- Judul dan tombol --}}
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2" style="margin-top:-5px;">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Daftar Peserta</h2>
            <a href="{{ route('petugas.pasien.create') }}" class="btn btn-success fw-semibold shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Peserta
            </a>
        </div>

        {{-- Notifikasi sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mt-1 mb-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            <br><br>
        {{-- Card tabel --}}
        <div class="card shadow-lg border-0 rounded-3 mt-n4">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="pasienTable" class="table table-striped table-hover align-middle text-center">
                        <thead class="bg-success text-white align-middle">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 18%">Nama Lengkap</th>
                                <th style="width: 13%">No. Rekam Medis</th>
                                <th style="width: 13%">Tanggal Lahir</th>
                                <th style="width: 10%">Jenis Kelamin</th>
                                <th style="width: 20%">Alamat</th>
                                <th style="width: 10%">Kontak</th>
                                <th style="width: 10%">Puskesmas</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasien as $index => $p)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td class="text-start">{{ $p->nama_lengkap }}</td>
                                                                <td>{{ $p->no_rekam_medis }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') }}</td>
                                                                <td>
                                                                    @php
                                $badge = $p->jenis_kelamin === 'Laki-laki' ? 'primary' : 'pink';
                                                                    @endphp
                                                                    <span class="badge bg-{{ $badge }}">{{ $p->jenis_kelamin }}</span>
                                                                </td>
                                                                <td class="text-start">{{ Str::limit($p->alamat, 40, '...') }}</td>
                                                                <td>{{ $p->kontak }}</td>
                                                                <td>
                                                                    {{ $p->puskesmas->nama_puskesmas ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('petugas.pasien.edit', $p->id) }}"
                                                                        class="btn btn-sm btn-warning me-1">
                                                                        <i class="bi bi-pencil-square"></i>
                                                                    </a>
                                                                    <form action="{{ route('petugas.pasien.destroy', $p->id) }}" method="POST"
                                                                        class="d-inline"
                                                                        onsubmit="return confirm('Yakin ingin menghapus data pasien ini?')">
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
    </div>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#pasienTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                info: true,
                searching: true,
                lengthChange: true,
                scrollX: false,
                order: [[1, 'asc']],
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

        .card {
            overflow: hidden;
            margin-top: -25px !important;
            /* Naikkan tabel lebih dekat ke header */
        }

        table {
            width: 100%;
            font-size: 0.9rem;
        }

        table th,
        table td {
            vertical-align: middle !important;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* Warna badge perempuan */
        .badge.bg-pink {
            background-color: #e83e8c !important;
        }

        @media (min-width: 1400px) {
            .container-fluid {
                max-width: 1500px !important;
            }

            table th,
            table td {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 991px) {
            .container-fluid {
                padding: 10px;
            }

            table {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection