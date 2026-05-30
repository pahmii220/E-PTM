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
                        <h4 class="fw-bold mb-0">Laporan Deteksi Dini PTM</h4>
                        <small class="text-muted">Tinjau hasil pemeriksaan deteksi dini (Tekanan Darah, Gula Darah, dll)
                            dari Puskesmas.</small>
                    </div>

                    <div class="col-md-7">
                        {{-- UBAH ROUTE KE DETEKSI DINI --}}
                        <form action="{{ route('kepala.laporan.deteksi_dini') }}" method="GET"
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
                    {{-- UBAH ROUTE CETAK KE DETEKSI DINI --}}
                    <a href="{{ route('kepala.laporan.deteksi_dini.cetak', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4" target="_blank">
                        <i class="bi bi-printer"></i> Cetak Laporan
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
                            <th>Puskesmas</th>
                            <th>Tanggal Periksa</th>
                            <th>Tekanan Darah</th>
                            <th>Gula Darah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ $row->pasien->nama_lengkap ?? '-' }}</td>

                                {{-- Menampilkan nama puskesmas asal data --}}
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hospital"></i> {{ $row->puskesmas->nama_puskesmas ?? 'Puskesmas' }}
                                    </span>
                                </td>

                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan ?? $row->dibuat_pada)->format('d-m-Y') }}
                                </td>

                                {{-- Menampilkan Hasil Medis --}}
                                <td>{{ $row->tekanan_darah ?? '-' }} mmHg</td>
                                <td>{{ $row->gula_darah ?? '-' }} mg/dL</td>

                                <td>
                                    @php
                                        // Ambil teks dari database dan ubah ke huruf kecil untuk pengecekan
                                        $status = strtolower($row->hasil_skrining ?? '');
                                    @endphp

                                    @if(str_contains($status, 'normal'))
                                        <span class="badge rounded-pill bg-success-subtle text-success">
                                            {{ $row->hasil_skrining }}
                                        </span>
                                    @elseif(str_contains($status, 'dicurigai'))
                                        {{-- Warna Kuning (Warning) --}}
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                            {{ $row->hasil_skrining }}
                                        </span>
                                    @elseif(str_contains($status, 'resiko') || str_contains($status, 'risiko'))
                                        {{-- Warna Merah (Danger) --}}
                                        <span class="badge rounded-pill bg-danger-subtle text-danger">
                                            {{ $row->hasil_skrining }}
                                        </span>
                                    @else
                                        {{-- Jika kosong atau status lain (Abu-abu) --}}
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                            {{ $row->hasil_skrining ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data pemeriksaan deteksi dini untuk periode bulan/tahun tersebut.
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