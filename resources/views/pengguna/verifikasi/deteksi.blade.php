@extends('layouts.master')

@section('title', 'Verifikasi - Deteksi Dini')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h4 class="fw-bold mb-0">Verifikasi Deteksi Dini</h4>
                    <small class="text-muted">
                        Kelola persetujuan data deteksi dini dari petugas
                    </small>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    {{-- CETAK --}}
                    <a href="{{ route('pengguna.verifikasi.print.deteksi', ['status' => $status ?? 'pending']) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>

                    {{-- FILTER --}}
                    <form method="GET">
                        <select name="status" class="form-select form-select-sm rounded-pill shadow-sm"
                            onchange="this.form.submit()">
                            <option value="pending" {{ ($status ?? 'pending') == 'pending' ? 'selected' : '' }}>Tertunda
                            </option>
                            <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="all" {{ ($status ?? '') == 'all' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </form>
                </div>

            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Peserta</th>
                            <th>Tanggal</th>
                            <th>Tekanan</th>
                            <th>Gula</th>
                            <th>Puskesmas</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">
                                    {{ $loop->iteration + (($data->currentPage() - 1) * $data->perPage()) }}
                                </td>

                                <td class="fw-semibold">
                                    {{ optional($row->pasien)->nama_lengkap ?? '-' }}
                                </td>

                                <td>{{ $row->tanggal_pemeriksaan?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $row->tekanan_darah ?? '-' }}</td>
                                <td>{{ $row->gula_darah ?? '-' }}</td>
                                <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>

                                {{-- STATUS (SAMA DENGAN PASIEN) --}}
                                <td>
                                    @if ($row->verification_status === 'approved')
                                        <span class="status-badge status-approved">
                                            <i class="bi bi-check-circle"></i> Diterima
                                        </span>
                                    @elseif ($row->verification_status === 'rejected')
                                        <span class="status-badge status-rejected">
                                            <i class="bi bi-x-circle"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="status-badge status-pending">
                                            <i class="bi bi-clock-history"></i> Tertunda
                                        </span>
                                    @endif
                                </td>

                                <td class="text-muted small">
                                    {{ $row->created_at->format('d-m-Y H:i') }}
                                </td>

                                {{-- AKSI (SAMA DENGAN PASIEN) --}}
                                <td class="text-end pe-4">
                                    @if ($row->verification_status === 'pending')

                                        <button class="btn btn-success btn-sm rounded-circle me-1" title="Terima"
                                            data-bs-toggle="modal" data-bs-target="#verifyModal" data-id="{{ $row->id }}"
                                            data-type="deteksi" data-action="approve">
                                            <i class="bi bi-check-lg"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm rounded-circle" title="Tolak" data-bs-toggle="modal"
                                            data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="deteksi"
                                            data-action="reject">
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data deteksi dini ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3">
            {{ $data->links() }}
        </div>

    </div>

    @include('pengguna.verifikasi._modal_verify')

    {{-- ================= STYLE ================= --}}
    <style>
        body {
            background-color: #f8fafc;
        }

        .table th {
            font-weight: 600
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
        }

        .status-pending {
            background: #eef2f7;
            color: #475569;
        }

        .status-approved {
            background: #e6f6ef;
            color: #047857;
        }

        .status-rejected {
            background: #fdecec;
            color: #b91c1c;
        }
    </style>
@endsection