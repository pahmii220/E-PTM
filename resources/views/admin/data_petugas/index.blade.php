@extends('layouts.master')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Daftar Petugas')

@section('content')
    <div class="container-fluid py-3">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0">Daftar Petugas</h4>
                    <small class="text-muted">Kelola data petugas dan status akun</small>
                </div>

                <div class="d-flex gap-2">
                    <form method="GET">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control" placeholder="Cari NIP / Nama">
                        </div>
                    </form>

                    <a href="{{ route('admin.data_petugas.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Tambah
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive p-0">

                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">No</th>
                            <th>Identitas</th>
                            <th>Jabatan & Bidang</th>
                            <th>Kontak</th>
                            <th>Puskesmas</th>
                            <th>Status Akun</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($petugas as $p)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>

                                {{-- IDENTITAS --}}
                                <td>
                                    <div class="fw-semibold">{{ $p->nama_pegawai }}</div>
                                    <div class="text-muted small">NIP: {{ $p->nip ?? '-' }}</div>
                                </td>

                                {{-- JABATAN & BIDANG --}}
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
                                            Lahir: {{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') }}
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
                                        <span class="badge {{ $p->user->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $p->user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <div class="small text-muted mt-1">
                                            Role: {{ ucfirst($p->user->role_name) }}
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Belum Ada Akun</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.data_petugas.edit', $p->id) }}" class="btn btn-sm btn-warning"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.data_petugas.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus data petugas?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Tidak ada data petugas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>


        {{-- ================= PAGINATION ================= --}}
        <div class="mt-3 d-flex justify-content-end">
            {{ $petugas->links() }}
        </div>

    </div>
@endsection