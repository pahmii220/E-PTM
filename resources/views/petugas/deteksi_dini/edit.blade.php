@extends('layouts.master')

@section('title', 'Edit Deteksi Dini PTM')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
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
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Peserta<</label>
                            <select name="pasien_id" class="form-select @error('pasien_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Peserta< --</option>
                                @foreach($pasien as $p)
                                    <option value="{{ $p->id }}" {{ old('pasien_id', $deteksi->pasien_id) == $p->id ? 'selected' : '' }}>
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
                                    <option value="{{ $pkm->id }}" {{ old('puskesmas_id', $deteksi->puskesmas_id) == $pkm->id ? 'selected' : '' }}>
                                        {{ $pkm->nama_puskesmas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('puskesmas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        {{-- Tanggal Pemeriksaan --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control @error('tanggal_pemeriksaan') is-invalid @enderror"
                                value="{{ old('tanggal_pemeriksaan', optional($deteksi->tanggal_pemeriksaan)->format('Y-m-d')) }}" required>
                            @error('tanggal_pemeriksaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tekanan Darah --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" class="form-control @error('tekanan_darah') is-invalid @enderror"
                                placeholder="120/80" value="{{ old('tekanan_darah', $deteksi->tekanan_darah) }}" required>
                            @error('tekanan_darah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Gula Darah --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.1" name="gula_darah" class="form-control @error('gula_darah') is-invalid @enderror"
                                placeholder="Contoh: 110" value="{{ old('gula_darah', $deteksi->gula_darah) }}">
                            @error('gula_darah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Kolesterol --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.1" name="kolesterol" class="form-control @error('kolesterol') is-invalid @enderror"
                                placeholder="Contoh: 190" value="{{ old('kolesterol', $deteksi->kolesterol) }}">
                            @error('kolesterol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Berat --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Badan (kg)</label>
                            <input type="number" step="0.01" name="berat_badan" id="berat_badan" class="form-control @error('berat_badan') is-invalid @enderror"
                                placeholder="Contoh: 60" value="{{ old('berat_badan', $deteksi->berat_badan) }}">
                            @error('berat_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tinggi --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan" class="form-control @error('tinggi_badan') is-invalid @enderror"
                                placeholder="Contoh: 165" value="{{ old('tinggi_badan', $deteksi->tinggi_badan) }}">
                            @error('tinggi_badan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- IMT (otomatis) --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">IMT (kg/m²)</label>
                            <input type="text" name="imt" id="imt" class="form-control" readonly value="{{ old('imt', $deteksi->imt) }}">
                        </div>

                        {{-- Hasil Skrining (readonly) --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Hasil Skrining</label>
                            <input type="text" name="hasil_skrining" class="form-control" readonly value="{{ old('hasil_skrining', $deteksi->hasil_skrining) }}">
                            <small class="text-muted">Hasil skrining dihitung otomatis (server). Untuk memperbarui hasil, lakukan verifikasi / proses skrining di modul yang sesuai.</small>
                        </div>
                    </div>

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

    {{-- Hitung IMT otomatis di client dan set value field imt --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const beratInput = document.getElementById('berat_badan');
        const tinggiInput = document.getElementById('tinggi_badan');
        const imtInput = document.getElementById('imt');

        function hitungIMT() {
            const berat = parseFloat(beratInput.value);
            const tinggi = parseFloat(tinggiInput.value) / 100;
            if (!isNaN(berat) && !isNaN(tinggi) && tinggi > 0) {
                const imt = berat / (tinggi * tinggi);
                imtInput.value = imt.toFixed(2);
            } else {
                imtInput.value = '';
            }
        }

        if (beratInput) beratInput.addEventListener('input', hitungIMT);
        if (tinggiInput) tinggiInput.addEventListener('input', hitungIMT);
    });
    </script>

@endsection
