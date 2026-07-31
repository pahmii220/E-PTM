@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1400px; background-color: #f8fafc;">
        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="text-2xl font-semibold text-gray-800">Laporan Data Pegawai P2PTM</h2>
                <p class="text-gray-500 text-sm mt-1">Data rekam pegawai Dinas Kesehatan yang bertugas di wilayah kerja P2PTM.</p>
            </div>
        </div>

        {{-- KONTEN TABEL DATA PEGAWAI --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 min-h-[400px]">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Laporan Data Pegawai Dinkes P2PTM</h4>
                    <small class="text-muted">Data pegawai Dinas Kesehatan yang bertugas di tingkat Kabupaten/Kota.</small>
                </div>
                <a href="{{ route('kepala.laporan.eksekutif.cetak_pegawai', request()->all()) }}" target="_blank"
                    class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </a>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th class="text-start">NIP</th>
                            <th class="text-start">Nama Pegawai</th>
                            <th class="text-start">Jabatan</th>
                            <th class="text-start">Bidang</th>
                            <th>Wilayah Tugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataPegawai ?? [] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start fw-bold text-dark">{{ $row->nip ?? '-' }}</td>
                                <td class="text-start text-dark">{{ $row->nama_pegawai ?? '-' }}</td>
                                <td class="text-start fw-semibold">{{ $row->jabatan ?? '-' }}</td>
                                <td class="text-start text-muted">{{ $row->bidang ?? '-' }}</td>
                                <td>{{ $row->kabupaten_kota ?? 'Banjarmasin' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data pegawai dinkes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
