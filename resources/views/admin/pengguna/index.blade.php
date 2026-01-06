@extends('layouts.master')

@section('title', 'Daftar Pengguna Dinkes')

@section('content')
    <div class="container-fluid py-3">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="fw-bold mb-0">Daftar Pengguna (Pegawai Dinkes)</h4>
                <small class="text-muted">
                    Kelola akun dan data pegawai dinas kesehatan
                </small>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle table-sm">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="40">No</th>
                            <th>Nama Pegawai</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Bidang</th>
                            <th>Status Akun</th>
                            <th>Role</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $u)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>

                                            {{-- NAMA --}}
                                            <td>
                                                {{
                            $u->pegawaiDinkes->nama_pegawai
                            ?? $u->Nama_Lengkap
                            ?? $u->Username
                                                }}
                                            </td>

                                            {{-- NIP --}}
                                            <td>
                                                {{ $u->pegawaiDinkes->nip ?? $u->nip ?? '-' }}
                                            </td>

                                            {{-- JABATAN --}}
                                            <td>
                                                {{ $u->pegawaiDinkes->jabatan ?? '-' }}
                                            </td>

                                            {{-- BIDANG --}}
                                            <td>
                                                {{ $u->pegawaiDinkes->bidang ?? '-' }}
                                            </td>

                                            {{-- STATUS AKUN --}}
                                            <td class="text-center">
                                                <span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>

                                            {{-- ROLE --}}
                                            <td class="text-center">
                                                {{ ucfirst($u->role_name) }}
                                            </td>

                                            {{-- AKSI --}}
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.pengguna.edit', $u->id) }}" class="btn btn-sm btn-warning"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('admin.pengguna.destroy', $u->id) }}" method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
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
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                    Tidak ada data pengguna
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-3 d-flex justify-content-end">
            {{ $users->links() }}
        </div>

    </div>
@endsection