@extends('layouts.master')

@section('title', 'Tambah Faktor Risiko PTM')

@section('content')
    <div class="container py-4 px-4" style="max-width: 900px; margin: auto;">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white fw-semibold">
                Tambah Data Faktor Risiko PTM
            </div>

            <div class="card-body p-4">
                <form action="{{ route('petugas.faktor_resiko.store') }}" method="POST">
                    @csrf

                    {{-- PASIEN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Peserta</label>
                        <select name="pasien_id" class="form-select @error('pasien_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($pasien as $p)
                                <option value="{{ $p->id }}" {{ old('pasien_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('pasien_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PUSKESMAS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Puskesmas</label>
                        <select name="puskesmas_id" class="form-select @error('puskesmas_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Puskesmas --</option>
                            @foreach($puskesmas as $pkm)
                                <option value="{{ $pkm->id }}" {{ old('puskesmas_id') == $pkm->id ? 'selected' : '' }}>
                                    {{ $pkm->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>
                        @error('puskesmas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan"
                            class="form-control @error('tanggal_pemeriksaan') is-invalid @enderror"
                            value="{{ old('tanggal_pemeriksaan') }}" required>
                        @error('tanggal_pemeriksaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- FAKTOR RISIKO --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Merokok</label>
                            <select name="merokok" class="form-select" required>
                                <option value="Tidak" {{ old('merokok') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ old('merokok') == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Alkohol</label>
                            <select name="alkohol" class="form-select" required>
                                <option value="Tidak" {{ old('alkohol') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ old('alkohol') == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kurang Aktivitas Fisik</label>
                            <select name="kurang_aktivitas_fisik" class="form-select" required>
                                <option value="Tidak" {{ old('kurang_aktivitas_fisik') == 'Tidak' ? 'selected' : '' }}>Tidak
                                </option>
                                <option value="Ya" {{ old('kurang_aktivitas_fisik') == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                    </div>

                    {{-- TOMBOL --}}
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('petugas.faktor_resiko.index') }}" class="btn btn-secondary me-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection