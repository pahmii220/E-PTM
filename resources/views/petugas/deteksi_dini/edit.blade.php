@extends('layouts.master')

@section('title', 'Edit Deteksi Dini PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Edit Deteksi Dini PTM</h4>
                <small class="opacity-75">
                    Perbarui data hasil pemeriksaan peserta
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                {{-- GLOBAL ERROR --}}
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

                    <div class="row g-4">

                        {{-- PESERTA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Peserta</label>
                            <input type="text" class="form-control rounded-3 bg-light"
                                value="{{ $deteksi->pasien->nama_lengkap }}" readonly>
                        </div>

                        {{-- PUSKESMAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Puskesmas</label>

                            @if(auth()->user()->role_name === 'admin')
                                <input type="text" class="form-control rounded-3 bg-light"
                                    value="{{ $deteksi->puskesmas->nama_puskesmas ?? 'Mengikuti peserta' }}" readonly>
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
                                value="{{ old('tanggal_pemeriksaan', optional($deteksi->tanggal_pemeriksaan)->format('Y-m-d')) }}"
                                required>
                            @error('tanggal_pemeriksaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TEKANAN DARAH --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tekanan Darah (mmHg)</label>
                            <input type="text" name="tekanan_darah" class="form-control rounded-3" placeholder="120/80"
                                value="{{ old('tekanan_darah', $deteksi->tekanan_darah) }}">
                        </div>

                        {{-- GULA DARAH --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gula Darah (mg/dL)</label>
                            <input type="number" step="0.1" name="gula_darah" class="form-control rounded-3"
                                value="{{ old('gula_darah', $deteksi->gula_darah) }}">
                        </div>

                        {{-- KOLESTEROL --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kolesterol (mg/dL)</label>
                            <input type="number" step="0.1" name="kolesterol" class="form-control rounded-3"
                                value="{{ old('kolesterol', $deteksi->kolesterol) }}">
                        </div>

                        {{-- BERAT --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Berat Badan (kg) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="berat_badan" id="berat_badan"
                                class="form-control rounded-3 @error('berat_badan') is-invalid @enderror"
                                value="{{ old('berat_badan', $deteksi->berat_badan) }}" required>
                            @error('berat_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TINGGI --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Tinggi Badan (cm) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                                class="form-control rounded-3 @error('tinggi_badan') is-invalid @enderror"
                                value="{{ old('tinggi_badan', $deteksi->tinggi_badan) }}" required>
                            @error('tinggi_badan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- IMT --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">IMT (kg/m²)</label>
                            <input type="text" id="imt" class="form-control rounded-3 bg-light"
                                value="{{ old('imt', $deteksi->imt) }}" readonly>
                        </div>

                        {{-- HASIL --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Hasil Skrining</label>
                            <input type="text" class="form-control rounded-3 bg-light"
                                value="{{ old('hasil_skrining', $deteksi->hasil_skrining) }}" readonly>
                            <small class="text-muted">
                                Hasil dihitung otomatis oleh sistem
                            </small>
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.deteksi_dini.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ================= IMT AUTO ================= --}}
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

            berat?.addEventListener('input', hitungIMT);
            tinggi?.addEventListener('input', hitungIMT);
        });
    </script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection