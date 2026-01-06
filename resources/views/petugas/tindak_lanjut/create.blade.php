@extends('layouts.master')

@section('title', 'Tambah Tindak Lanjut PTM')

@section('content')
        <div class="container-fluid py-3 px-4" style="max-width: 1200px; margin: 0 auto;">

            {{-- Judul --}}
            <div class="mb-3">
                <h2 class="fs-3 fw-bold text-gray-800 mb-0">Tambah Tindak Lanjut PTM</h2>
                <p class="text-muted mb-0">
                    Input tindak lanjut berdasarkan hasil skrining PTM
                </p>
            </div>
            <br>

            {{-- Error --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card Form --}}
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4">

                    <form action="{{ route('petugas.tindak_lanjut.store') }}" method="POST">
                        @csrf

                        {{-- Informasi Pasien --}}
    <div class="mb-3">
        <label class="form-label fw-semibold">Peserta / Pasien (berdasarkan Deteksi Dini)</label>
        <select name="deteksi_dini_id" class="form-select" required>
            @foreach ($daftarDeteksi as $d)
                <option value="{{ $d->id }}" {{ $d->id == $deteksiTerpilih->id ? 'selected' : '' }}>
                    {{ $d->pasien->nama_lengkap }}
                    | {{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }}
                    | {{ ucfirst(str_replace('_', ' ', $d->status_risiko)) }}
                </option>
            @endforeach
        </select>
    </div>


                        {{-- Jenis Tindak Lanjut --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Tindak Lanjut</label>
                            <select name="jenis_tindak_lanjut" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="edukasi">Edukasi</option>
                                <option value="anjuran_gaya_hidup">Anjuran Gaya Hidup</option>
                                <option value="rujukan">Rujukan</option>
                                <option value="monitoring">Monitoring</option>
                                <option value="tidak_ada">Tidak Ada</option>
                            </select>
                        </div>

                        {{-- Tanggal Tindak Lanjut --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Tindak Lanjut</label>
                            <input type="date" name="tanggal_tindak_lanjut" class="form-control">
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Petugas</label>
                            <textarea name="catatan_petugas" class="form-control" rows="4"
                                placeholder="Catatan edukasi, anjuran, atau keterangan tambahan..."></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('petugas.deteksi_dini.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <style>
            body {
                background-color: #f8fafc;
            }

            .container-fluid {
                margin-top: -10px;
            }
        </style>
@endsection