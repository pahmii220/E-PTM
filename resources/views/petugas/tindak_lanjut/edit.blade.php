@extends('layouts.master')

@section('title', 'Edit Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-3 px-4" style="max-width: 1200px; margin: 0 auto;">

        {{-- Judul --}}
        <div class="mb-3">
            <h2 class="fs-3 fw-bold text-gray-800 mb-0">Edit Tindak Lanjut PTM</h2>
            <p class="text-muted mb-0">
                Perbarui data tindak lanjut berdasarkan hasil skrining PTM
            </p>
        </div>

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

                <form action="{{ route('petugas.tindak_lanjut.update', $tindakLanjut->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Informasi Pasien --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Peserta</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $tindakLanjut->pasien->nama_lengkap }}" required>
                    </div>

                    {{-- Tanggal Pemeriksaan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($tindakLanjut->deteksiDini->tanggal_pemeriksaan)->format('d-m-Y') }}"
                            disabled>
                    </div>

                    {{-- Jenis Tindak Lanjut --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tindak Lanjut</label>
                        <select name="jenis_tindak_lanjut" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="edukasi" {{ $tindakLanjut->jenis_tindak_lanjut == 'edukasi' ? 'selected' : '' }}>
                                Edukasi
                            </option>
                            <option value="anjuran_gaya_hidup" {{ $tindakLanjut->jenis_tindak_lanjut == 'anjuran_gaya_hidup' ? 'selected' : '' }}>
                                Anjuran Gaya Hidup
                            </option>
                            <option value="rujukan" {{ $tindakLanjut->jenis_tindak_lanjut == 'rujukan' ? 'selected' : '' }}>
                                Rujukan
                            </option>
                            <option value="monitoring" {{ $tindakLanjut->jenis_tindak_lanjut == 'monitoring' ? 'selected' : '' }}>
                                Monitoring
                            </option>
                            <option value="tidak_ada" {{ $tindakLanjut->jenis_tindak_lanjut == 'tidak_ada' ? 'selected' : '' }}>
                                Tidak Ada
                            </option>
                        </select>
                    </div>

                    {{-- Tanggal Tindak Lanjut --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Tindak Lanjut</label>
                        <input type="date" name="tanggal_tindak_lanjut" class="form-control"
                            value="{{ $tindakLanjut->tanggal_tindak_lanjut }}">
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Tindak Lanjut</label>
                        <select name="status_tindak_lanjut" class="form-select" required>
                            <option value="belum" {{ $tindakLanjut->status_tindak_lanjut == 'belum' ? 'selected' : '' }}>
                                Belum Dilakukan
                            </option>
                            <option value="sudah" {{ $tindakLanjut->status_tindak_lanjut == 'sudah' ? 'selected' : '' }}>
                                Sudah Dilakukan
                            </option>
                        </select>
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Petugas</label>
                        <textarea name="catatan_petugas" class="form-control" rows="4"
                            placeholder="Catatan edukasi, anjuran, atau keterangan tambahan...">{{ $tindakLanjut->catatan_petugas }}</textarea>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.tindak_lanjut.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Perubahan
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