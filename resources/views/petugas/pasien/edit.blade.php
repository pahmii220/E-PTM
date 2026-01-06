@extends('layouts.master')

@section('title', 'Edit Data Pasien')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Data Peserta</h4>
            </div>

            <div class="card-body p-4">

                {{-- Global validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('petugas.pasien.update', $pasien->id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- Nama Lengkap --}}
                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input id="nama_lengkap" type="text" name="nama_lengkap"
                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                value="{{ old('nama_lengkap', $pasien->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Puskesmas --}}
                            {{-- PUSKESMAS --}}
<div class="col-md-6">
    <label class="form-label fw-semibold">
        Puskesmas <span class="text-danger">*</span>
    </label>

    @if(auth()->user()->role_name === 'admin')
        {{-- ADMIN: BOLEH PILIH --}}
        <select name="puskesmas_id"
            class="form-select @error('puskesmas_id') is-invalid @enderror" required>
            <option value="">-- Pilih Puskesmas --</option>
            @foreach($puskesmas as $pkm)
                <option value="{{ $pkm->id }}"
                    {{ old('puskesmas_id', $pasien->puskesmas_id) == $pkm->id ? 'selected' : '' }}>
                    {{ $pkm->nama_puskesmas }}
                </option>
            @endforeach
        </select>
    @else
        {{-- PETUGAS: TERKUNCI --}}
        <input type="text" class="form-control"
            value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
        <input type="hidden" name="puskesmas_id"
            value="{{ auth()->user()->petugas->puskesmas_id }}">
    @endif

    @error('puskesmas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>



                        {{-- Nomor Rekam Medis --}}
                        <div class="col-md-6">
                            <label for="no_rekam_medis" class="form-label fw-semibold">Nomor Rekam Medis <span
                                    class="text-danger">*</span></label>
                            <input id="no_rekam_medis" type="text" name="no_rekam_medis"
                                class="form-control @error('no_rekam_medis') is-invalid @enderror"
                                value="{{ old('no_rekam_medis', $pasien->no_rekam_medis) }}" required>
                            @error('no_rekam_medis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin <span
                                    class="text-danger">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label fw-semibold">Tanggal Lahir</label>
                            <input id="tanggal_lahir" type="date" name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ old('tanggal_lahir', optional($pasien->tanggal_lahir)->format('Y-m-d')) }}">
                            @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div class="col-md-6">
                            <label for="kontak" class="form-label fw-semibold">No. HP <span
                                    class="text-danger">*</span></label>
                            <input id="kontak" type="text" name="kontak"
                                class="form-control @error('kontak') is-invalid @enderror"
                                value="{{ old('kontak', $pasien->kontak) }}" required>
                            @error('kontak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="col-12">
                            <label for="alamat" class="form-label fw-semibold">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                rows="3">{{ old('alamat', $pasien->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('petugas.pasien.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 12px;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .text-danger {
            color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection