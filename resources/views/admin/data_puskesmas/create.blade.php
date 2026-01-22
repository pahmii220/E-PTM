@extends('layouts.master')

@section('title', 'Tambah Data Puskesmas')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#ecfdf5,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold mb-0">Tambah Data Puskesmas</h4>
                    <small class="text-muted">Input informasi puskesmas dan wilayah</small>
                </div>

            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">

                {{-- ALERT ERROR --}}
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

                <form action="{{ route('admin.data_puskesmas.store') }}" method="POST">
                    @csrf

                    {{-- INFORMASI UTAMA --}}
                    <h6 class="fw-semibold mb-3 text-success">Informasi Utama</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kode Puskesmas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kode_puskesmas" class="form-control rounded-3"
                                placeholder="Contoh: PKM-001" value="{{ old('kode_puskesmas') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Nama Puskesmas <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_puskesmas" class="form-control rounded-3"
                                placeholder="Nama puskesmas" value="{{ old('nama_puskesmas') }}" required>
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
                                placeholder="Nama kabupaten" value="{{ old('nama_kabupaten') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kecamatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kecamatan" class="form-control rounded-3" placeholder="Nama kecamatan"
                                value="{{ old('kecamatan') }}" required>
                        </div>

                    </div>

                    {{-- DETAIL --}}
                    <h6 class="fw-semibold mb-3 text-success">Detail Tambahan</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-12">
                            <label class="form-label fw-medium">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control rounded-3"
                                placeholder="Alamat lengkap puskesmas">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control rounded-3" placeholder="Kode pos"
                                value="{{ old('kode_pos') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" name="email" class="form-control rounded-3"
                                placeholder="email@puskesmas.go.id" value="{{ old('email') }}">
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.data_puskesmas.index') }}"
                            class="btn btn-light rounded-pill px-4 shadow-sm">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save"></i> Simpan Data
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