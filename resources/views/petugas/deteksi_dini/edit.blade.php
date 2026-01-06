@extends('layouts.master')

@section('title', 'Edit Deteksi Dini PTM')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Edit Deteksi Dini PTM</h4>
        </div>

        <div class="card-body p-4">
            {{-- Global errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('petugas.deteksi_dini.update', $deteksi->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- PASIEN --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Peserta</label>
                        <input type="text" class="form-control"
                            value="{{ $deteksi->pasien->nama_lengkap }}" readonly>
                    </div>

                    {{-- PUSKESMAS --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Puskesmas</label>

                        @if(auth()->user()->role_name === 'admin')
                            {{-- ADMIN: mengikuti puskesmas peserta --}}
                            <input type="text" class="form-control"
                                value="{{ $deteksi->puskesmas->nama_puskesmas ?? 'Mengikuti peserta' }}" readonly>
                        @else
                            {{-- PETUGAS: TERKUNCI --}}
                            <input type="text" class="form-control"
                                value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
                            <input type="hidden" name="puskesmas_id"
                                value="{{ auth()->user()->petugas->puskesmas_id }}">
                        @endif
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan"
                            class="form-control @error('tanggal_pemeriksaan') is-invalid @enderror"
                            value="{{ old('tanggal_pemeriksaan', optional($deteksi->tanggal_pemeriksaan)->format('Y-m-d')) }}"
                            required>
                        @error('tanggal_pemeriksaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tekanan Darah --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                        <input type="text" name="tekanan_darah"
                            class="form-control @error('tekanan_darah') is-invalid @enderror"
                            placeholder="120/80"
                            value="{{ old('tekanan_darah', $deteksi->tekanan_darah) }}">
                        @error('tekanan_darah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Gula Darah --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                        <input type="number" step="0.1" name="gula_darah"
                            class="form-control @error('gula_darah') is-invalid @enderror"
                            value="{{ old('gula_darah', $deteksi->gula_darah) }}">
                        @error('gula_darah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kolesterol --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                        <input type="number" step="0.1" name="kolesterol"
                            class="form-control @error('kolesterol') is-invalid @enderror"
                            value="{{ old('kolesterol', $deteksi->kolesterol) }}">
                        @error('kolesterol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Berat --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Berat Badan (kg)</label>
                        <input type="number" step="0.01" name="berat_badan" id="berat_badan"
                            class="form-control @error('berat_badan') is-invalid @enderror"
                            value="{{ old('berat_badan', $deteksi->berat_badan) }}">
                        @error('berat_badan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tinggi --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                            class="form-control @error('tinggi_badan') is-invalid @enderror"
                            value="{{ old('tinggi_badan', $deteksi->tinggi_badan) }}">
                        @error('tinggi_badan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- IMT --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">IMT (kg/m²)</label>
                        <input type="text" id="imt" class="form-control" readonly
                            value="{{ old('imt', $deteksi->imt) }}">
                    </div>

                    {{-- Hasil --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Hasil Skrining</label>
                        <input type="text" class="form-control" readonly
                            value="{{ old('hasil_skrining', $deteksi->hasil_skrining) }}">
                        <small class="text-muted">
                            Hasil dihitung otomatis oleh sistem.
                        </small>
                    </div>

                </div>

                {{-- TOMBOL --}}
                <div class="mt-4 text-end">
                    <a href="{{ route('petugas.deteksi_dini.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hitung IMT otomatis --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const berat = document.getElementById('berat_badan');
    const tinggi = document.getElementById('tinggi_badan');
    const imt = document.getElementById('imt');

    function hitungIMT() {
        const b = parseFloat(berat.value);
        const t = parseFloat(tinggi.value) / 100;
        if (!isNaN(b) && !isNaN(t) && t > 0) {
            imt.value = (b / (t * t)).toFixed(2);
        } else {
            imt.value = '';
        }
    }

    if (berat) berat.addEventListener('input', hitungIMT);
    if (tinggi) tinggi.addEventListener('input', hitungIMT);
});
</script>
@endsection
