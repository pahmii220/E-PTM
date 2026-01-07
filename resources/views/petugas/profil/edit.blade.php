@extends('layouts.master')

@section('title', 'Profil Petugas')

@section('content')
<div class="container-fluid py-3" style="max-width:1200px">

    {{-- ================= HEADER ================= --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-person-badge-fill text-success me-1"></i>
                    Profil Petugas
                </h4>
                <small class="text-muted">
                    Lengkapi data diri petugas puskesmas
                </small>
            </div>
        </div>
    </div>

    {{-- ================= FORM ================= --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header fw-semibold bg-white">
            Data Petugas
        </div>

        <div class="card-body">
                <form method="POST" action="{{ route('petugas.profil.update') }}">
    @csrf


                <div class="row g-3">

                    {{-- NIP --}}
                    <div class="col-md-6">
                        <label class="form-label">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control"
                            value="{{ old('nip', $petugas->nip ?? '') }}">
                    </div>

                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pegawai" class="form-control"
                            value="{{ old('nama_pegawai', $petugas->nama_pegawai ?? '') }}">
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control"
                            value="{{ old('tanggal_lahir', $petugas->tanggal_lahir ?? '') }}">
                    </div>

                    {{-- Telepon --}}
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="telepon" class="form-control"
                            value="{{ old('telepon', $petugas->telepon ?? '') }}">
                    </div>

                    {{-- Alamat --}}
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"
                            placeholder="Alamat lengkap">{{ old('alamat', $petugas->alamat ?? '') }}</textarea>
                    </div>

                    {{-- Jabatan --}}
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control"
                            value="{{ old('jabatan', $petugas->jabatan ?? '') }}">
                    </div>

                    {{-- Bidang --}}
                    <div class="col-md-6">
                        <label class="form-label">Bidang</label>
                        <input type="text" name="bidang" class="form-control"
                            value="{{ old('bidang', $petugas->bidang ?? '') }}">
                    </div>

                    {{-- Puskesmas --}}
                    <div class="col-md-12">
                        <label class="form-label">Puskesmas <span class="text-danger">*</span></label>
                        <select name="puskesmas_id" class="form-select">
                            <option value="">-- Pilih Puskesmas --</option>
                            @foreach($puskesmas as $p)
                                <option value="{{ $p->id }}"
                                    {{ ($petugas->puskesmas_id ?? '') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- ================= ACTION ================= --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    <button class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan Profil
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
