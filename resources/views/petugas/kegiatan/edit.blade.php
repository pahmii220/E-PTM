@extends('layouts.master')

@section('title', 'Edit Data Kegiatan PTM')

@section('content')
<div class="container-fluid py-4" style="max-width:1100px">

    {{-- ================= HEADER ================= --}}
    <div class="card border-0 shadow-sm mb-4 rounded-4"
         style="background:linear-gradient(135deg,#22c55e,#16a34a)">
        <div class="card-body text-white">
            <h4 class="fw-bold mb-0">Edit Data Kegiatan PTM</h4>
            <small class="opacity-75">
                Perbarui data kegiatan PTM sebelum disimpan
            </small>
        </div>
    </div>

    {{-- ================= FORM ================= --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- VALIDATION ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('petugas.kegiatan.update',$kegiatan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- NAMA KEGIATAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Nama Kegiatan <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="nama_kegiatan"
                               class="form-control rounded-3 @error('nama_kegiatan') is-invalid @enderror"
                               value="{{ old('nama_kegiatan',$kegiatan->nama_kegiatan) }}"
                               required>

                        @error('nama_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- JENIS KEGIATAN --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jenis Kegiatan <span class="text-danger">*</span>
                        </label>

                        <select name="jenis_kegiatan"
                                class="form-select rounded-3 @error('jenis_kegiatan') is-invalid @enderror"
                                required>

                            <option value="">-- Pilih Jenis --</option>

                            <option value="Posbindu PTM"
                                {{ old('jenis_kegiatan',$kegiatan->jenis_kegiatan) == 'Posbindu PTM' ? 'selected':'' }}>
                                Posbindu PTM
                            </option>

                            <option value="Skrining PTM"
                                {{ old('jenis_kegiatan',$kegiatan->jenis_kegiatan) == 'Skrining PTM' ? 'selected':'' }}>
                                Skrining PTM
                            </option>

                            <option value="Penyuluhan"
                                {{ old('jenis_kegiatan',$kegiatan->jenis_kegiatan) == 'Penyuluhan' ? 'selected':'' }}>
                                Penyuluhan
                            </option>

                            <option value="Pemeriksaan PTM"
                                {{ old('jenis_kegiatan',$kegiatan->jenis_kegiatan) == 'Pemeriksaan PTM' ? 'selected':'' }}>
                                Pemeriksaan PTM
                            </option>

                        </select>

                        @error('jenis_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Tanggal Kegiatan <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="tanggal"
                               class="form-control rounded-3 @error('tanggal') is-invalid @enderror"
                               value="{{ old('tanggal',optional($kegiatan->tanggal)->format('Y-m-d')) }}"
                               required>

                        @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- JUMLAH PESERTA --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Jumlah Peserta
                        </label>

                        <input type="number"
                               name="jumlah_peserta"
                               class="form-control rounded-3 @error('jumlah_peserta') is-invalid @enderror"
                               value="{{ old('jumlah_peserta',$kegiatan->jumlah_peserta) }}">

                        @error('jumlah_peserta')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- LOKASI --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Lokasi Kegiatan <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="lokasi"
                               class="form-control rounded-3 @error('lokasi') is-invalid @enderror"
                               value="{{ old('lokasi',$kegiatan->lokasi) }}"
                               required>

                        @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea name="keterangan"
                                  rows="3"
                                  class="form-control rounded-3 @error('keterangan') is-invalid @enderror">{{ old('keterangan',$kegiatan->keterangan) }}</textarea>

                        @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- ================= ACTION ================= --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route('petugas.kegiatan.index') }}"
                       class="btn btn-outline-secondary rounded-pill px-4">

                        <i class="bi bi-x-circle"></i> Batal

                    </a>

                    <button type="submit"
                            class="btn btn-success rounded-pill px-4 shadow-sm">

                        <i class="bi bi-save"></i> Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

<style>
body{
    background-color:#f8fafc;
}

.form-label{
    font-size:.9rem;
}
</style>

@endsection