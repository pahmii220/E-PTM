@extends('layouts.master')

@section('title', 'Tambah Petugas')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px">

    {{-- ================= HEADER ================= --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4"
        style="background:linear-gradient(135deg,#ecfdf5,#f8fafc); backdrop-filter: blur(6px)">
        <div class="card-body">
            <h4 class="fw-bold mb-0">Tambah Petugas</h4>
            <small class="text-muted">Input data petugas puskesmas dan akun login</small>
        </div>
    </div>

    {{-- ================= FORM ================= --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.data_petugas.store') }}" method="POST">
                @csrf

                {{-- IDENTITAS --}}
                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">NIP</label>
                        <input type="text" name="nip" class="form-control rounded-3"
                            value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai">
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
                        <input type="text" name="telepon" class="form-control rounded-3"
                            value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx">
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
                        <input type="text" name="jabatan" class="form-control rounded-3"
                            value="{{ old('jabatan') }}" placeholder="Contoh: Perawat" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Bidang</label>
                        <input type="text" name="bidang" class="form-control rounded-3"
                            value="{{ old('bidang') }}" placeholder="Contoh: Kesehatan Masyarakat">
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
                </div>

                {{-- ================= AKUN LOGIN ================= --}}
                <hr class="my-4">

                <h6 class="fw-bold mb-3">Akun Login Petugas</h6>

                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="username" class="form-control rounded-3"
                            value="{{ old('username') }}" placeholder="Username login" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">
                            Password Awal <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password" class="form-control rounded-3"
                            placeholder="Minimal 8 karakter" required>
                        <small class="text-muted">
                            Password awal, petugas dapat mengganti setelah login
                        </small>
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.data_petugas.index') }}"
                        class="btn btn-light rounded-pill px-4 shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc; }
</style>
@endsection
