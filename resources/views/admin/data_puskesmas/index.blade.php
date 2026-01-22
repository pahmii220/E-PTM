@extends('layouts.master')

@section('title', 'Daftar Puskesmas')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#ecfdf5,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h4 class="fw-bold mb-0">Daftar Puskesmas</h4>
                    <small class="text-muted">Kelola data puskesmas dan informasi wilayah</small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    {{-- SEARCH --}}
                    <form action="{{ route('admin.data_puskesmas.index') }}" method="GET">
                        <div class="input-group input-group-sm rounded-pill shadow-sm overflow-hidden">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0"
                                placeholder="Cari kode / nama / kabupaten">
                        </div>
                    </form>

                    {{-- ADD BUTTON --}}
                    <a href="{{ route('admin.data_puskesmas.create') }}"
                        class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah
                    </a>

                </div>
            </div>
        </div>


        {{-- ================= TABLE ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead class="bg-light text-muted small">
                        <tr class="text-center">
                            <th width="40">No</th>
                            <th class="text-start">Kode</th>
                            <th class="text-start">Nama Puskesmas</th>
                            <th>Kabupaten</th>
                            <th>Kecamatan</th>
                            <th class="text-start">Email</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $index => $item)
                            <tr class="hover-shadow">

                                <td class="text-center text-muted">
                                    {{ $index + 1 + (($data->currentPage() - 1) * $data->perPage()) }}
                                </td>

                                <td class="text-start fw-medium">
                                    {{ $item->kode_puskesmas }}
                                </td>

                                <td class="text-start fw-semibold">
                                    {{ $item->nama_puskesmas }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($item->nama_kabupaten ?? '-', 30) }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($item->kecamatan ?? '-', 30) }}
                                </td>

                                <td class="text-start">
                                    {{ $item->email ?? '-' }}
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('admin.data_puskesmas.edit', $item->id) }}"
                                            class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit">
                                            <i class="bi bi-pencil text-warning"></i>
                                        </a>

                                        <form action="{{ route('admin.data_puskesmas.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data puskesmas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" title="Hapus">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-hospital fs-3 d-block mb-2 opacity-50"></i>
                                    Belum ada data puskesmas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>


        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        .hover-shadow:hover {
            background: #f8fafc;
            transition: all .2s ease;
        }
    </style>
@endsection