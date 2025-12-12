@extends('layouts.master')

@section('title', 'Tambah Petugas')

@section('content')
<br>
    <div class="container-fluid py-1 px-4" style="max-width: 1400px; margin: auto; margin-top: -10px;">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Tambah Petugas</h4>
                </div>
            </div>

            <div class="card-body">
                {{-- Validasi error --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <br>
                @endif

                <form action="{{ route('admin.data_petugas.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="nama_pegawai" class="form-label">Nama Pegawai <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" id="nama_pegawai" class="form-control"
                                value="{{ old('nama_pegawai') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                                value="{{ old('tanggal_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" name="telepon" id="telepon" class="form-control"
                                value="{{ old('telepon') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="2" class="form-control">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan') }}"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label for="bidang" class="form-label">Bidang</label>
                            <input type="text" name="bidang" id="bidang" class="form-control" value="{{ old('bidang') }}">
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.data_petugas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Styling tambahan agar konsisten dengan halaman pasien --}}
    <style>
        body {
            background-color: #f8fafc;
        }

        .card {
            overflow: hidden;
            margin-top: -25px !important;
        }

        @media (min-width: 1400px) {
            .container-fluid {
                max-width: 1500px !important;
            }
        }

        @media (max-width: 991px) {
            .container-fluid {
                padding: 10px;
            }
        }
    </style>
@endsection