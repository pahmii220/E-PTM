@extends('layouts.master')

@section('title', 'Edit Faktor Risiko PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1000px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Edit Faktor Risiko PTM</h4>
                <small class="opacity-75">
                    Perbarui data faktor risiko peserta
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                {{-- GLOBAL ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('petugas.faktor_resiko.update', $faktor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- PASIEN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Peserta <span class="text-danger">*</span>
                        </label>
                        <select name="pasien_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($pasien as $p)
                                <option value="{{ $p->id }}" {{ old('pasien_id', $faktor->pasien_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PUSKESMAS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Puskesmas <span class="text-danger">*</span>
                        </label>

                        @if(auth()->user()->role_name === 'admin')
                            <select name="puskesmas_id"
                                class="form-select rounded-3 @error('puskesmas_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Puskesmas --</option>
                                @foreach($puskesmas as $pkm)
                                    <option value="{{ $pkm->id }}" {{ old('puskesmas_id', $faktor->puskesmas_id) == $pkm->id ? 'selected' : '' }}>
                                        {{ $pkm->nama_puskesmas }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control rounded-3 bg-light"
                                value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
                            <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                        @endif

                        @error('puskesmas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Tanggal Pemeriksaan <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control rounded-3"
                            value="{{ old('tanggal_pemeriksaan', optional($faktor->tanggal_pemeriksaan)->format('Y-m-d')) }}"
                            required>
                    </div>

                    {{-- FAKTOR RISIKO --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Merokok</label>
                            <select name="merokok" class="form-select rounded-3" required>
                                <option value="Tidak" {{ $faktor->merokok == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ $faktor->merokok == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Konsumsi Alkohol</label>
                            <select name="alkohol" class="form-select rounded-3" required>
                                <option value="Tidak" {{ $faktor->alkohol == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ $faktor->alkohol == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kurang Aktivitas Fisik</label>
                            <select name="kurang_aktivitas_fisik" class="form-select rounded-3" required>
                                <option value="Tidak" {{ $faktor->kurang_aktivitas_fisik == 'Tidak' ? 'selected' : '' }}>Tidak
                                </option>
                                <option value="Ya" {{ $faktor->kurang_aktivitas_fisik == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('petugas.faktor_resiko.index') }}"
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