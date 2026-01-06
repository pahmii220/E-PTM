@extends('layouts.master')

@section('title', 'Rekap PTM Per Puskesmas')

@section('content')
    <div class="container-fluid py-3 px-4" style="max-width:1400px; margin:auto;">

        {{-- Judul & Tombol --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h3 class="fw-bold text-gray-800 mb-0">
                Rekap Laporan PTM Per Puskesmas
            </h3>

            <a href="{{ route('pengguna.rekap.puskesmas.print') }}" target="_blank"
                class="btn btn-danger fw-semibold shadow-sm">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
        <br>

        {{-- Tabel Rekap --}}
        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-success">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Puskesmas</th>
                            <th>Total Pasien</th>
                            <th>Deteksi Dini</th>
                            <th>Faktor Risiko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapPuskesmas as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $item->nama_puskesmas }}</td>
                                <td>{{ $item->total_pasien }}</td>
                                <td>{{ $item->total_deteksi }}</td>
                                <td>{{ $item->total_faktor }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">
                                    Tidak ada data rekap
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        

    </div>
@endsection