@extends('layouts.master')

@section('title', 'Edit Faktor Risiko PTM')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Edit Faktor Risiko PTM</h4>
            </div>

            <div class="card-body">

                {{-- Error global --}}
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

                <form action="{{ route('petugas.faktor_resiko.update', $faktor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Pasien + Tanggal --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pasien</label>
                            <select name="pasien_id" class="form-select" required>
                                <option value="">-- Pilih Pasien --</option>
                                @foreach($pasien as $p)
                                    <option value="{{ $p->id }}" {{ old('pasien_id', $faktor->pasien_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal_pemeriksaan" class="form-control"
                                value="{{ old('tanggal_pemeriksaan', optional($faktor->tanggal_pemeriksaan)->format('Y-m-d')) }}"
                                required>
                        </div>
                    </div>

                    {{-- 3 Faktor Risiko --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Merokok</label>
                            <select name="merokok" class="form-select" required>
                                <option value="Tidak" {{ $faktor->merokok == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ $faktor->merokok == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Konsumsi Alkohol</label>
                            <select name="alkohol" class="form-select" required>
                                <option value="Tidak" {{ $faktor->alkohol == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ $faktor->alkohol == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kurang Aktivitas Fisik</label>
                            <select name="kurang_aktivitas_fisik" class="form-select" required>
                                <option value="Tidak" {{ $faktor->kurang_aktivitas_fisik == 'Tidak' ? 'selected' : '' }}>Tidak
                                </option>
                                <option value="Ya" {{ $faktor->kurang_aktivitas_fisik == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                    </div>

                    {{-- Puskesmas --}}
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Puskesmas</label>
                            <input type="text" name="puskesmas" class="form-control"
                                value="{{ old('puskesmas', $faktor->puskesmas) }}" required>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="text-end">
                        <a href="{{ route('petugas.faktor_resiko.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection