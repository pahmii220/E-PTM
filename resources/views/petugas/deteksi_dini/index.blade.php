@extends('layouts.master')

@section('title', 'Data Deteksi Dini PTM')

@section('content')
    <div class="container-fluid py-3 px-4" style="max-width: 1400px; margin: 0 auto;">
        {{-- Judul dan tombol --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Data Deteksi Dini PTM</h2>

            {{-- Tambah hanya jika belum diverifikasi (opsional, bisa tetap tampil) --}}
            <a href="{{ route('petugas.deteksi_dini.create') }}" class="btn btn-success fw-semibold shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
            </a>
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
                    <table id="deteksiTable" class="table table-striped table-hover align-middle text-center mb-0">
                        <thead class="bg-success text-white">
                            <tr>
                                <th>No</th>
                                <th>Nama Peserta</th>
                                <th>Tanggal Pemeriksaan</th>
                                <th>Tekanan Darah</th>
                                <th>Gula Darah</th>
                                <th>Kolesterol</th>
                                <th>Puskesmas</th>
                                <th>IMT</th>
                                <th>Hasil Skrining</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($deteksi as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td class="text-start">
                                        {{ $d->pasien->nama_lengkap ?? '-' }}
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
                                    <td>{{ $d->tekanan_darah ?? '-' }}</td>
                                    <td>{{ $d->gula_darah ?? '-' }}</td>
                                    <td>{{ $d->kolesterol ?? '-' }}</td>

                                    <td class="text-start">
                                        <strong>{{ $d->puskesmas->nama_puskesmas ?? '-' }}</strong><br>
                                        <small class="text-muted">
                                            {{ $d->puskesmas->kecamatan ?? '' }}
                                        </small>
                                    </td>

                                    <td>{{ $d->imt ?? '-' }}</td>

                                    {{-- Hasil skrining --}}
                                    <td>
                                        @php
                                            $color = match ($d->hasil_skrining) {
                                                'Normal' => 'success',
                                                'Risiko Tinggi' => 'danger',
                                                'Dicurigai PTM' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ $d->hasil_skrining }}</span>
                                    </td>

                                    {{-- STATUS VERIFIKASI --}}
                                    <td>
                                        @if ($d->verification_status === 'approved')
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif ($d->verification_status === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                            @if($d->verification_note)
                                                <div class="small text-danger mt-1">
                                                    Alasan: {{ $d->verification_note }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-warning text-dark">Tertunda</span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td>
    @if(auth()->user()->role_name === 'admin')
        {{-- ADMIN: SELALU BISA --}}
        <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}"
            class="btn btn-warning btn-sm me-1">
            <i class="bi bi-pencil-square"></i>
        </a>

        <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}"
            method="POST" class="d-inline"
            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @else
        {{-- PETUGAS --}}
        @if($d->verification_status === 'pending')
            <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}"
                class="btn btn-warning btn-sm me-1">
                <i class="bi bi-pencil-square"></i>
            </a>

            <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}"
                method="POST" class="d-inline"
                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        @else
            <span class="text-muted small">Terkunci</span>
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
                order: [[2, 'desc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });
        });
    </script>

    <style>
        body {
            background-color: #f8fafc;
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