@extends('layouts.master')

@section('title', 'Lengkapi Profil Pegawai Dinkes')

@section('content')
    <div class="container-fluid py-4" style="max-width:1000px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="bi bi-person-lines-fill fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Lengkapi Profil Pegawai</h4>
                    <span class="text-muted small">
                        Silakan lengkapi identitas dan area tugas Anda sebelum menggunakan aplikasi PTM.
                    </span>
                </div>
            </div>
        </div>

        {{-- ================= ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-4 border-0 border-start border-danger border-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <strong>Terdapat Kesalahan:</strong>
                </div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white fw-bold py-3 border-bottom border-light">
                Formulir Data Diri
            </div>

            <div class="card-body p-4">
                {{-- WAJIB tambahkan enctype="multipart/form-data" untuk upload file --}}
                <form action="{{ route('pengguna.pegawai_dinkes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        {{-- BAGIAN FOTO PROFIL --}}
                        <div class="col-12 text-center mb-2">
                            <label class="form-label fw-semibold d-block">Foto Profil (Opsional)</label>
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center mb-3 shadow-sm"
                                    style="width: 120px; height: 120px; overflow: hidden;">
                                    <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                                    {{-- Nanti Anda bisa tambahkan logika untuk menampilkan foto lama di sini jika sedang
                                    mode edit --}}
                                </div>
                                <input type="file" name="foto" class="form-control form-control-sm"
                                    style="max-width: 300px;" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted mt-2">Format yang diizinkan: JPG, JPEG, PNG. Maksimal 2MB.</small>
                            </div>
                        </div>

                        <hr class="text-light my-4">

                        {{-- INFORMASI PRIBADI --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" class="form-control" required
                                placeholder="Masukkan nama lengkap beserta gelar"
                                value="{{ old('nama_pegawai', auth()->user()->Nama_Lengkap ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP / NRPTT</label>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP (Jika ada)"
                                value="{{ old('nip', auth()->user()->nip ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telepon" class="form-control" placeholder="Contoh: 081234567890"
                                value="{{ old('telepon') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-control"
                                placeholder="Masukkan alamat domisili saat ini">{{ old('alamat') }}</textarea>
                        </div>

                        <hr class="text-light my-4">

                        {{-- INFORMASI JABATAN & WILAYAH TUGAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                placeholder="Contoh: Staff Surveilans / Kepala Seksi" value="{{ old('jabatan') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bidang / Sub-Bidang</label>
                            <input type="text" name="bidang" class="form-control"
                                placeholder="Contoh: P2PTM (Pencegahan & Pengendalian PTM)" value="{{ old('bidang') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provinsi Wilayah Tugas</label>
                            <select name="provinsi" class="form-select">
                                <option value="">-- Pilih Provinsi --</option>
                                <option value="Kalimantan Selatan" {{ old('provinsi') == 'Kalimantan Selatan' ? 'selected' : '' }}>Kalimantan Selatan</option>
                                <option value="Kalimantan Tengah" {{ old('provinsi') == 'Kalimantan Tengah' ? 'selected' : '' }}>Kalimantan Tengah</option>
                                <option value="Kalimantan Timur" {{ old('provinsi') == 'Kalimantan Timur' ? 'selected' : '' }}>Kalimantan Timur</option>
                                {{-- Tambahkan provinsi lainnya sesuai kebutuhan, atau ambil dari database --}}
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kabupaten / Kota</label>
                            <input type="text" name="kabupaten_kota" class="form-control"
                                placeholder="Contoh: Kota Banjarmasin" value="{{ old('kabupaten_kota') }}">
                            <small class="text-muted">Isi jika Anda bertugas atau bertanggung jawab untuk area
                                spesifik.</small>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <a href="{{ route('pengguna.dashboard') }}" class="btn btn-light border px-4 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Profil
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection