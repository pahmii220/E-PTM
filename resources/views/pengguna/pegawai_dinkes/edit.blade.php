@extends('layouts.master')

@section('title', 'Profil Pegawai Dinkes')

@section('content')
    <div class="container py-4" style="max-width:800px">

        <h4 class="fw-bold mb-3">Profil Pegawai Dinas Kesehatan</h4>
        <p class="text-muted mb-4">
            Lengkapi data profil Anda untuk keperluan verifikasi dan administrasi.
        </p>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST"
      action="{{ route('pengguna.pegawai_dinkes.update', Auth::user()->id) }}">
    @csrf
    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
    <label class="form-label">NIP</label>

    {{-- tampilkan saja, tidak bisa diubah --}}
    <input type="text"
           class="form-control bg-light"
           value="{{ Auth::user()->nip ?? optional($pegawai)->nip }}"
           readonly>

    {{-- hidden input agar tetap tersimpan saat update --}}
    <input type="hidden"
           name="nip"
           value="{{ Auth::user()->nip ?? optional($pegawai)->nip }}">
</div>


                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_pegawai" class="form-control"
                                value="{{ old('nama_pegawai', optional($pegawai)->nama_pegawai) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                value="{{ old('jabatan', optional($pegawai)->jabatan) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bidang</label>
                            <input type="text" name="bidang" class="form-control" value="{{ old('bidang', optional($pegawai)->bidang) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telepon" class="form-control"
                                value="{{ old('telepon', optional($pegawai)->telepon) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', optional($pegawai)->alamat) }}</textarea>
                        </div>

                    </div>


                    <div class="mt-4 text-end">
                        <button class="btn btn-success px-4">
                            <i class="bi bi-save me-1"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection