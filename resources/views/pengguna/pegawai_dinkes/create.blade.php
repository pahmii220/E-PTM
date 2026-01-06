@extends('layouts.master')

@section('title', 'Lengkapi Profil Pegawai Dinkes')

@section('content')
    <div class="container-fluid py-3" style="max-width:1200px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="fw-bold mb-0">Lengkapi Profil Pegawai Dinkes</h4>
                <small class="text-muted">
                    Silakan lengkapi data diri sebelum menggunakan aplikasi
                </small>
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

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold">
                Data Pegawai Dinkes
            </div>

            <div class="card-body">
                <form action="{{ route('pengguna.pegawai_dinkes.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pegawai" class="form-control" required
                                value="{{ old('nama_pegawai', auth()->user()->Nama_Lengkap) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control"
                                value="{{ old('nip', auth()->user()->nip) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <input type="text" name="bidang" class="form-control" value="{{ old('bidang') }}">
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('pengguna.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Profil
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
@endsection