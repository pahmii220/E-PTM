@extends('layouts.master')

@section('content')
    <div class="container py-3">

        <!-- HEADER: Judul di Kiri, Tools (Cetak & Filter) di Kanan -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Verifikasi - Data Peserta</h4>
            <br>
            <br> 
            <div class="d-flex gap-2 align-items-center">
                {{-- Tombol Cetak Semua --}}
                <a href="{{ route('pengguna.verifikasi.print.pasien', ['status' => $status]) }}"
                    class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Cetak
                </a>

                {{-- Filter Status --}}
                <form method="GET" class="d-flex">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="min-width: 120px;">
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Tertunda</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Nama</th>
                                <th>No RM</th>
                                <th>Kontak</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                    <td>{{ $row->nama_lengkap }}</td>
                                    <td>{{ $row->no_rekam_medis }}</td>
                                    <td>{{ $row->kontak }}</td>

                                    <td>
                                        @if ($row->verification_status == 'approved')
                                            <span class="badge bg-success">Diterima</span>
                                        @elseif($row->verification_status == 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">Tertunda</span>
                                        @endif
                                    </td>

                                    <td>{{ $row->created_at->format('d-m-Y H:i') }}</td>

                                    <td class="text-end pe-3">
                                        {{-- Aksi hanya muncul jika status masih tertunda --}}
                                        @if ($row->verification_status == 'pending')
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="pasien"
                                                data-action="approve">
                                                Diterima
                                            </button>

                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal" data-id="{{ $row->id }}" data-type="pasien"
                                                data-action="reject">
                                                Ditolak
                                            </button>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tidak ada data Peserta ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $data->links() }}
        </div>

    </div>

    @include('pengguna.verifikasi._modal_verify')
@endsection