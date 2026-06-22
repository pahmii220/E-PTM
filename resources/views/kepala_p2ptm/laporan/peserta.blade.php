@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ALERT PESAN --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ================= HEADER & FILTER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-0">Laporan Peserta (Terverifikasi)</h4>
                        <small class="text-muted">Tinjau data peserta dan lakukan pencetakan laporan bulanan.</small>
                    </div>

                    <div class="col-md-7">
                        <form action="{{ route('kepala.laporan.peserta') }}" method="GET"
                            class="d-flex flex-wrap gap-2 justify-content-md-end align-items-end">
                            <div>
                                <small class="text-muted d-block mb-1">Bulan</small>
                                <select name="bulan" class="form-select form-select-sm rounded-pill shadow-sm">
                                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $nama)
                                        <option value="{{ $key + 1 }}" {{ request('bulan', date('m')) == ($key + 1) ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Tahun</small>
                                <input type="number" name="tahun"
                                    class="form-control form-control-sm rounded-pill shadow-sm"
                                    value="{{ request('tahun', date('Y')) }}" style="width: 100px;">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm px-3">
                                    <i class="bi bi-search"></i> Tampilkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <hr class="my-3 opacity-25">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('kepala.laporan.peserta.cetak', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4" target="_blank">
                        <i class="bi bi-printer"></i> Cetak & Sahkan Laporan
                    </a>
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
                            <th>Nama Peserta</th>
                            <th>No RM</th>
                            <th>Kontak</th>
                            <th>Status</th>
                            <th>Tanggal Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ $row->nama_lengkap }}</td>
                                <td>{{ $row->no_rekam_medis ?? '-' }}</td>
                                <td>{{ $row->kontak ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-success-subtle text-success">
                                        <i class="bi bi-check-circle"></i> Terverifikasi
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($row->dibuat_pada)->format('d-m-Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data untuk periode bulan/tahun tersebut.
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
@endsection