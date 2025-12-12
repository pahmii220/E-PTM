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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Pasien</label>
                            <select name="pasien_id" class="form-select" required>
                                <option value="">-- Pilih Pasien --</option>
                                @foreach($pasien as $p)
                                    <option value="{{ $p->id }}" {{ old('pasien_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control"
                                value="{{ old('tanggal_pemeriksaan') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" class="form-control" placeholder="120/80"
                                value="{{ old('tekanan_darah') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.1" name="gula_darah" class="form-control" placeholder="Contoh: 110"
                                value="{{ old('gula_darah') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.1" name="kolesterol" class="form-control" placeholder="Contoh: 190"
                                value="{{ old('kolesterol') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" id="berat_badan" class="form-control"
                                placeholder="Contoh: 60" value="{{ old('berat_badan') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" class="form-control"
                                placeholder="Contoh: 165" value="{{ old('tinggi_badan') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Puskesmas</label>
                            <input type="text" name="puskesmas" class="form-control" placeholder="Nama Puskesmas"
                                value="{{ old('puskesmas') }}" required>
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

    {{-- Script Hitung IMT Otomatis (hanya preview; tidak wajib) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const beratInput = document.getElementById('berat_badan');
            const tinggiInput = document.getElementById('tinggi_badan');

            function hitungIMT() {
                const berat = parseFloat(beratInput.value);
                const tinggi = parseFloat(tinggiInput.value) / 100; // ubah cm ke meter
                // hanya ubah preview console, jangan akses element yang tidak ada
                if (berat > 0 && tinggi > 0) {
                    const imt = berat / (tinggi * tinggi);
                    console.log('IMT preview:', imt.toFixed(1));
                }
            }

            if (beratInput) beratInput.addEventListener('input', hitungIMT);
            if (tinggiInput) tinggiInput.addEventListener('input', hitungIMT);
        });
    </script>
@endsection