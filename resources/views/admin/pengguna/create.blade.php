@extends('layouts.master')

@section('title', 'Tambah Pengguna (Pegawai Dinkes)')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px">

    {{-- ================= HEADER ================= --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4"
         style="background:linear-gradient(135deg,#eef2ff,#f8fafc); backdrop-filter: blur(6px)">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h4 class="fw-bold mb-0">Tambah Pengguna</h4>
                <small class="text-muted">Input data pegawai dinas kesehatan</small>
            </div>


        </div>
    </div>

    {{-- ================= FORM ================= --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="card-body p-4">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.pengguna.store') }}" method="POST">
                @csrf

                {{-- AKUN --}}
                <h6 class="fw-semibold mb-3 text-primary">Informasi Akun</h6>
                <div class="row g-4 mb-4">

                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="Username"
                               class="form-control rounded-3"
                               placeholder="Username login"
                               value="{{ old('Username') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email"
                               class="form-control rounded-3"
                               placeholder="email@contoh.com"
                               value="{{ old('email') }}">
                    </div>

                </div>

                {{-- DATA PEGAWAI --}}
                <h6 class="fw-semibold mb-3 text-primary">Data Pegawai Dinkes</h6>
                <div class="row g-4 mb-4">

                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="Nama_Lengkap"
                               class="form-control rounded-3"
                               placeholder="Nama lengkap pegawai"
                               value="{{ old('Nama_Lengkap') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">NIP</label>
                        <input type="text" name="nip"
                               class="form-control rounded-3"
                               placeholder="Nomor Induk Pegawai"
                               value="{{ old('nip') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Jabatan</label>
                        <input type="text" name="jabatan"
                               class="form-control rounded-3"
                               placeholder="Contoh: Staf Kesehatan"
                               value="{{ old('jabatan') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Bidang</label>
                        <input type="text" name="bidang"
                               class="form-control rounded-3"
                               placeholder="Contoh: Pencegahan Penyakit"
                               value="{{ old('bidang') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-medium">Alamat</label>
                        <textarea name="alamat"
                                  rows="2"
                                  class="form-control rounded-3"
                                  placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.pengguna.index') }}"
                       class="btn btn-light rounded-pill px-4 shadow-sm">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit"
                            class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save"></i> Simpan
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
