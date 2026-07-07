@extends('layouts.master') {{-- Sesuaikan dengan nama template kamu --}}

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-info text-white">
                <h6 class="m-0 font-weight-bold">Preview Data: {{ $dokumen->jenis_laporan }}</h6>
                <span class="badge bg-light text-dark">Periode: {{ $dokumen->bulan }} {{ $dokumen->tahun }}</span>
            </div>
            <div class="card-body">
                <div class="alert alert-secondary text-center mb-4">
                    <i class="fas fa-info-circle"></i> Ini adalah tampilan pratinjau (read-only). Pastikan data rekapitulasi
                    di bawah ini sudah sesuai sebelum melakukan pengesahan.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">

                        {{-- 1. Tampilan untuk Laporan Data Peserta --}}
                        @if($dokumen->jenis_laporan == 'Laporan Data Peserta PTM')
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Peserta</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataLaporan as $i => $data)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $data->nik }}</td>
                                        <td class="text-start">{{ $data->nama_lengkap }}</td>
                                        <td>{{ $data->tanggal_lahir }}</td>
                                        <td>{{ $data->jenis_kelamin }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Tidak ada data peserta di periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            {{-- 2. Tampilan untuk Laporan Deteksi Dini --}}
                        @elseif($dokumen->jenis_laporan == 'Laporan Deteksi Dini PTM')
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peserta</th>
                                    <th>Tensi (Sistole/Diastole)</th>
                                    <th>Gula Darah</th>
                                    <th>Hasil Skrining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataLaporan as $i => $data)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="text-start">{{ $data->nama_lengkap }}</td>
                                        <td>{{ $data->sistole ?? '-' }} / {{ $data->diastole ?? '-' }}</td>
                                        <td>{{ $data->gula_darah_sewaktu ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $data->hasil_skrining == 'Normal' ? 'success' : 'danger' }}">
                                                {{ $data->hasil_skrining ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Tidak ada data deteksi dini terverifikasi di periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            {{-- 3. Tampilan untuk Laporan Kelompok Usia --}}
                        @elseif($dokumen->jenis_laporan == 'Laporan PTM Berdasarkan Kelompok Usia')
                            <thead class="table-dark">
                                <tr>
                                    <th>Di Bawah 20 Tahun</th>
                                    <th>20 - 44 Tahun</th>
                                    <th>45 - 59 Tahun</th>
                                    <th>Lansia (>= 60 Tahun)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $dataLaporan->di_bawah_20 ?? 0 }} Orang</td>
                                    <td>{{ $dataLaporan->usia_20_44 ?? 0 }} Orang</td>
                                    <td>{{ $dataLaporan->usia_45_59 ?? 0 }} Orang</td>
                                    <td>{{ $dataLaporan->lansia ?? 0 }} Orang</td>
                                </tr>
                            </tbody>

                            {{-- Tampilan Default untuk laporan lainnya --}}
                        @else
                            <div class="alert alert-warning text-start">
                                Tabel pratinjau spesifik untuk <b>{{ $dokumen->jenis_laporan }}</b> dapat kamu tambahkan lebih
                                lanjut di file <code>preview.blade.php</code>.
                                <br>Sistem mendeteksi ada
                                <strong>{{ is_countable($dataLaporan) ? count($dataLaporan) : 1 }}</strong> baris data pada
                                periode ini.
                            </div>
                        @endif

                    </table>
                </div>

                <hr>

                {{-- Bagian Tombol Aksi --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('kepala.laporan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    @if($dokumen->status == 'menunggu')
                        <form action="{{ route('kepala.laporan.sahkan', $dokumen->id) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin data ini sudah benar dan ingin disahkan menggunakan QR Code?')">
                            @csrf
                            <button type="submit" class="btn btn-success px-4 font-weight-bold">
                                <i class="fas fa-qrcode"></i> Sahkan Laporan Ini
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection