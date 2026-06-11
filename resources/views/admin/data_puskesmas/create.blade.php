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

                    {{-- WILAYAH (SUDAH DIUBAH MENJADI DROPDOWN) --}}
                    <h6 class="fw-semibold mb-3 text-success">Wilayah</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Kabupaten / Kota <span class="text-danger">*</span>
                            </label>
                            <select name="nama_kabupaten" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kabupaten / Kota --</option>
                                <option value="Kota Banjarmasin" {{ old('nama_kabupaten') == 'Kota Banjarmasin' ? 'selected' : '' }}>Kota Banjarmasin</option>
                                <option value="Kota Banjarbaru" {{ old('nama_kabupaten') == 'Kota Banjarbaru' ? 'selected' : '' }}>Kota Banjarbaru</option>
                                <option value="Kabupaten Banjar" {{ old('nama_kabupaten') == 'Kabupaten Banjar' ? 'selected' : '' }}>Kabupaten Banjar</option>
                                <option value="Kabupaten Barito Kuala" {{ old('nama_kabupaten') == 'Kabupaten Barito Kuala' ? 'selected' : '' }}>Kabupaten Barito Kuala</option>
                                <option value="Kabupaten Tapin" {{ old('nama_kabupaten') == 'Kabupaten Tapin' ? 'selected' : '' }}>Kabupaten Tapin</option>
                                <option value="Kabupaten Hulu Sungai Selatan" {{ old('nama_kabupaten') == 'Kabupaten Hulu Sungai Selatan' ? 'selected' : '' }}>Kabupaten Hulu Sungai Selatan</option>
                                <option value="Kabupaten Hulu Sungai Tengah" {{ old('nama_kabupaten') == 'Kabupaten Hulu Sungai Tengah' ? 'selected' : '' }}>Kabupaten Hulu Sungai Tengah</option>
                                <option value="Kabupaten Hulu Sungai Utara" {{ old('nama_kabupaten') == 'Kabupaten Hulu Sungai Utara' ? 'selected' : '' }}>Kabupaten Hulu Sungai Utara</option>
                                <option value="Kabupaten Tabalong" {{ old('nama_kabupaten') == 'Kabupaten Tabalong' ? 'selected' : '' }}>Kabupaten Tabalong</option>
                                <option value="Kabupaten Tanah Laut" {{ old('nama_kabupaten') == 'Kabupaten Tanah Laut' ? 'selected' : '' }}>Kabupaten Tanah Laut</option>
                                <option value="Kabupaten Tanah Bumbu" {{ old('nama_kabupaten') == 'Kabupaten Tanah Bumbu' ? 'selected' : '' }}>Kabupaten Tanah Bumbu</option>
                                <option value="Kabupaten Kotabaru" {{ old('nama_kabupaten') == 'Kabupaten Kotabaru' ? 'selected' : '' }}>Kabupaten Kotabaru</option>
                                <option value="Kabupaten Balangan" {{ old('nama_kabupaten') == 'Kabupaten Balangan' ? 'selected' : '' }}>Kabupaten Balangan</option>
                            </select>
                        </div>

                            <div class="col-md-6">
                                <label class="form-label fw-medium">
                                    Kecamatan <span class="text-danger">*</span>
                                </label>
                                <select name="kecamatan" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <option value="Banjarmasin Utara" {{ old('kecamatan') == 'Banjarmasin Utara' ? 'selected' : '' }}>Banjarmasin
                                        Utara</option>
                                    <option value="Banjarmasin Selatan" {{ old('kecamatan') == 'Banjarmasin Selatan' ? 'selected' : '' }}>Banjarmasin
                                        Selatan</option>
                                    <option value="Banjarmasin Tengah" {{ old('kecamatan') == 'Banjarmasin Tengah' ? 'selected' : '' }}>Banjarmasin
                                        Tengah</option>
                                    <option value="Banjarmasin Timur" {{ old('kecamatan') == 'Banjarmasin Timur' ? 'selected' : '' }}>Banjarmasin
                                        Timur</option>
                                    <option value="Banjarmasin Barat" {{ old('kecamatan') == 'Banjarmasin Barat' ? 'selected' : '' }}>Banjarmasin
                                        Barat</option>
                                </select>
                            </div>

                    </div>

                    {{-- DETAIL --}}
                    <h6 class="fw-semibold mb-3 text-success">Detail Tambahan</h6>
                    <div class="row g-4 mb-4">

                        <div class="col-md-12">
                            <label class="form-label fw-medium">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-control rounded-3"
                                placeholder="">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control rounded-3" placeholder="Contoh: 70238"
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

        /* Tambahan styling agar dropdown terlihat lebih rapi */
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
        }
    </style>
@endsection