@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-0">Laporan Tindak Lanjut PTM</h4>
                        <small class="text-muted">Tinjau data tindakan atau rujukan yang diberikan petugas kepada
                            peserta.</small>
                    </div>

                    <div class="col-md-7">
                        <form action="{{ route('kepala.laporan.tindak_lanjut') }}" method="GET"
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

                <hr class="my-3 opacity-25">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('kepala.laporan.tindak_lanjut.cetak', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}"
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
                            <th class="ps-4" style="width:60px">No</th>
                            <th>Nama Peserta</th>
                            <th>Tanggal Tindak Lanjut</th>
                            <th>Jenis Tindak Lanjut</th>
                            <th>Keterangan/Catatan</th>
                            <th>Puskesmas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                <td class="text-muted small">
                                    {{ $row->tanggal_tindak_lanjut ? \Carbon\Carbon::parse($row->tanggal_tindak_lanjut)->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill">
                                        {{ $row->jenis_tindak_lanjut ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $row->catatan_petugas ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hospital"></i> {{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data tindak lanjut untuk periode tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $data->links() }}
        </div>
    </div>
@endsection