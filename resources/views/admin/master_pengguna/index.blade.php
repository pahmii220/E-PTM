@extends('layouts.master')

@section('title', 'Data Semua Pengguna')

@section('content')
<div class="container-fluid px-md-5 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="page-header d-flex align-items-center gap-3">
            <div class="header-icon" style="width: 56px; height: 56px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4338ca; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; box-shadow: 0 4px 12px rgba(67, 56, 202, 0.1);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="header-text">
                <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px; letter-spacing: -0.5px;">Data Seluruh Pengguna</h1>
                <p style="font-size: 14px; color: #64748b; margin: 0;">Kelola akun master (Admin, Kepala, Pegawai, Petugas) dari satu pintu.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            {{-- FORM PENCARIAN --}}
            <form action="{{ route('admin.master_pengguna.index') }}" method="GET" class="d-flex" id="searchForm" style="min-width: 250px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, username..." value="{{ request('q') }}" style="border-radius: 0 10px 10px 0;" autocomplete="off">
                </div>
            </form>

            <a href="{{ route('admin.master_pengguna.create') }}" class="btn btn-primary shadow-sm" style="background-color: #4f46e5; border:none; padding: 10px 20px; border-radius: 10px; font-weight: 600;">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>



    {{-- TABLE --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center py-3 ps-3">No</th>
                        <th width="30%" class="py-3">Identitas Akun</th>
                        <th width="20%" class="py-3">Email / Kontak</th>
                        <th width="15%" class="py-3">Role Akses</th>
                        <th width="15%" class="text-center py-3">Status Akun</th>
                        <th width="15%" class="text-center py-3 pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($pengguna as $u)
                        <tr>
                            <td class="text-center text-secondary fw-medium ps-3">{{ $pengguna->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $u->Nama_Lengkap ?? $u->Username }}</div>
                                <div class="text-muted small"><i class="bi bi-person-badge text-primary me-1"></i> {{ $u->Username }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $u->email ?? '-' }}</div>
                            </td>
                            <td>
                                @if($u->role_name == 'admin')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="bi bi-shield-lock-fill me-1"></i> Administrator</span>
                                @elseif($u->role_name == 'kepala_p2ptm')
                                    <span class="badge bg-purple bg-opacity-10 text-purple border border-purple px-3 py-2 rounded-pill" style="color: #6f42c1; border-color: #6f42c1;"><i class="bi bi-person-lines-fill me-1"></i> Kepala P2PTM</span>
                                @elseif($u->role_name == 'pegawai')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill"><i class="bi bi-briefcase-fill me-1"></i> Pegawai Dinkes</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="bi bi-clipboard2-pulse me-1"></i> Petugas</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.master_pengguna.updateAccess', $u->id) }}" method="POST" class="d-flex flex-column align-items-center m-0">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_aktif" value="{{ $u->status_aktif ? 0 : 1 }}">
                                    <div class="form-check form-switch m-0 p-0 d-flex justify-content-center" style="min-height: 1.5rem;">
                                        <input class="form-check-input shadow-sm m-0" type="checkbox" role="switch" 
                                            {{ $u->status_aktif ? 'checked' : '' }} 
                                            {{ auth()->id() == $u->id ? 'disabled' : '' }}
                                            onchange="this.form.submit()" 
                                            style="cursor: pointer; width: 2.5rem; height: 1.25rem;"
                                            title="{{ $u->status_aktif ? 'Klik untuk Menonaktifkan' : 'Klik untuk Mengaktifkan' }}">
                                    </div>
                                    <span class="badge {{ $u->status_aktif ? 'bg-success' : 'bg-secondary' }} mt-2" style="font-size: 10px; width: 65px;">
                                        {{ $u->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </form>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.master_pengguna.edit', $u->id) }}" class="btn btn-light btn-sm rounded-3 border text-primary" title="Edit Akun">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.master_pengguna.destroy', $u->id) }}" method="POST" class="m-0" onsubmit="return confirm('Peringatan: Apakah Anda yakin ingin menghapus permanen data akun ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm rounded-3 border text-danger" title="Hapus Akun" {{ auth()->id() == $u->id ? 'disabled' : '' }}>
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                    <h5 class="mt-3">Belum ada data pengguna</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 d-flex justify-content-end custom-pagination">
        {{ $pengguna->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        let debounceTimer;

        // Auto-focus input on load so typing can continue smoothly
        const val = searchInput.value;
        searchInput.focus();
        searchInput.setSelectionRange(val.length, val.length);

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchForm.submit();
            }, 600); // 600ms delay after user stops typing
        });
    });
</script>
@endpush
