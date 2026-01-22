@extends('layouts.master')

@section('title', 'Edit Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Edit Tindak Lanjut PTM</h4>
                <small class="opacity-75">
                    Perbarui data tindak lanjut berdasarkan hasil skrining PTM
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('petugas.tindak_lanjut.update', $tindakLanjut->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        {{-- PESERTA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nama Peserta
                            </label>
                            <input type="text" class="form-control rounded-3"
                                value="{{ $tindakLanjut->pasien->nama_lengkap }}" readonly>
                        </div>

                        {{-- TANGGAL PEMERIKSAAN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Pemeriksaan
                            </label>
                            <input type="text" class="form-control rounded-3"
                                value="{{ \Carbon\Carbon::parse($tindakLanjut->deteksiDini->tanggal_pemeriksaan)->format('d-m-Y') }}"
                                readonly>
                        </div>

                        {{-- JENIS TINDAK LANJUT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jenis Tindak Lanjut <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_tindak_lanjut" class="form-select rounded-3" required>
                                <option value="">-- Pilih --</option>
                                <option value="edukasi" {{ $tindakLanjut->jenis_tindak_lanjut == 'edukasi' ? 'selected' : '' }}>Edukasi</option>
                                <option value="anjuran_gaya_hidup" {{ $tindakLanjut->jenis_tindak_lanjut == 'anjuran_gaya_hidup' ? 'selected' : '' }}>Anjuran
                                    Gaya Hidup</option>
                                <option value="rujukan" {{ $tindakLanjut->jenis_tindak_lanjut == 'rujukan' ? 'selected' : '' }}>Rujukan</option>
                                <option value="monitoring" {{ $tindakLanjut->jenis_tindak_lanjut == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                                <option value="tidak_ada" {{ $tindakLanjut->jenis_tindak_lanjut == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>

                        {{-- TANGGAL TINDAK LANJUT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Tindak Lanjut
                            </label>
                            <input type="date" name="tanggal_tindak_lanjut" class="form-control rounded-3"
                                value="{{ old('tanggal_tindak_lanjut', $tindakLanjut->tanggal_tindak_lanjut) }}">
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Status Tindak Lanjut
                            </label>
                            <select name="status_tindak_lanjut" class="form-select rounded-3" required>
                                <option value="belum" {{ $tindakLanjut->status_tindak_lanjut == 'belum' ? 'selected' : '' }}>
                                    Belum Dilakukan
                                </option>
                                <option value="sudah" {{ $tindakLanjut->status_tindak_lanjut == 'sudah' ? 'selected' : '' }}>
                                    Sudah Dilakukan
                                </option>
                            </select>
                        </div>

                        {{-- CATATAN --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Catatan Petugas
                            </label>
                            <textarea name="catatan_petugas" class="form-control rounded-3" rows="4"
                                placeholder="Catatan edukasi, anjuran, atau keterangan tambahan...">{{ old('catatan_petugas', $tindakLanjut->catatan_petugas) }}</textarea>
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.tindak_lanjut.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
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

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection