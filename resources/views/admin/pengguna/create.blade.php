@extends('layouts.master')

@section('title', 'Tambah Pengguna (Pegawai Dinkes)')

@section('content')
    <div class="container-fluid py-3" style="max-width:1400px">

        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Tambah Pengguna (Pegawai Dinkes)</h4>
                    <small class="text-muted">Input data pegawai dinas kesehatan</small>
                </div>
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- FORM --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold">Data Pegawai Dinkes</div>

            <div class="card-body">
                <form action="{{ route('admin.pengguna.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- AKUN --}}
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="Username" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="Nama_Lengkap" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control">
                        </div>

                        {{-- DATA PEGAWAI --}}
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <input type="text" name="bidang" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection