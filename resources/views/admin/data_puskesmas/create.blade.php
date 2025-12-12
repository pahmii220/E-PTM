@extends('layouts.master')

@section('title', 'Tambah Data Puskesmas')

@section('content')
    <div class="container mt-4" style="max-width: 900px;">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Tambah Data Puskesmas</h4>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.data_puskesmas.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Puskesmas</label>
                            <input type="text" name="kode_puskesmas" class="form-control"
                                value="{{ old('kode_puskesmas') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Puskesmas</label>
                            <input type="text" name="nama_puskesmas" class="form-control"
                                value="{{ old('nama_puskesmas') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Kabupaten</label>
                            <input type="text" name="nama_kabupaten" class="form-control"
                                value="{{ old('nama_kabupaten') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('admin.data_puskesmas.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection