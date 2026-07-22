@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        @include('kepala_p2ptm.laporan.partials.tabs')

        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#eef2ff,#f8fafc)">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <h4 class="fw-bold mb-0">Laporan Faktor Risiko PTM</h4>
                        <small class="text-muted">Tinjau data perilaku berisiko pasien (Merokok, Kurang Aktivitas, Diet,
                            dll).</small>
                    </div>

                    <div class="col-md-7">
                        <form action="{{ route('kepala.laporan.faktor_risiko') }}" method="GET"
                            class="d-flex flex-wrap gap-2 justify-content-md-end align-items-end">
                            
                            {{-- Jenis Filter --}}
                            <div class="d-flex align-items-center gap-3 bg-white px-3 py-1 border rounded-pill shadow-sm mb-1">
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="filter_type" id="type_bulan" value="bulanan" 
                                        {{ request('filter_type', 'bulanan') === 'bulanan' ? 'checked' : '' }} onchange="toggleFilterType()">
                                    <label class="form-check-label text-muted small mb-0 fw-semibold" for="type_bulan">Bulanan</label>
                                </div>
                                <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="filter_type" id="type_tanggal" value="tanggal" 
                                        {{ request('filter_type') === 'tanggal' ? 'checked' : '' }} onchange="toggleFilterType()">
                                    <label class="form-check-label text-muted small mb-0 fw-semibold" for="type_tanggal">Rentang Tanggal</label>
                                </div>
                            </div>

                            {{-- Input Bulanan --}}
                            <div id="filter_bulanan_inputs" class="d-flex gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">Bulan</small>
                                    <select name="bulan" class="form-select form-select-sm rounded-pill shadow-sm" style="min-width: 120px;">
                                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $nama)
                                            <option value="{{ $key + 1 }}" {{ request('bulan', date('m')) == ($key + 1) ? 'selected' : '' }}>{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Tahun</small>
                                    <input type="number" name="tahun"
                                        class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tahun', date('Y')) }}" style="width: 90px;">
                                </div>
                            </div>

                            {{-- Input Rentang Tanggal --}}
                            <div id="filter_tanggal_inputs" class="d-flex gap-2">
                                <div>
                                    <small class="text-muted d-block mb-1">Tanggal Awal</small>
                                    <input type="date" name="tgl_awal" class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tgl_awal') }}">
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1">Tanggal Akhir</small>
                                    <input type="date" name="tgl_akhir" class="form-control form-control-sm rounded-pill shadow-sm"
                                        value="{{ request('tgl_akhir') }}">
                                </div>
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
                    <a href="{{ route('kepala.laporan.faktor_risiko.cetak', request()->all()) }}"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4" target="_blank">
                        <i class="bi bi-printer"></i> Cetak & Sahkan Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Puskesmas</th>
                            <th>Merokok</th>
                            <th>Alkohol</th>
                            <th>Kurang Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                                <td class="fw-semibold">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                <td class="text-muted small">
                                    {{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-hospital"></i> {{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $row->merokok ?? '-' }}</td>
                                <td>{{ $row->alkohol ?? '-' }}</td>
                                <td>{{ $row->kurang_aktivitas_fisik ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-4 d-block mb-2"></i>
                                    Tidak ada data faktor risiko untuk periode tersebut.
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

@push('scripts')
<script>
    function toggleFilterType() {
        const type = document.querySelector('input[name="filter_type"]:checked').value;
        const bulanSection = document.getElementById('filter_bulanan_inputs');
        const tanggalSection = document.getElementById('filter_tanggal_inputs');
        
        if (type === 'bulanan') {
            bulanSection.style.display = 'flex';
            tanggalSection.style.display = 'none';
        } else {
            bulanSection.style.display = 'none';
            tanggalSection.style.display = 'flex';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterType();
    });
</script>
@endpush