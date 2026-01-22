@extends('layouts.master')

@section('title', 'Tambah Petugas')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#ecfdf5,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold mb-0">Tambah Petugas</h4>
                    <small class="text-muted">Input data petugas puskesmas</small>
                </div>

            </div>
        </div>

        {{-- ================= FORM CARD ================= --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body p-4">

                {{-- ALERT ERROR --}}
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

                <form action="{{ route('admin.data_petugas.store') }}" method="POST">
                    @csrf

                    {{-- IDENTITAS --}}
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">NIP</label>
                            <input type="text" name="nip" class="form-control rounded-3" value="{{ old('nip') }}"
                                placeholder="Nomor Induk Pegawai">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Nama Pegawai <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_pegawai" class="form-control rounded-3"
                                value="{{ old('nama_pegawai') }}" placeholder="Nama lengkap" required>
                        </div>
                    </div>

                    {{-- KONTAK --}}
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3"
                                value="{{ old('tanggal_lahir') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telepon</label>
                            <input type="text" name="telepon" class="form-control rounded-3" value="{{ old('telepon') }}"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    {{-- ALAMAT --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Alamat</label>
                        <textarea name="alamat" rows="2" class="form-control rounded-3"
                            placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                    </div>

                    {{-- JABATAN --}}
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="jabatan" class="form-control rounded-3" value="{{ old('jabatan') }}"
                                placeholder="Contoh: Perawat" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Bidang</label>
                            <input type="text" name="bidang" class="form-control rounded-3" value="{{ old('bidang') }}"
                                placeholder="Contoh: Kesehatan Masyarakat">
                        </div>
                    </div>

                    {{-- PUSKESMAS --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium">Puskesmas</label>
                        <select name="puskesmas_id" class="form-select rounded-3">
                            <option value="">— Pilih Puskesmas —</option>
                            @foreach($puskesmas ?? [] as $p)
                                <option value="{{ $p->id }}" {{ old('puskesmas_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>
                        @error('puskesmas_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.data_petugas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
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