@extends('layouts.master')

@section('title', 'Edit Petugas')

@section('content')
    <div class="container-fluid py-3" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Edit Petugas</h4>
                    <small class="text-muted">Kelola data petugas dan hak akses akun</small>
                </div>
            </div>
        </div>

        {{-- ================= ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================================================= --}}
        {{-- CARD 1 : DATA PETUGAS (HIJAU) --}}
        {{-- ================================================= --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
                <i class="bi bi-person-badge-fill me-1"></i> Data Petugas
            </div>

            <div class="card-body">
                <form action="{{ route('admin.data_petugas.update', $petugas) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip', $petugas->nip) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" class="form-control" required
                                value="{{ old('nama_pegawai', $petugas->nama_pegawai) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir', optional($petugas->tanggal_lahir)->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control"
                                value="{{ old('telepon', $petugas->telepon) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="form-control">{{ old('alamat', $petugas->alamat) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" class="form-control" required
                                value="{{ old('jabatan', $petugas->jabatan) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <input type="text" name="bidang" class="form-control"
                                value="{{ old('bidang', $petugas->bidang) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Puskesmas</label>
                            <select name="puskesmas_id" class="form-select">
                                <option value="">-- Pilih Puskesmas --</option>
                                @foreach($puskesmas as $p)
                                    <option value="{{ $p->id }}" {{ old('puskesmas_id', $petugas->puskesmas_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_puskesmas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- CARD 2 : AKUN & ROLE (BIRU / KEAMANAN) --}}
        {{-- ================================================= --}}
        @if($petugas->user)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock-fill"></i>
                    Kelola Akun & Role
                    <span class="badge bg-warning text-dark ms-auto">
                        Akses & Keamanan
                    </span>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.data_petugas.update', $petugas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="update_account_only" value="1">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Akun</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $petugas->user->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$petugas->user->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                <small class="text-muted">Nonaktifkan untuk memblokir login</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Role </label>
                                <select name="role_name" class="form-select">
                                    <option value="petugas" {{ $petugas->user->role_name == 'petugas' ? 'selected' : '' }}>
                                        Petugas
                                    </option>
                                    <option value="pengguna" {{ $petugas->user->role_name == 'pengguna' ? 'selected' : '' }}>
                                        Pengguna
                                    </option>
                                </select>
                                <small class="text-muted">Menentukan hak akses sistem</small>
                            </div>

                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <a href="{{ route('admin.data_petugas.index') }}" class="btn btn-outline-secondary w-50">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>

                                <button type="submit" class="btn btn-primary w-50">
                                    <i class="bi bi-shield-check"></i> Simpan Akun
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
@endsection