@extends('layouts.master')

@section('title', 'Daftar Pegawai Dinas Kesehatan')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="header-text">
                    <h1>Daftar Pegawai</h1>
                    <p>Manajemen akun pegawai dan wilayah penugasan Dinas Kesehatan.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                {{-- SEARCH --}}
                <form method="GET" action="{{ url()->current() }}" class="m-0">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="q" value="{{ request('q') }}" class="search-input"
                            placeholder="Cari nama atau NIP...">
                    </div>
                </form>

                {{-- ADD BUTTON --}}
                @if(auth()->check() && auth()->user()->role_name === 'admin')
                    <a href="{{ route('admin.pengguna.create') }}" class="btn-action-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Pegawai
                    </a>
                @endif
            </div>

        </div>

        {{-- ================= TABLE ================= --}}
        <div class="data-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0 custom-table">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">Identitas Pegawai</th>
                            <th width="20%">Jabatan & Bidang</th>
                            <th width="20%">Wilayah Kerja</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengguna as $u)
                            <tr>
                                {{-- NO --}}
                                <td class="text-center text-secondary fw-medium">
                                    {{ $pengguna->firstItem() + $loop->index }}
                                </td>

                                {{-- IDENTITAS & FOTO --}}
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        {{-- FOTO PROFILE --}}
                                        <div class="avatar-wrapper">
                                            @if($u->pegawaiDinkes && $u->pegawaiDinkes->foto)
                                                <img src="{{ asset('storage/' . $u->pegawaiDinkes->foto) }}"
                                                    alt="Foto {{ $u->Username }}" class="avatar-img">
                                            @else
                                                <div class="avatar-placeholder">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- DETAIL IDENTITAS --}}
                                        <div>
                                            <div class="fw-bold text-dark mb-1">
                                                {{ $u->pegawaiDinkes->nama_pegawai ?? $u->Nama_Lengkap ?? $u->Username }}
                                            </div>
                                            <div class="text-muted small mb-1 d-flex align-items-center gap-1">
                                                <i class="bi bi-credit-card-2-front"></i>
                                                NIP: {{ $u->pegawaiDinkes->nip ?? $u->nip ?? '-' }}
                                            </div>
                                            <div class="text-primary small fw-medium">
                                                <i class="bi bi-envelope"></i> {{ $u->email ?? 'Tidak ada email' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- JABATAN --}}
                                <td>
                                    <div class="fw-medium text-dark mb-1">
                                        {{ $u->pegawaiDinkes->jabatan ?? '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-building"></i>
                                        {{ $u->pegawaiDinkes->bidang ?? 'Bidang tidak tersedia' }}
                                    </div>
                                </td>

                                {{-- WILAYAH KERJA (TAMBAHAN BARU) --}}
                                <td>
                                    <div class="fw-medium text-dark mb-1 text-capitalize">
                                        <i class="bi bi-geo-alt-fill text-danger small"></i>
                                        {{ strtolower($u->pegawaiDinkes->kabupaten_kota ?? '-') }}
                                    </div>
                                    <div class="text-muted small text-capitalize" style="padding-left: 14px;">
                                        Prov. {{ strtolower($u->pegawaiDinkes->provinsi ?? '-') }}
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="text-center">
                                    <span class="status-badge {{ $u->status_aktif ? 'status-active' : 'status-inactive' }}">
                                        <i class="bi {{ $u->status_aktif ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                        {{ $u->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <div class="text-muted small mt-2 fw-medium text-uppercase"
                                        style="font-size: 11px; letter-spacing: 0.5px;">
                                        {{ $u->role_name }}
                                    </div>
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.pengguna.edit', $u->id) }}" class="btn-icon btn-edit"
                                            title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('admin.pengguna.destroy', $u->id) }}" method="POST" class="m-0"
                                            onsubmit="return confirm('Peringatan: Apakah Anda yakin ingin menghapus permanen data pegawai ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete" title="Hapus Data">
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
                                        <i class="bi bi-people text-muted"></i>
                                        <h5>Belum ada data pegawai</h5>
                                        <p>Data pegawai Dinas Kesehatan yang Anda tambahkan akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-4 d-flex justify-content-end custom-pagination">
            {{ $pengguna->withQueryString()->links() }}
        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4338ca;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.1);
        }

        .header-text h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .header-text p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        /* SEARCH BOX */
        .search-box {
            position: relative;
            width: 260px;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        /* PRIMARY BUTTON */
        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }

        .btn-action-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-1px);
            color: white;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }

        /* AVATAR STYLE (FOTO PEGAWAI) */
        .avatar-wrapper {
            position: relative;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background-color: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
        }

        /* CARD & TABLE */
        .data-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .custom-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .custom-table td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .custom-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .status-inactive {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* ACTION BUTTONS */
        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-edit {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .btn-edit:hover {
            background-color: #fef3c7;
            color: #b45309;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-delete:hover {
            background-color: #fee2e2;
            color: #b91c1c;
            transform: translateY(-2px);
        }

        /* EMPTY STATE */
        .empty-state {
            padding: 40px 0;
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
            display: inline-block;
        }

        .empty-state h5 {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }

        /* ALERTS */
        .alert-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: none;
            font-weight: 500;
        }

        .alert-success.alert-modern {
            background-color: #ecfdf5;
            color: #065f46;
            border-left: 5px solid #10b981;
        }

        .alert-danger.alert-modern {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 5px solid #ef4444;
        }
    </style>
@endsection