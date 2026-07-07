@extends('layouts.master')

@section('title', 'Edit Data Peserta')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Edit Data Peserta</h4>
                <small class="opacity-75">
                    Perbarui data peserta dengan benar sebelum disimpan
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                {{-- VALIDATION ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('petugas.peserta.update', $peserta->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- NIK (BARU) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nik" class="form-control rounded-3 @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $peserta->nik) }}" minlength="16" maxlength="16" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NO RM --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nomor Rekam Medis <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="no_rekam_medis"
                                class="form-control rounded-3 @error('no_rekam_medis') is-invalid @enderror"
                                value="{{ old('no_rekam_medis', $peserta->no_rekam_medis) }}" required>
                            <small class="text-muted" style="font-size: 11px;">
                                Nomor rekam medis menyertakan kode Puskesmas sebagai prefiks unik (contoh: <code>Pk-002/RM-001</code>).
                            </small>
                            @error('no_rekam_medis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_lengkap"
                                class="form-control rounded-3 @error('nama_lengkap') is-invalid @enderror"
                                value="{{ old('nama_lengkap', $peserta->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- JENIS KELAMIN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_kelamin" class="form-select rounded-3 @error('jenis_kelamin') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $peserta->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $peserta->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TEMPAT LAHIR (BARU) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tempat Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tempat_lahir"
                                class="form-control rounded-3 @error('tempat_lahir') is-invalid @enderror"
                                value="{{ old('tempat_lahir', $peserta->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TANGGAL LAHIR --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir"
                                class="form-control rounded-3 @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', optional($peserta->tanggal_lahir)->format('Y-m-d')) }}" required>
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PEKERJAAN (BARU) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Pekerjaan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="pekerjaan" class="form-control rounded-3 @error('pekerjaan') is-invalid @enderror"
                                value="{{ old('pekerjaan', $peserta->pekerjaan) }}" required>
                            @error('pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KONTAK --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nomor Kontak / HP <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kontak" class="form-control rounded-3 @error('kontak') is-invalid @enderror"
                                value="{{ old('kontak', $peserta->kontak) }}" required>
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KECAMATAN (DROPDOWN BARU) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Kecamatan <span class="text-danger">*</span>
                            </label>
                            <select name="kecamatan" class="form-select rounded-3 @error('kecamatan') is-invalid @enderror" required>
                                <option value="">-- Pilih Kecamatan --</option>
                                @php
                                    $daftarKecamatan = [
                                        'Banjarmasin Barat',
                                        'Banjarmasin Selatan',
                                        'Banjarmasin Tengah',
                                        'Banjarmasin Timur',
                                        'Banjarmasin Utara'
                                    ];
                                @endphp
                                @foreach($daftarKecamatan as $kec)
                                    <option value="{{ $kec }}" {{ old('kecamatan', $peserta->kecamatan) == $kec ? 'selected' : '' }}>
                                        {{ $kec }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kecamatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PUSKESMAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Puskesmas <span class="text-danger">*</span>
                            </label>

                            @if(auth()->user()->role_name === 'admin')
                                <select name="puskesmas_id" class="form-select rounded-3 @error('puskesmas_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Puskesmas --</option>
                                    @foreach($puskesmas as $pkm)
                                        <option value="{{ $pkm->id }}" {{ old('puskesmas_id', $peserta->puskesmas_id) == $pkm->id ? 'selected' : '' }}>
                                            {{ $pkm->nama_puskesmas }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control rounded-3 bg-light"
                                    value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas ?? '-' }}" readonly>
                                <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                            @endif

                            @error('puskesmas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ALAMAT --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Alamat Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea name="alamat" rows="3" class="form-control rounded-3 @error('alamat') is-invalid @enderror"
                                required>{{ old('alamat', $peserta->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- ================= ACTION ================= --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.peserta.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
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

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection