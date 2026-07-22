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
                        id="eksekutif-tab" data-bs-toggle="tab" data-bs-target="#eksekutif" type="button" role="tab" aria-controls="eksekutif" aria-selected="true">
                        <i class="bi bi-grid-3x3-gap-fill text-indigo-600 fs-5" style="color: #4f46e5;"></i> Matriks Eksekutif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 border-0 rounded-0 fw-semibold text-gray-700 d-flex align-items-center gap-2" 
                        id="puskesmas-tab" data-bs-toggle="tab" data-bs-target="#puskesmas" type="button" role="tab" aria-controls="puskesmas" aria-selected="false">
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

                {{-- TAB 0: MATRIKS EKSEKUTIF --}}
                <div class="tab-pane fade show active" id="eksekutif" role="tabpanel" aria-labelledby="eksekutif-tab">
                    
                    {{-- Form Filter Bulan --}}
                    <div class="card bg-white shadow-sm border-0 mb-4 rounded-xl">
                        <div class="card-body p-3">
                            <form action="{{ route('pengguna.rekap.index') }}" method="GET" class="d-flex align-items-center gap-3">
                                <label for="bulan" class="fw-bold text-gray-700 mb-0 ms-2"><i class="bi bi-calendar-month me-1"></i> Periode Laporan:</label>
                                <input type="month" name="bulan" id="bulan" class="form-control form-control-sm w-auto border-gray-300" value="{{ $filterBulan }}">
                                <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm" style="background-color: #4f46e5; border: none;">Filter Laporan</button>
                                <a href="{{ route('pengguna.rekap.index') }}" class="btn btn-sm btn-light fw-semibold text-gray-600 border">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-gray-800 mb-0">Laporan Eksekutif Kategori (Bulan: {{ \Carbon\Carbon::parse($filterBulan)->translatedFormat('F Y') }})</h5>
                            <p class="text-muted small mb-0">Matriks data riil distribusi PTM berdasarkan Wilayah, Kelompok Usia, dan Jenis Penyakit.</p>
                        </div>
                        <button onclick="window.print()" class="btn btn-danger fw-semibold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Matriks
                        </button>
                    </div>

                    <div class="table-responsive bg-white rounded-xl shadow-sm border p-1" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover table-bordered text-center align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="sticky-top" style="background-color: #f8fafc; z-index: 2;">
                                <tr>
                                    <th rowspan="2" class="align-middle" style="background-color: #4f46e5; color: white; border-color: #4338ca; width: 5%;">No</th>
                                    <th rowspan="2" class="align-middle text-start" style="background-color: #4f46e5; color: white; border-color: #4338ca; min-width: 150px;">Wilayah Puskesmas</th>
                                    <th colspan="4" style="background-color: #0ea5e9; color: white; border-color: #0284c7;">Berdasarkan Kelompok Usia</th>
                                    <th colspan="{{ count($penyakitList) }}" style="background-color: #f59e0b; color: white; border-color: #d97706;">Berdasarkan Jenis Penyakit Terdeteksi</th>
                                    <th rowspan="2" class="align-middle" style="background-color: #10b981; color: white; border-color: #059669;">Total Pasien</th>
                                </tr>
                                <tr>
                                    <th style="background-color: #e0f2fe; color: #0369a1; min-width: 80px;">Remaja</th>
                                    <th style="background-color: #e0f2fe; color: #0369a1; min-width: 80px;">Dewasa</th>
                                    <th style="background-color: #e0f2fe; color: #0369a1; min-width: 80px;">Pra Lansia</th>
                                    <th style="background-color: #e0f2fe; color: #0369a1; min-width: 80px;">Lansia</th>
                                    
                                    @foreach($penyakitList as $p)
                                        <th style="background-color: #fef3c7; color: #b45309; min-width: 100px;">{{ $p }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matriksLaporan as $row)
                                    <tr>
                                        <td class="fw-semibold text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="text-start fw-bold text-gray-800">{{ $row['puskesmas'] }}</td>
                                        <td>{{ $row['remaja'] }}</td>
                                        <td>{{ $row['dewasa'] }}</td>
                                        <td>{{ $row['pra_lansia'] }}</td>
                                        <td class="fw-semibold text-danger">{{ $row['lansia'] }}</td>
                                        
                                        @foreach($penyakitList as $p)
                                            <td class="text-gray-600 {{ $row['ptm'][$p] > 0 ? 'fw-bold text-rose-600 bg-rose-50' : '' }}">{{ $row['ptm'][$p] }}</td>
                                        @endforeach
                                        
                                        <td class="fw-bold fs-6 text-green-700 bg-light">{{ $row['total_pasien'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 6 + count($penyakitList) + 1 }}" class="text-muted py-5 text-center">
                                            <i class="bi bi-inbox fs-1 text-gray-300 d-block mb-2"></i>
                                            Belum ada data skrining PTM yang masuk pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($matriksLaporan) > 0)
                            <tfoot class="fw-bold sticky-bottom" style="background-color: #f1f5f9;">
                                <tr>
                                    <td colspan="2" class="text-end py-3">TOTAL KESELURUHAN WILAYAH :</td>
                                    <td class="text-primary">{{ $matriksLaporan->sum('remaja') }}</td>
                                    <td class="text-primary">{{ $matriksLaporan->sum('dewasa') }}</td>
                                    <td class="text-primary">{{ $matriksLaporan->sum('pra_lansia') }}</td>
                                    <td class="text-danger">{{ $matriksLaporan->sum('lansia') }}</td>
                                    
                                    @foreach($penyakitList as $p)
                                        @php
                                            $totalPenyakit = $matriksLaporan->sum(function($row) use ($p) {
                                                return $row['ptm'][$p] ?? 0;
                                            });
                                        @endphp
                                        <td class="{{ $totalPenyakit > 0 ? 'text-rose-600 fs-6' : 'text-gray-500' }}">{{ $totalPenyakit }}</td>
                                    @endforeach

                                    <td class="fs-5 text-green-700 bg-green-50">{{ $matriksLaporan->sum('total_pasien') }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- TAB 1: REKAP PUSKESMAS --}}
                <div class="tab-pane fade" id="puskesmas" role="tabpanel" aria-labelledby="puskesmas-tab">
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
