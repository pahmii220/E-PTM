@extends('layouts.master')

@section('title', 'Daftar Pengguna Dinkes')

@section('content')
    <div class="container-fluid py-4">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#eef2ff,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h4 class="fw-bold mb-0">Daftar Pengguna</h4>
                    <small class="text-muted">Manajemen akun pegawai dinas kesehatan</small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    {{-- SEARCH --}}
                    <form method="GET">
                        <div class="input-group input-group-sm rounded-pill shadow-sm overflow-hidden">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0"
                                placeholder="Cari nama / NIP">
                        </div>
                    </form>

                    {{-- ADD BUTTON --}}
                    @if(auth()->user()->role_name === 'admin')
                        <a href="{{ route('admin.pengguna.create') }}"
                            class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tambah
                        </a>
                    @endif

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
                            <th class="text-start">Identitas</th>
                            <th class="text-start">Jabatan & Bidang</th>
                            <th class="text-start">Akun</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $u)
                                        <tr class="hover-shadow">

                                            {{-- NO --}}
                                            <td class="text-center text-muted">
                                                {{ $loop->iteration }}
                                            </td>

                                            {{-- IDENTITAS --}}
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $u->pegawaiDinkes->nama_pegawai
                            ?? $u->Nama_Lengkap
                            ?? $u->Username }}
                                                </div>
                                                <div class="text-muted small">
                                                    NIP • {{ $u->pegawaiDinkes->nip ?? $u->nip ?? '-' }}
                                                </div>
                                            </td>

                                            {{-- JABATAN --}}
                                            <td>
                                                <div>{{ $u->pegawaiDinkes->jabatan ?? '-' }}</div>
                                                <div class="text-muted small">
                                                    {{ $u->pegawaiDinkes->bidang ?? 'Bidang tidak tersedia' }}
                                                </div>
                                            </td>

                                            {{-- AKUN --}}
                                            <td>
                                                <div class="fw-medium">{{ $u->Username }}</div>
                                                <div class="text-muted small">{{ $u->email ?? '-' }}</div>
                                            </td>

                                            {{-- STATUS --}}
                                            <td class="text-center">
                                                <span
                                                    class="badge rounded-pill px-3 py-1
                                                    {{ $u->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    <i class="bi {{ $u->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                                <div class="text-muted small mt-1">
                                                    {{ ucfirst($u->role_name) }}
                                                </div>
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">

                                                    <a href="{{ route('admin.pengguna.edit', $u->id) }}"
                                                        class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit">
                                                        <i class="bi bi-pencil text-warning"></i>
                                                    </a>

                                                    <form action="{{ route('admin.pengguna.destroy', $u->id) }}" method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-3 d-block mb-2 opacity-50"></i>
                                    Belum ada data pengguna
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-4 d-flex justify-content-end">
            {{ $users->links() }}
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