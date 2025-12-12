@extends('layouts.master')

@section('title', 'Edit Data Puskesmas')

@section('content')
        <div class="container mt-4" style="max-width: 900px;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Data Puskesmas</h4>
                    <a href="{{ route('admin.data_puskesmas.index') }}" class="btn btn-light btn-sm">Kembali</a>
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

    <form
        action="{{ isset($puskesmas) ? route('admin.data_puskesmas.update', $puskesmas->id) : route('admin.data_puskesmas.store') }}"
        method="POST">
        @csrf

        @if(isset($puskesmas))
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kode Puskesmas</label>
                <input type="text" name="kode_puskesmas" class="form-control"
                    value="{{ old('kode_puskesmas', $puskesmas->kode_puskesmas ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Puskesmas</label>
                <input type="text" name="nama_puskesmas" class="form-control"
                    value="{{ old('nama_puskesmas', $puskesmas->nama_puskesmas ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Kabupaten</label>
                <input type="text" name="nama_kabupaten" class="form-control"
                    value="{{ old('nama_kabupaten', $puskesmas->nama_kabupaten ?? '') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kecamatan</label>
                <input type="text" name="kecamatan" class="form-control"
                    value="{{ old('kecamatan', $puskesmas->kecamatan ?? '') }}" required>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control"
                    rows="2">{{ old('alamat', $puskesmas->alamat ?? '') }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kode Pos</label>
                <input type="text" name="kode_pos" class="form-control"
                    value="{{ old('kode_pos', $puskesmas->kode_pos ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $puskesmas->email ?? '') }}">
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('admin.data_puskesmas.index') }}" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </form>


                </div>
            </div>
        </div>
@endsection