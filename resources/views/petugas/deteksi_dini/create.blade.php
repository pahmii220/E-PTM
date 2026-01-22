@extends('layouts.master')

@section('title', 'Tambah Deteksi Dini PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Tambah Deteksi Dini PTM</h4>
                <small class="opacity-75">
                    Input hasil pemeriksaan deteksi dini peserta
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('petugas.deteksi_dini.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        {{-- PASIEN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Peserta <span class="text-danger">*</span>
                            </label>
                            <select name="pasien_id" class="form-select rounded-3 @error('pasien_id') is-invalid @enderror"
                                required>
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

                            @if(auth()->user()->role_name === 'admin')
                                <input type="text" class="form-control rounded-3 bg-light" value="Mengikuti puskesmas peserta"
                                    readonly>
                            @else
                                <input type="text" class="form-control rounded-3 bg-light"
                                    value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
                                <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                            @endif
                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Pemeriksaan <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_pemeriksaan"
                                class="form-control rounded-3 @error('tanggal_pemeriksaan') is-invalid @enderror"
                                value="{{ old('tanggal_pemeriksaan') }}" required>
                            @error('tanggal_pemeriksaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TEKANAN DARAH --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" class="form-control rounded-3" placeholder="120/80"
                                value="{{ old('tekanan_darah') }}">
                        </div>

                        {{-- GULA DARAH --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.1" name="gula_darah" class="form-control rounded-3"
                                value="{{ old('gula_darah') }}">
                        </div>

                        {{-- KOLESTEROL --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.1" name="kolesterol" class="form-control rounded-3"
                                value="{{ old('kolesterol') }}">
                        </div>

                        {{-- BERAT BADAN --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Berat Badan (kg) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="berat_badan"
                                class="form-control rounded-3 @error('berat_badan') is-invalid @enderror"
                                value="{{ old('berat_badan') }}" required>
                            @error('berat_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TINGGI BADAN --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Tinggi Badan (cm) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.1" name="tinggi_badan"
                                class="form-control rounded-3 @error('tinggi_badan') is-invalid @enderror"
                                value="{{ old('tinggi_badan') }}" required>
                            @error('tinggi_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.deteksi_dini.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save"></i> Simpan Data
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

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection