@extends('layouts.master')

@section('title', 'Rekap Laporan Terpadu PTM')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px; margin: auto;">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-gray-800 mb-1">
                <i class="bi bi-bar-chart-fill text-green-600 me-2"></i> Rekap Laporan Terpadu
            </h3>
            <p class="text-muted small mb-0">Lihat dan cetak seluruh rekapitulasi data Penyakit Tidak Menular (PTM) dari satu tempat.</p>
        </div>
    </div>

    {{-- Card Utama Kontainer Tab --}}
    <div class="card shadow-sm border-0 rounded-xl overflow-hidden">
        {{-- Header Tab Navigasi --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 flex-nowrap overflow-x-auto" id="rekapTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-4 border-0 rounded-0 fw-semibold text-gray-700 d-flex align-items-center gap-2" 
                        id="puskesmas-tab" data-bs-toggle="tab" data-bs-target="#puskesmas" type="button" role="tab" aria-controls="puskesmas" aria-selected="true">
                        <i class="bi bi-building text-green-600 fs-5"></i> Rekap Puskesmas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-gray-700 d-flex align-items-center gap-2" 
                        id="skrining-tab" data-bs-toggle="tab" data-bs-target="#skrining" type="button" role="tab" aria-controls="skrining" aria-selected="false">
                        <i class="bi bi-heart-pulse text-green-600 fs-5"></i> Rekap Skrining PTM
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-gray-700 d-flex align-items-center gap-2" 
                        id="usia-tab" data-bs-toggle="tab" data-bs-target="#usia" type="button" role="tab" aria-controls="usia" aria-selected="false">
                        <i class="bi bi-person-lines-fill text-green-600 fs-5"></i> PTM Kelompok Usia
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-gray-700 d-flex align-items-center gap-2" 
                        id="kegiatan-tab" data-bs-toggle="tab" data-bs-target="#kegiatan" type="button" role="tab" aria-controls="kegiatan" aria-selected="false">
                        <i class="bi bi-calendar-event text-green-600 fs-5"></i> Laporan Kegiatan PTM
                    </button>
                </li>
            </ul>
        </div>

        {{-- Konten Tab --}}
        <div class="card-body p-4 bg-light bg-opacity-25">
            <div class="tab-content" id="rekapTabContent">

                {{-- TAB 1: REKAP PUSKESMAS --}}
                <div class="tab-pane fade show active" id="puskesmas" role="tabpanel" aria-labelledby="puskesmas-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold text-gray-800 mb-0">Rekapitulasi Data PTM Per Puskesmas</h5>
                        <a href="{{ route('pengguna.rekap.puskesmas.print') }}" target="_blank" class="btn btn-danger fw-semibold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Rekap Puskesmas
                        </a>
                    </div>
                    
                    <div class="table-responsive bg-white rounded shadow-sm border">
                        <table class="table table-bordered table-striped text-center align-middle mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th width="5%">No</th>
                                    <th class="text-start">Nama Puskesmas</th>
                                    <th>Total Peserta</th>
                                    <th>Deteksi Dini</th>
                                    <th>Faktor Risiko</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapPuskesmas as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start fw-semibold">{{ $item->nama_puskesmas }}</td>
                                        <td>{{ $item->total_peserta }}</td>
                                        <td>{{ $item->total_deteksi }}</td>
                                        <td>{{ $item->total_faktor }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted py-4">Tidak ada data rekap puskesmas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: REKAP SKRINING PTM --}}
                <div class="tab-pane fade" id="skrining" role="tabpanel" aria-labelledby="skrining-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold text-gray-800 mb-0">Rekapitulasi Hasil Skrining Penyakit Tidak Menular</h5>
                        <a href="{{ route('pengguna.laporan.status_ptm') }}" target="_blank" class="btn btn-danger fw-semibold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Rekap Skrining
                        </a>
                    </div>

                    <div class="table-responsive bg-white rounded shadow-sm border">
                        <table class="table table-bordered table-striped text-center align-middle mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th width="80px">No</th>
                                    <th>Status Kesehatan (Hasil Skrining)</th>
                                    <th>Jumlah Peserta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($skriningPtm as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row->hasil_skrining }}</td>
                                        <td>{{ $row->jumlah }} orang</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted py-4">Tidak ada data hasil skrining.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end fw-bold text-gray-700">
                        Total Keseluruhan Peserta: {{ $skriningPtm->sum('jumlah') }} orang
                    </div>
                </div>

                {{-- TAB 3: PTM KELOMPOK USIA --}}
                <div class="tab-pane fade" id="usia" role="tabpanel" aria-labelledby="usia-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold text-gray-800 mb-0">Laporan PTM Berdasarkan Kelompok Usia</h5>
                        <a href="{{ route('pengguna.laporan.kelompok_usia.print') }}" target="_blank" class="btn btn-danger fw-semibold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Laporan Kelompok Usia
                        </a>
                    </div>

                    <div class="table-responsive bg-white rounded shadow-sm border">
                        <table class="table table-bordered table-striped text-center align-middle mb-0">
                            <thead class="table-success">
                                <tr>
                                    <th style="width:80px">No</th>
                                    <th>Kelompok Usia</th>
                                    <th>Rentang Usia</th>
                                    <th>Jumlah Peserta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td class="text-start fw-semibold">Remaja</td>
                                    <td>&lt; 18 Tahun</td>
                                    <td class="fw-bold text-green-600">{{ $kelompokUsia['remaja'] }}</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td class="text-start fw-semibold">Dewasa</td>
                                    <td>18 – 44 Tahun</td>
                                    <td class="fw-bold text-green-600">{{ $kelompokUsia['dewasa'] }}</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td class="text-start fw-semibold">Pra Lansia</td>
                                    <td>45 – 59 Tahun</td>
                                    <td class="fw-bold text-green-600">{{ $kelompokUsia['pra_lansia'] }}</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td class="text-start fw-semibold">Lansia</td>
                                    <td>≥ 60 Tahun</td>
                                    <td class="fw-bold text-green-600">{{ $kelompokUsia['lansia'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 text-end fw-bold text-gray-700">
                        Total Keseluruhan Peserta : {{ array_sum($kelompokUsia) }} orang
                    </div>
                </div>

                {{-- TAB 4: LAPORAN KEGIATAN PTM --}}
                <div class="tab-pane fade" id="kegiatan" role="tabpanel" aria-labelledby="kegiatan-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold text-gray-800 mb-0">Laporan Pelaksanaan Kegiatan PTM / Posbindu</h5>
                        <a href="{{ route('pengguna.laporan.kegiatan') }}" target="_blank" class="btn btn-danger fw-semibold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Laporan Kegiatan
                        </a>
                    </div>

                    <div class="table-responsive bg-white rounded shadow-sm border" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-striped text-center align-middle mb-0">
                            <thead class="table-success sticky-top">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Puskesmas</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Jumlah Peserta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kegiatan as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td class="text-start fw-semibold">{{ $item->nama_kegiatan }}</td>
                                        <td>{{ $item->puskesmas->nama_puskesmas ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                        <td>{{ $item->lokasi }}</td>
                                        <td class="fw-bold">{{ $item->jumlah_peserta }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted py-4">Tidak ada data kegiatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Custom style untuk mempercantik tab Bootstrap --}}
<style>
    #rekapTab .nav-link {
        border-bottom: 3px solid transparent !important;
        transition: all 0.2s ease-in-out;
    }
    #rekapTab .nav-link.active {
        border-bottom: 3px solid #198754 !important; /* warna green-600 */
        color: #198754 !important;
        background-color: transparent !important;
    }
    #rekapTab .nav-link:hover {
        background-color: #f8f9fa;
        color: #198754;
    }
</style>
@endsection
