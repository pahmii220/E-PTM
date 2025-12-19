@extends('layouts.master')

@section('title', 'Daftar Petugas')

@section('content')
    <div class="container-fluid py-1 px-4" style="max-width: 1400px; margin: auto; margin-top: -10px;">

        {{-- Judul dan tombol --}}
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2" style="margin-top:-5px;">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Daftar Petugas</h2>

            <div class="d-flex gap-2">

                {{-- Tombol Cetak --}}
                <a href="{{ route('admin.data_petugas.print') }}" target="_blank"
                    class="btn btn-outline-primary fw-semibold shadow-sm">
                    <i class="bi bi-printer-fill"></i> Cetak Data
                </a>

                {{-- Tombol Tambah --}}
                <a href="{{ route('admin.data_petugas.create') }}" class="btn btn-success fw-semibold shadow-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Petugas
                </a>
            </div>
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
                    <table id="petugasTable" class="table table-striped table-hover align-middle text-center">
                        <thead class="bg-success text-white align-middle">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 12%">NIP</th>
                                <th style="width: 20%">Nama</th>
                                <th style="width: 15%">Jabatan</th>
                                <th style="width: 15%">Bidang</th>
                                <th style="width: 20%">Puskesmas</th> {{-- kolom baru --}}
                                <th style="width: 13%">Telepon</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($petugas as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start">{{ $p->nip }}</td>
                                    <td class="text-start">{{ $p->nama_pegawai }}</td>
                                    <td>{{ Str::limit($p->jabatan ?? '-', 25) }}</td>
                                    <td>{{ Str::limit($p->bidang ?? '-', 25) }}</td>
                                    <td class="text-start">
                                        {{ $p->puskesmas->nama_puskesmas ?? '-' }}
                                    </td>
                                    <td>{{ $p->telepon ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.data_petugas.edit', $p->id) }}" class="btn btn-sm btn-warning me-1"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.data_petugas.destroy', $p->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data petugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            @if($petugas->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center py-3">
                                        Belum ada data petugas.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        {{-- Pagination jika diperlukan --}}
        @if(method_exists($petugas, 'links'))
            <div class="mt-3">
                {{ $petugas->links() }}
            </div>
        @endif
    </div>
@endsection