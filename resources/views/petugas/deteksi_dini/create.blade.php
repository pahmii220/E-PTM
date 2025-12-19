@extends('layouts.master')

@section('title', 'Tambah Deteksi Dini PTM')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Tambah Deteksi Dini PTM</h4>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('petugas.deteksi_dini.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- Pasien --}}
                        <div class="row g-3">

                            {{-- PASIEN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Peserta</label>
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
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Puskesmas</label>
                                <select name="puskesmas_id" class="form-select @error('puskesmas_id') is-invalid @enderror" required>
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

                        </div>


                        {{-- Tanggal --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan"
                                class="form-control @error('tanggal_pemeriksaan') is-invalid @enderror"
                                value="{{ old('tanggal_pemeriksaan') }}" required>
                            @error('tanggal_pemeriksaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tekanan Darah --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="120/80"
                                value="{{ old('tekanan_darah') }}">
                        </div>

                        {{-- Gula Darah --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.1" name="gula_darah" class="form-control" placeholder="Contoh: 110"
                                value="{{ old('gula_darah') }}">
                        </div>

                        {{-- Kolesterol --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.1" name="kolesterol" class="form-control" placeholder="Contoh: 190"
                                value="{{ old('kolesterol') }}">
                        </div>

                        {{-- Berat Badan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" id="berat_badan"
                                class="form-control @error('berat_badan') is-invalid @enderror" placeholder="Contoh: 60"
                                value="{{ old('berat_badan') }}" required>
                            @error('berat_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tinggi Badan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                                class="form-control @error('tinggi_badan') is-invalid @enderror" placeholder="Contoh: 165"
                                value="{{ old('tinggi_badan') }}" required>
                            @error('tinggi_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 text-end">
                        <a href="{{ route('petugas.deteksi_dini.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview IMT (opsional, tidak disimpan) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const berat = document.getElementById('berat_badan');
            const tinggi = document.getElementById('tinggi_badan');

            function previewIMT() {
                if (berat.value && tinggi.value) {
                    const imt = berat.value / Math.pow(tinggi.value / 100, 2);
                    console.log('IMT preview:', imt.toFixed(1));
                }
            }

            berat.addEventListener('input', previewIMT);
            tinggi.addEventListener('input', previewIMT);
        });
    </script>
@endsection