@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">Edit Data Pejabat</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pejabat.update', $pejabat->id) }}" method="POST">
                    @csrf
                    @method('PUT') <div class="mb-3">
                        <label>Nama Lengkap (Beserta Gelar)</label>
                        <input type="text" name="nama_kepala" class="form-control" value="{{ $pejabat->nama_kepala }}"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ $pejabat->nip }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ $pejabat->jabatan }}" required>
                    </div>

                    <button type="submit" class="btn btn-success">Update Data</button>
                    <a href="{{ route('admin.pejabat.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection