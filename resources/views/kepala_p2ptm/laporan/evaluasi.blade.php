@extends('layouts.master')
@section('title', 'Laporan Evaluasi Sistem')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="text-2xl font-semibold text-gray-800">Laporan Survei Kepuasan Petugas Puskesmas</h2>
            <p class="text-gray-500 text-sm mt-1">Laporan dari hasil kuesioner System Usability Scale (SUS) tentang kemudahan pelayanan sistem oleh Petugas Puskesmas.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('kepala.laporan.evaluasi.cetak', request()->all()) }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
    </div>

    {{-- FILTER SIMPEL LAPORAN EVALUASI --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('kepala.laporan.evaluasi') }}" method="GET">
                <div class="row g-3 align-items-end">
                    
                    {{-- FILTER PUSKESMAS --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Filter Puskesmas</label>
                        <select class="form-select border-success-subtle" name="puskesmas_id">
                            <option value="">-- Semua Puskesmas --</option>
                            @foreach($semuaPuskesmasMaster as $pusk)
                                <option value="{{ $pusk->id }}" {{ request('puskesmas_id') == $pusk->id ? 'selected' : '' }}>
                                    {{ $pusk->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- JENIS FILTER WAKTU --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Filter Waktu</label>
                        <select class="form-select border-success-subtle" name="filter_waktu" id="evaluasi_filter_waktu" onchange="toggleFilterWaktuEvaluasi(this.value)">
                            <option value="bulan" {{ request('filter_waktu', 'bulan') == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="tanggal" {{ request('filter_waktu') == 'tanggal' ? 'selected' : '' }}>Rentang Tanggal</option>
                            <option value="semua" {{ request('filter_waktu') == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                        </select>
                    </div>

                    {{-- INPUT BULAN --}}
                    <div class="col-md-3 input-waktu-bulan" style="display: {{ request('filter_waktu', 'bulan') == 'bulan' ? 'block' : 'none' }};">
                        <label class="form-label small fw-bold text-secondary mb-1">Pilih Bulan</label>
                        <select class="form-select border-success-subtle" name="bulan">
                            @php
                                $namaBulanList = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $selectedBulan = request('bulan') ? (int) request('bulan') : (int) date('m');
                            @endphp
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $selectedBulan == $i ? 'selected' : '' }}>
                                    {{ $namaBulanList[$i] }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- INPUT RENTANG TANGGAL --}}
                    <div class="col-md-4 input-waktu-tanggal" style="display: {{ request('filter_waktu') == 'tanggal' ? 'block' : 'none' }};">
                        <label class="form-label small fw-bold text-secondary mb-1">Rentang Tanggal</label>
                        <div class="input-group">
                            <input type="date" class="form-control border-success-subtle" name="tgl_awal" value="{{ request('tgl_awal') }}">
                            <span class="input-group-text bg-success-subtle">s/d</span>
                            <input type="date" class="form-control border-success-subtle" name="tgl_akhir" value="{{ request('tgl_akhir') }}">
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="col-md-2 ms-auto text-end">
                        <div class="d-flex gap-2">
                            <a href="{{ route('kepala.laporan.evaluasi') }}" class="btn btn-outline-secondary w-50 shadow-sm" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                            <button type="submit" class="btn btn-success w-50 shadow-sm" title="Terapkan Filter">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFilterWaktuEvaluasi(val) {
            const boxBulan = document.querySelector('.input-waktu-bulan');
            const boxTanggal = document.querySelector('.input-waktu-tanggal');
            if (val === 'bulan') {
                boxBulan.style.display = 'block';
                boxTanggal.style.display = 'none';
            } else if (val === 'tanggal') {
                boxBulan.style.display = 'none';
                boxTanggal.style.display = 'block';
            } else {
                boxBulan.style.display = 'none';
                boxTanggal.style.display = 'none';
            }
        }
    </script>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-4">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold">Total Responden</h6>
                        <h3 class="mb-0 fw-black text-dark">{{ $totalResponden }} <span class="fs-6 text-muted fw-normal">Petugas Puskesmas</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle {{ $rataRataSkor >= 70 ? 'bg-success' : 'bg-warning' }} bg-opacity-10 p-3 me-4">
                        <i class="bi bi-star text-{{ $rataRataSkor >= 70 ? 'success' : 'warning' }} fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold">Rata-rata Skor SUS</h6>
                        <h3 class="mb-0 fw-black text-dark">{{ $rataRataSkor }} <span class="fs-6 text-{{ $rataRataSkor >= 70 ? 'success' : 'warning' }} fw-bold">({{ $predikat }})</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">Detail Respons Petugas Puskesmas</h5>
        </div>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Petugas</th>
                    <th>Puskesmas</th>
                    <th class="text-center">Skor SUS</th>
                    <th>Waktu Pengisian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semuaData ?? [] as $index => $sus)
                    <tr>
                        <td class="ps-4">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $sus->user->Nama_Lengkap ?? $sus->user->username ?? 'Anonim' }}</td>
                        <td>
                            @if($sus->user->role_name === 'petugas')
                                {{ optional(optional($sus->user->petugas)->puskesmas)->nama_puskesmas ?? 'Puskesmas (Data Belum Lengkap)' }}
                            @else
                                {{ optional($sus->user->pegawaiDinkes)->bidang ?? 'Dinas Kesehatan' }}
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $sus->skor_sus >= 70 ? 'bg-success' : 'bg-warning' }} rounded-pill px-3 py-2">
                                {{ $sus->skor_sus }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $sus->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data evaluasi sistem.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
