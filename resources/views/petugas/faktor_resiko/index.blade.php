@extends('layouts.master')

@section('title', 'Data Faktor Risiko PTM')

@section('content')
<div class="container-fluid py-3 px-4" style="max-width: 1400px; margin: 0 auto;">

    {{-- Judul dan tombol --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="fs-3 fw-bold text-gray-800 mb-0">Data Faktor Risiko PTM</h2>
        <a href="{{ route('petugas.faktor_resiko.create') }}" class="btn btn-success fw-semibold shadow-sm">
            <i class="bi bi-plus-circle"></i> Tambah Data
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
                <table id="faktorTable" class="table table-striped table-hover align-middle text-center mb-0">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Merokok</th>
                            <th>Alkohol</th>
                            <th>Kurang Aktivitas Fisik</th>
                            <th>Puskesmas</th>
                            <th>Status</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($faktor as $index => $f)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td class="text-start">
                                    {{ $f->pasien->nama_lengkap ?? '-' }}
                                </td>

                                <td>{{ \Carbon\Carbon::parse($f->tanggal_pemeriksaan)->format('d-m-Y') }}</td>

                                <td>
                                    <span class="badge bg-{{ $f->merokok == 'Ya' ? 'danger' : 'success' }}">
                                        {{ $f->merokok }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $f->alkohol == 'Ya' ? 'danger' : 'success' }}">
                                        {{ $f->alkohol }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $f->kurang_aktivitas_fisik == 'Ya' ? 'warning' : 'success' }}">
                                        {{ $f->kurang_aktivitas_fisik }}
                                    </span>
                                </td>

                                <td class="text-start">
                                    {{ $f->puskesmas->nama_puskesmas ?? '-' }}
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if ($f->verification_status === 'approved')
                                        <span class="badge bg-success">Diterima</span>
                                    @elseif ($f->verification_status === 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Tertunda</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td>
                                    @if(auth()->user()->role_name === 'admin')
                                        {{-- ADMIN: SELALU BISA --}}
                                        <a href="{{ route('petugas.faktor_resiko.edit', $f->id) }}"
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('petugas.faktor_resiko.destroy', $f->id) }}"
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
                                        @if(in_array($f->verification_status, ['pending','rejected']))
                                            <a href="{{ route('petugas.faktor_resiko.edit', $f->id) }}"
                                                class="btn btn-warning btn-sm me-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('petugas.faktor_resiko.destroy', $f->id) }}"
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
    $('#faktorTable').DataTable({
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
@endsection
