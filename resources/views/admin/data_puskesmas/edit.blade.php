@extends('layouts.master')

@section('title', 'Edit Data Puskesmas')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#ecfdf5,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold mb-0">Edit Data Puskesmas</h4>
                    <small class="text-muted">Perbarui informasi puskesmas dan wilayah</small>
                </div>

            </div>
        </div>

        {{-- ================= ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>Periksa kembali input Anda:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================= FORM CARD ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">

                <form action="{{ route('admin.data_puskesmas.update', $puskesmas->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- INFORMASI UTAMA --}}
                    <h6 class="fw-semibold mb-3 text-success">Informasi Utama</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kode Puskesmas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kode_puskesmas" class="form-control rounded-3"
                                value="{{ old('kode_puskesmas', $puskesmas->kode_puskesmas) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Nama Puskesmas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_puskesmas" class="form-control rounded-3"
                                value="{{ old('nama_puskesmas', $puskesmas->nama_puskesmas) }}" required>
                        </div>

                    </div>

                    {{-- WILAYAH --}}
                    <h6 class="fw-semibold mb-3 text-success">Wilayah</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kabupaten <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_kabupaten" class="form-control rounded-3"
                                value="{{ old('nama_kabupaten', $puskesmas->nama_kabupaten) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kecamatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kecamatan" class="form-control rounded-3"
                                value="{{ old('kecamatan', $puskesmas->kecamatan) }}" required>
                        </div>

                    </div>

                    {{-- DETAIL --}}
                    <h6 class="fw-semibold mb-3 text-success">Detail Tambahan</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-12">
                            <label class="form-label fw-medium">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="form-control rounded-3">{{ old('alamat', $puskesmas->alamat) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control rounded-3"
                                value="{{ old('kode_pos', $puskesmas->kode_pos) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control rounded-3"
                                value="{{ old('email', $puskesmas->email) }}">
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.data_puskesmas.index') }}"
                            class="btn btn-light rounded-pill px-4 shadow-sm">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
@endsection