@extends('layouts.master') {{-- Sesuaikan dengan nama layout master kamu --}}

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Laporan Data Peserta PTM</h1>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- KOTAK FILTER TANGGAL --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter"></i> Filter Berdasarkan Tanggal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('kepala.laporan.peserta') }}" method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal"
                            value="{{ request('tanggal_awal') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                            value="{{ request('tanggal_akhir') }}" required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                        <a href="{{ route('kepala.laporan.peserta') }}" class="btn btn-secondary">Reset</a>

                        {{-- Tombol Cetak (Hanya aktif jika sedang melakukan filter tanggal) --}}
                        @if(request('tanggal_awal') && request('tanggal_akhir'))
                            <a href="{{ route('kepala.laporan.peserta.cetak', ['tanggal_awal' => request('tanggal_awal'), 'tanggal_akhir' => request('tanggal_akhir')]) }}"
                                target="_blank" class="btn btn-warning float-end ms-2">
                                <i class="fas fa-print"></i> Cetak Laporan
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- KOTAK TABEL DATA PESERTA --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Peserta Terdaftar</h6>

                {{-- Tombol Sahkan (Muncul jika ada filter tanggal) --}}
                @if(request('tanggal_awal') && request('tanggal_akhir'))
                    <form action="{{ route('kepala.laporan.peserta.sahkan') }}" method="POST"
                        onsubmit="return confirm('Sahkan data peserta untuk periode ini dengan QR Code?')">
                        @csrf
                        <input type="hidden" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                        <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-qrcode"></i> Sahkan Periode Ini
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Daftar</th>
                                <th>No. Rekam Medis</th>
                                <th>Nama Pasien</th>
                                <th>Tanggal Lahir</th>
                                <th>Kontak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataPeserta as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($row->dibuat_pada)->format('d-m-Y') }}</td>
                                    <td>{{ $row->no_rekam_medis ?? '-' }}</td>
                                    <td><strong>{{ $row->nama_lengkap ?? '-' }}</strong></td>
                                    <td class="text-center">
                                        {{ $row->tanggal_lahir ? \Carbon\Carbon::parse($row->tanggal_lahir)->format('d-m-Y') : '-' }}
                                    </td>
                                    <td>{{ $row->kontak ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Silakan gunakan filter tanggal untuk menampilkan data, atau belum ada data di rentang
                                        waktu tersebut.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection