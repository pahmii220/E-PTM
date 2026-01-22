@extends('layouts.master')

@section('title', 'Edit Pengguna (Pegawai Dinkes)')

@section('content')
    <div class="container-fluid py-3" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Edit Pengguna</h4>
                    <small class="text-muted">Kelola data pegawai dinkes dan hak akses akun</small>
                </div>
            </div>
        </div>

        {{-- ================= ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================================================= --}}
        {{-- CARD 1 : DATA PEGAWAI (HIJAU) --}}
        {{-- ================================================= --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
                <i class="bi bi-person-badge-fill me-1"></i> Data Pegawai Dinkes
            </div>

            <div class="card-body">
                <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="Nama_Lengkap" class="form-control"
                                value="{{ old('Nama_Lengkap', $user->Nama_Lengkap) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip', $user->nip) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                value="{{ old('jabatan', $pegawai->jabatan ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <input type="text" name="bidang" class="form-control"
                                value="{{ old('bidang', $pegawai->bidang ?? '') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2"
                                class="form-control">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Data Pegawai
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- CARD 2 : AKUN & ROLE (BIRU) --}}
        {{-- ================================================= --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill"></i>
                Kelola Akun & Role
                <span class="badge bg-warning text-dark ms-auto">
                    Akses & Keamanan
                </span>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.pengguna.updateAccess', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role_name" class="form-select">
                                <option value="pengguna" {{ $user->role_name == 'pengguna' ? 'selected' : '' }}>
                                    Pengguna
                                </option>
                                <option value="petugas" {{ $user->role_name == 'petugas' ? 'selected' : '' }}>
                                    Petugas
                                </option>
                            </select>
                            <small class="text-muted">Menentukan hak akses sistem</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Akun</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            <small class="text-muted">Nonaktifkan untuk memblokir login</small>
                        </div>

                        <div class="col-md-4 d-flex gap-2">
                            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary w-50">
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

    </div>
@endsection