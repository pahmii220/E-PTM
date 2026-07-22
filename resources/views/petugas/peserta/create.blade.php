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
                                <input type="text" name="nik" class="form-control rounded-3" placeholder="16 Digit NIK" minlength="16"
                                    maxlength="16" required>
                            </div>

                            {{-- NO RM --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nomor Rekam Medis <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="no_rekam_medis" class="form-control rounded-3" placeholder="Contoh: RM-001" required>
                                <small class="text-muted" style="font-size: 11px;">
                                    Sistem akan otomatis menambahkan prefiks Puskesmas (contoh: <code>Pk-002/RM-001</code>).
                                </small>
                            </div>

                            {{-- NAMA --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" class="form-control rounded-3" placeholder="Nama lengkap pasien"
                                    required>
                            </div>

                            {{-- JENIS KELAMIN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_kelamin" class="form-select rounded-3" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            {{-- TEMPAT LAHIR (BARU) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tempat Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="tempat_lahir" class="form-control rounded-3" placeholder="Kota/Kabupaten kelahiran"
                                    required>
                            </div>

                            {{-- TANGGAL LAHIR --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tanggal Lahir <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3" required>
                            </div>

                            {{-- PEKERJAAN (BARU) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Pekerjaan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="pekerjaan" class="form-control rounded-3" placeholder="Pekerjaan saat ini" required>
                            </div>

                            {{-- KONTAK --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nomor Kontak / HP <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kontak" class="form-control rounded-3" placeholder="08xxxxxxxxxx" required>
                            </div>

                            {{-- KECAMATAN (BARU) --}}
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
                                <select name="kecamatan" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <option value="Banjarmasin Barat" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Barat' ? 'selected' : '' }}>Banjarmasin Barat</option>
                                    <option value="Banjarmasin Selatan" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Selatan' ? 'selected' : '' }}>Banjarmasin Selatan</option>
                                    <option value="Banjarmasin Tengah" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Tengah' ? 'selected' : '' }}>Banjarmasin Tengah</option>
                                    <option value="Banjarmasin Timur" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Timur' ? 'selected' : '' }}>Banjarmasin Timur</option>
                                    <option value="Banjarmasin Utara" {{ old('kecamatan', $defaultKecamatan) == 'Banjarmasin Utara' ? 'selected' : '' }}>Banjarmasin Utara</option>
                                </select>
                            </div>

                            @if(auth()->user()->role_name === 'admin')
                                {{-- PUSKESMAS --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Puskesmas <span class="text-danger">*</span>
                                    </label>
                                    <select name="puskesmas_id" class="form-select rounded-3" required>
                                        <option value="">-- Pilih Puskesmas --</option>
                                        @foreach($puskesmas as $pkm)
                                            <option value="{{ $pkm->id }}">
                                                {{ $pkm->nama_puskesmas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                            @endif

                            {{-- ALAMAT --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat" rows="3" class="form-control rounded-3" placeholder="Masukkan Alamat Lengkap"
                                    required></textarea>
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