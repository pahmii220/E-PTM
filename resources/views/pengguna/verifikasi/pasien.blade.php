@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h4 class="fw-bold mb-0">Verifikasi Data Peserta</h4>
                    <small class="text-muted">
                        Kelola persetujuan data peserta dari petugas
                    </small>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    {{-- CETAK --}}
                    <a href="{{ route('pengguna.verifikasi.print.pasien', ['status' => $status]) }}"
                        class="btn btn-outline-primary btn-sm rounded-pill shadow-sm" target="_blank">
                        <i class="bi bi-printer"></i> Cetak
                    </a>

                    {{-- FILTER --}}
                    <form method="GET">
                        <select name="status"
                            class="form-select form-select-sm rounded-pill shadow-sm"
                            onchange="this.form.submit()">
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Tertunda</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
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
                            <th>Nama</th>
                            <th>No RM</th>
                            <th>Kontak</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td class="ps-4">{{ $loop->iteration }}</td>

                            <td class="fw-semibold">{{ $row->nama_lengkap }}</td>

                            <td>{{ $row->no_rekam_medis }}</td>

                            <td>{{ $row->kontak }}</td>

                        <td>
                            @if ($row->verification_status === 'approved')
                                <span class="status-badge status-approved">
                                    <i class="bi bi-check-circle"></i>
                                    Diterima
                                </span>
                            @elseif ($row->verification_status === 'rejected')
                                <span class="status-badge status-rejected">
                                    <i class="bi bi-x-circle"></i>
                                    Ditolak
                                </span>
                            @else
                                <span class="status-badge status-pending">
                                    <i class="bi bi-clock-history"></i>
                                    Tertunda
                                </span>
                            @endif
                        </td>


                            <td class="text-muted small">
                                {{ $row->created_at->format('d-m-Y H:i') }}
                            </td>

                            <td class="text-end pe-4">

                                {{-- DETAIL --}}
                                <a href="{{ route('pengguna.verifikasi.pasien.show', $row->id) }}"
                                    class="btn btn-outline-primary btn-sm rounded-circle me-1"
                                    title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- VERIFIKASI --}}
                                @if ($row->verification_status === 'pending')

                                    <button class="btn btn-success btn-sm rounded-circle me-1"
                                        title="Terima"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal"
                                        data-id="{{ $row->id }}"
                                        data-type="pasien"
                                        data-action="approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm rounded-circle"
                                        title="Tolak"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal"
                                        data-id="{{ $row->id }}"
                                        data-type="pasien"
                                        data-action="reject">
                                        <i class="bi bi-x-lg"></i>
                                    </button>

                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                Tidak ada data peserta ditemukan
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

    {{-- STYLE TAMBAHAN --}}
    <style>
        body { background-color:#f8fafc; }
        .table th { font-weight:600 }

    </style>
    <style>
        /* Badge dasar */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: .2px;
        }

        /* Pending */
        .status-pending {
            background-color: #eef2f7;
            color: #475569;
        }

        /* Approved */
        .status-approved {
            background-color: #e6f6ef;
            color: #047857;
        }

        /* Rejected */
        .status-rejected {
            background-color: #fdecec;
            color: #b91c1c;
        }

        /* Ikon */
        .status-badge i {
            font-size: 0.9rem;
        }
    </style>

@endsection

