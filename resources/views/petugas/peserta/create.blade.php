@extends('layouts.master')

@section('title', 'Tambah Data Pasien')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Tambah Data Pasien</h4>
                <small class="opacity-75">
                    Lengkapi data pasien dengan benar sebelum disimpan
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('petugas.peserta.store') }}" method="POST">
                    @csrf

                        <div class="row g-3">

                            {{-- NIK (BARU) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nik" class="form-control rounded-3 @error('nik') is-invalid @enderror" placeholder="16 Digit NIK" minlength="16"
                                    maxlength="16" value="{{ old('nik') }}" required
                                    oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'NIK (Nomor Induk Kependudukan) wajib diisi.' : (this.validity.tooShort || this.validity.tooLong ? 'NIK harus terdiri dari tepat 16 digit angka.' : 'NIK tidak valid.'))"
                                    oninput="this.setCustomValidity('')">
                                @error('nik')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- NO RM --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nomor Rekam Medis <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="no_rekam_medis" class="form-control rounded-3 @error('no_rekam_medis') is-invalid @enderror" placeholder="Contoh: RM-001" value="{{ old('no_rekam_medis') }}" required
                                    oninvalid="this.setCustomValidity('Nomor Rekam Medis wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    Sistem akan otomatis menambahkan prefiks Puskesmas (contoh: <code>Pk-002/RM-001</code>).
                                </small>
                                @error('no_rekam_medis')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- NAMA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" class="form-control rounded-3 @error('nama_lengkap') is-invalid @enderror" placeholder="Nama lengkap pasien" value="{{ old('nama_lengkap') }}"
                                    required
                                    oninvalid="this.setCustomValidity('Nama lengkap pasien wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                @error('nama_lengkap')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- JENIS KELAMIN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_kelamin" class="form-select rounded-3 @error('jenis_kelamin') is-invalid @enderror" required
                                    oninvalid="this.setCustomValidity('Jenis kelamin wajib dipilih.')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- TEMPAT LAHIR (BARU) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tempat Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tempat_lahir" class="form-control rounded-3 @error('tempat_lahir') is-invalid @enderror" placeholder="Kota/Kabupaten kelahiran" value="{{ old('tempat_lahir') }}"
                                    required
                                    oninvalid="this.setCustomValidity('Tempat lahir wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                @error('tempat_lahir')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- TANGGAL LAHIR --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tanggal Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3 @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required
                                    oninvalid="this.setCustomValidity('Tanggal lahir wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                @error('tanggal_lahir')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- PEKERJAAN (BARU) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Pekerjaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="pekerjaan" class="form-control rounded-3 @error('pekerjaan') is-invalid @enderror" placeholder="Pekerjaan saat ini" value="{{ old('pekerjaan') }}" required
                                    oninvalid="this.setCustomValidity('Pekerjaan pasien wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                @error('pekerjaan')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- KONTAK --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nomor Kontak / HP <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kontak" class="form-control rounded-3 @error('kontak') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('kontak') }}" required
                                    oninvalid="this.setCustomValidity('Nomor kontak / HP wajib diisi.')"
                                    oninput="this.setCustomValidity('')">
                                @error('kontak')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- KECAMATAN (DROPDOWN) --}}
                            @php
                                $defaultKecamatan = '';
                                if (auth()->check() && auth()->user()->role_name === 'petugas' && auth()->user()->petugas && auth()->user()->petugas->puskesmas) {
                                    $defaultKecamatan = trim(auth()->user()->petugas->puskesmas->kecamatan);
                                }
                            @endphp
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Kecamatan <span class="text-danger">*</span>
                                </label>
                                <select name="kecamatan" class="form-select rounded-3 @error('kecamatan') is-invalid @enderror" required
                                    oninvalid="this.setCustomValidity('Kecamatan wajib dipilih.')"
                                    oninput="this.setCustomValidity('')">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <option value="Banjarmasin Barat" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Barat' ? 'selected' : '' }}>Banjarmasin Barat</option>
                                    <option value="Banjarmasin Selatan" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Selatan' ? 'selected' : '' }}>Banjarmasin Selatan</option>
                                    <option value="Banjarmasin Tengah" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Tengah' ? 'selected' : '' }}>Banjarmasin Tengah</option>
                                    <option value="Banjarmasin Timur" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Timur' ? 'selected' : '' }}>Banjarmasin Timur</option>
                                    <option value="Banjarmasin Utara" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Utara' ? 'selected' : '' }}>Banjarmasin Utara</option>
                                </select>
                                @error('kecamatan')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            @if(auth()->user()->role_name === 'admin')
                                {{-- PUSKESMAS --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Puskesmas <span class="text-danger">*</span>
                                    </label>
                                    <select name="puskesmas_id" class="form-select rounded-3 @error('puskesmas_id') is-invalid @enderror" required
                                        oninvalid="this.setCustomValidity('Puskesmas wajib dipilih.')"
                                        oninput="this.setCustomValidity('')">
                                        <option value="">-- Pilih Puskesmas --</option>
                                        @foreach($puskesmas as $pkm)
                                            <option value="{{ $pkm->id }}" {{ old('puskesmas_id') == $pkm->id ? 'selected' : '' }}>
                                                {{ $pkm->nama_puskesmas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('puskesmas_id')
                                        <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                            @endif

                            {{-- ALAMAT --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat" rows="3" class="form-control rounded-3 @error('alamat') is-invalid @enderror" placeholder="Masukkan Alamat Lengkap"
                                    required
                                    oninvalid="this.setCustomValidity('Alamat lengkap wajib diisi.')"
                                    oninput="this.setCustomValidity('')">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                    {{-- ================= ACTION ================= --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.peserta.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left"></i> Kembali
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

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection