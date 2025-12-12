@extends('layouts.master')

@section('title', 'Daftar Puskesmas')

@section('content')
    <div class="container-fluid py-1 px-4" style="max-width: 1400px; margin: auto; margin-top: -10px;">

        {{-- Header: judul + tools --}}
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2" style="margin-top:-5px;">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Daftar Puskesmas</h2>

            <div class="d-flex gap-2">
                {{-- Form pencarian --}}
                <form action="{{ route('admin.data_puskesmas.index') }}" method="GET" class="d-flex">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm"
                        placeholder="Cari kode / nama / kabupaten" style="min-width:260px;">
                    <button class="btn btn-outline-secondary btn-sm ms-2">Cari</button>
                </form>

                {{-- Tombol Cetak --}}
                <a href="{{ route('admin.data_puskesmas.print') }}" target="_blank"
                    class="btn btn-outline-primary fw-semibold shadow-sm">
                    <i class="bi bi-printer-fill"></i> Cetak Data
                </a>


                {{-- Tombol Tambah --}}
                <a href="{{ route('admin.data_puskesmas.create') }}" class="btn btn-success fw-semibold shadow-sm">
                    <i class="bi bi-plus-circle"></i> Tambah Puskesmas
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
                    <table class="table table-striped table-hover align-middle text-center">
                        <thead class="bg-success text-white align-middle">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 12%">Kode</th>
                                <th style="width: 28%">Nama Puskesmas</th>
                                <th style="width: 15%">Kabupaten</th>
                                <th style="width: 15%">Kecamatan</th>
                                <th style="width: 13%">Email</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 + (($data->currentPage() - 1) * $data->perPage()) }}</td>
                                    <td class="text-start">{{ $item->kode_puskesmas }}</td>
                                    <td class="text-start">{{ $item->nama_puskesmas }}</td>
                                    <td>{{ Str::limit($item->nama_kabupaten ?? '-', 30) }}</td>
                                    <td>{{ Str::limit($item->kecamatan ?? '-', 30) }}</td>
                                    <td class="text-start">{{ $item->email ?? '-' }}</td>

                                    <td>
                                        <a href="{{ route('admin.data_puskesmas.edit', $item->id) }}"
                                            class="btn btn-sm btn-warning me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.data_puskesmas.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data puskesmas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">Belum ada data puskesmas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer tabel: info & pagination --}}
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $data->firstItem() ?: 0 }} - {{ $data->lastItem() ?: 0 }} dari
                            {{ $data->total() }} data
                        </div>
                        <div>{{ $data->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection