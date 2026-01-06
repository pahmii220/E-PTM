@extends('layouts.master')

@section('title', 'Tambah Data Pasien')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Tambah Data Peserta</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.pasien.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="no_rekam_medis" class="form-label">Nomor Rekam Medis</label>
                            <input type="text" name="no_rekam_medis" id="no_rekam_medis" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="2" class="form-control" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="kontak" class="form-label">Nomor Kontak / HP</label>
                            <input type="text" name="kontak" id="kontak" class="form-control" required>
                        </div>
                        
                        {{-- PUSKESMAS --}}
<div class="col-md-6">
    <label class="form-label fw-semibold">
        Puskesmas <span class="text-danger">*</span>
    </label>

    @if(auth()->user()->role_name === 'admin')
        {{-- ADMIN: PILIH PUSKESMAS --}}
        <select name="puskesmas_id"
            class="form-select @error('puskesmas_id') is-invalid @enderror" required>
            <option value="">-- Pilih Puskesmas --</option>
            @foreach($puskesmas as $pkm)
                <option value="{{ $pkm->id }}">
                    {{ $pkm->nama_puskesmas }}
                </option>
            @endforeach
        </select>
    @else
        {{-- PETUGAS: TERKUNCI --}}
        <input type="text" class="form-control"
            value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
        <input type="hidden" name="puskesmas_id"
            value="{{ auth()->user()->petugas->puskesmas_id }}">
    @endif

    @error('puskesmas_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>



                    </div>

                    <div class="text-end">
                        <a href="{{ route('petugas.pasien.index') }}" class="btn btn-secondary">
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
@endsection