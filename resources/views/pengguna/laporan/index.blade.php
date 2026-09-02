@extends('layouts.master') {{-- Sesuaikan dengan template kamu --}}

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 bg-primary d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">Riwayat Pengajuan Laporan PTM</h6>
                <a href="{{ route('pengguna.pengajuan.create') }}" class="btn btn-sm btn-light text-primary font-weight-bold">
                    <i class="fas fa-plus"></i> Ajukan Laporan Baru
                </a>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jenis Laporan</th>
                                <th>Periode</th>
                                <th>Status Pengesahan</th>
                                <th>Tanggal Disahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPengajuan as $index => $dokumen)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start"><strong>{{ $dokumen->jenis_laporan }}</strong></td>
                                    <td>{{ $dokumen->bulan }} {{ $dokumen->tahun }}</td>
                                    <td>
                                        @if($dokumen->status == 'menunggu')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu Kepala P2PTM</span>
                                        @else
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Disahkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dokumen->status == 'disahkan')
                                            {{ \Carbon\Carbon::parse($dokumen->tanggal_pengesahan)->format('d/m/Y H:i') }}
                                            <br>
                                            {{-- Tombol Cetak PDF Baru --}}
                                            <a href="{{ route('pengguna.pengajuan.cetak', $dokumen->id) }}"
                                                class="inline-block mt-1 px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                                <i class="bi bi-printer"></i> Cetak Lembar Pengesahan
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted py-4">Anda belum pernah mengajukan laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection