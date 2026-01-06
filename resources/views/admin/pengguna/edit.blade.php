@extends('layouts.master')

@section('title', 'Edit Pengguna (Pegawai Dinkes)')

@section('content')
    <div class="container-fluid py-3" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0">Edit Pengguna (Pegawai Dinkes)</h4>
                    <small class="text-muted">
                        Perbarui data pegawai dinas kesehatan
                    </small>
                </div>

                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- ================= ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">

            {{-- ================================================= --}}
            {{-- CARD 1 : DATA PEGAWAI (SAMA SEPERTI PETUGAS) --}}
            {{-- ================================================= --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header fw-bold">
                        Data Pegawai Dinkes
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
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
                                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>

                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- CARD 2 : AKSES AKUN (SAMA SEPERTI PETUGAS) --}}
            {{-- ================================================= --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header fw-bold">
                        Akses Akun
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.pengguna.updateAccess', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role_name" class="form-select">
                                    <option value="pengguna" {{ $user->role_name == 'pengguna' ? 'selected' : '' }}>
                                        Pengguna
                                    </option>
                                    <option value="petugas" {{ $user->role_name == 'petugas' ? 'selected' : '' }}>
                                        Petugas
                                    </option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Status Akun</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>
                                        Nonaktif
                                    </option>
                                </select>
                            </div>

                            <button class="btn btn-primary w-100">
                                <i class="bi bi-shield-check"></i> Simpan Akses
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection