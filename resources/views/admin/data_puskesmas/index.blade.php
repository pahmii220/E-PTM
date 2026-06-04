@extends('layouts.master')

@section('title', 'Daftar Puskesmas')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-hospital"></i>
                </div>
                <div class="header-text">
                    <h1>Daftar Puskesmas</h1>
                    <p>Kelola data puskesmas dan informasi wilayah operasional.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                {{-- SEARCH --}}
                <form method="GET" action="{{ route('admin.data_puskesmas.index') }}" class="m-0">
                    <div class="search-box">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="q" value="{{ request('q') }}" class="search-input"
                            placeholder="Cari kode atau nama...">
                    </div>
                </form>

                {{-- ADD BUTTON --}}
                <a href="{{ route('admin.data_puskesmas.create') }}" class="btn-action-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Puskesmas
                </a>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="data-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0 custom-table">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode</th>
                            <th width="25%">Nama Puskesmas</th>
                            <th width="20%">Kabupaten</th>
                            <th width="15%">Kecamatan</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td class="text-center text-secondary fw-medium">
                                    {{ $index + 1 + (($data->currentPage() - 1) * $data->perPage()) }}
                                </td>
                                <td class="fw-bold text-primary">{{ $item->kode_puskesmas }}</td>
                                <td class="fw-bold text-dark">{{ $item->nama_puskesmas }}</td>
                                <td class="text-muted">{{ $item->nama_kabupaten ?? '-' }}</td>
                                <td class="text-muted">{{ $item->kecamatan ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.data_puskesmas.edit', $item->id) }}" class="btn-icon btn-edit"
                                            title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.data_puskesmas.destroy', $item->id) }}" method="POST"
                                            class="m-0" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf @method('DELETE')
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
                                        <i class="bi bi-hospital text-muted"></i>
                                        <h5>Belum ada data puskesmas</h5>
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
            {{ $data->withQueryString()->links() }}
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

        /* AVATAR PLACEHOLDER */
        .avatar-placeholder {
            width: 42px;
            height: 42px;
            background-color: #f1f5f9;
            color: #94a3b8;
            flex-shrink: 0;
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
    </style>
@endsection