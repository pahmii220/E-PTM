@extends('layouts.master')

@section('title', 'Daftar Petugas')

@section('content')
    <div class="container-fluid py-4">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#ecfeff,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h4 class="fw-bold mb-0">Daftar Petugas</h4>
                    <small class="text-muted">Kelola data petugas dan status akun</small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    {{-- SEARCH --}}
                    <form method="GET">
                        <div class="input-group input-group-sm rounded-pill shadow-sm overflow-hidden">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0"
                                placeholder="Cari NIP / Nama">
                        </div>
                    </form>

                    {{-- ADD BUTTON --}}
                    <a href="{{ route('admin.data_petugas.create') }}"
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
                            <th class="text-start">Identitas</th>
                            <th class="text-start">Jabatan</th>
                            <th class="text-start">Kontak</th>
                            <th class="text-start">Puskesmas</th>
                            <th>Status Akun</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($petugas as $p)
                            <tr class="hover-shadow">

                                {{-- NO --}}
                                <td class="text-center text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- IDENTITAS --}}
                                <td>
                                    <div class="fw-semibold">{{ $p->nama_pegawai }}</div>
                                    <div class="text-muted small">
                                        NIP • {{ $p->nip ?? '-' }}
                                    </div>
                                </td>

                                {{-- JABATAN --}}
                                <td>
                                    <div>{{ $p->jabatan ?? '-' }}</div>
                                    <div class="text-muted small">
                                        {{ $p->bidang ?? 'Bidang tidak tersedia' }}
                                    </div>
                                </td>

                                {{-- KONTAK --}}
                                <td>
                                    <div>{{ $p->telepon ?? '-' }}</div>
                                    @if($p->tanggal_lahir)
                                        <div class="text-muted small">
                                            Lahir • {{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') }}
                                        </div>
                                    @endif
                                </td>

                                {{-- PUSKESMAS --}}
                                <td>
                                    {{ $p->puskesmas->nama_puskesmas ?? '-' }}
                                </td>

                                {{-- STATUS AKUN --}}
                                <td class="text-center">
                                    @if($p->user)
                                                        <span class="badge rounded-pill px-3 py-1
                                                                {{ $p->user->is_active
                                        ? 'bg-success-subtle text-success'
                                        : 'bg-danger-subtle text-danger' }}">
                                                            <i class="bi {{ $p->user->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                                            {{ $p->user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                        <div class="small text-muted mt-1">
                                                            {{ ucfirst($p->user->role_name) }}
                                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                                            Belum Ada Akun
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('admin.data_petugas.edit', $p->id) }}"
                                            class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit">
                                            <i class="bi bi-pencil text-warning"></i>
                                        </a>

                                        <form action="{{ route('admin.data_petugas.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data petugas?')">
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
                                    <i class="bi bi-person-badge fs-3 d-block mb-2 opacity-50"></i>
                                    Tidak ada data petugas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-4 d-flex justify-content-end">
            {{ $petugas->links() }}
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