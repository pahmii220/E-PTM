@extends('layouts.master')

@section('title', isset($logistik) ? 'Edit Data Logistik' : 'Tambah Data Logistik')

@section('content')
<div class="container-fluid py-4" style="max-width: 800px; margin: auto;">
    
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('pengguna.logistik.index') }}" class="btn btn-light border shadow-sm me-3 rounded-circle" style="width:40px; height:40px; padding:0; line-height:38px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold text-gray-800 mb-0">
                {{ isset($logistik) ? 'Edit Data Logistik PTM' : 'Tambah Data Logistik PTM' }}
            </h3>
            <p class="text-muted small mb-0">Formulir pengisian sisa stok logistik Puskesmas</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-5">
            <form action="{{ isset($logistik) ? route('pengguna.logistik.update', $logistik->id) : route('pengguna.logistik.store') }}" method="POST">
                @csrf
                @if(isset($logistik))
                    @method('PUT')
                @endif

                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-gray-700">Puskesmas <span class="text-danger">*</span></label>
                        <select name="puskesmas_id" class="form-select border-gray-300 rounded-3 shadow-sm @error('puskesmas_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Puskesmas --</option>
                            @foreach($puskesmasList as $pkm)
                                <option value="{{ $pkm->id }}" {{ (old('puskesmas_id', $logistik->puskesmas_id ?? '')) == $pkm->id ? 'selected' : '' }}>
                                    {{ $pkm->nama_puskesmas }}
                                </option>
                            @endforeach
                        </select>
                        @error('puskesmas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="text-muted mb-4">
                <h6 class="fw-bold text-gray-800 mb-3"><i class="bi bi-box-seam text-primary me-2"></i> Sisa Stok Fisik</h6>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-gray-600">Strip Gula Darah</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <input type="number" name="strip_gula" class="form-control border-gray-300 @error('strip_gula') is-invalid @enderror" value="{{ old('strip_gula', $logistik->strip_gula ?? 0) }}" min="0" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                        @error('strip_gula') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-gray-600">Strip Kolesterol</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <input type="number" name="strip_kolesterol" class="form-control border-gray-300 @error('strip_kolesterol') is-invalid @enderror" value="{{ old('strip_kolesterol', $logistik->strip_kolesterol ?? 0) }}" min="0" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-gray-600">Strip Asam Urat</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <input type="number" name="strip_asam_urat" class="form-control border-gray-300 @error('strip_asam_urat') is-invalid @enderror" value="{{ old('strip_asam_urat', $logistik->strip_asam_urat ?? 0) }}" min="0" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-gray-600">Blood Lancet (Jarum)</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <input type="number" name="lancet" class="form-control border-gray-300 @error('lancet') is-invalid @enderror" value="{{ old('lancet', $logistik->lancet ?? 0) }}" min="0" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-gray-600">Kapas Alkohol (Swab)</label>
                        <div class="input-group input-group-sm shadow-sm">
                            <input type="number" name="kapas_alkohol" class="form-control border-gray-300 @error('kapas_alkohol') is-invalid @enderror" value="{{ old('kapas_alkohol', $logistik->kapas_alkohol ?? 0) }}" min="0" required>
                            <span class="input-group-text bg-light text-muted">Pcs</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold text-gray-600">Keterangan / Catatan Tambahan (Opsional)</label>
                    <textarea name="keterangan" class="form-control border-gray-300 rounded-3 shadow-sm" rows="3" placeholder="Contoh: Stok strip gula rusak 10 pcs">{{ old('keterangan', $logistik->keterangan ?? '') }}</textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success fw-bold px-5 rounded-pill shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Data Logistik
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
