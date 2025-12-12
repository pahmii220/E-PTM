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
                    <div class="mb-3">
                        <label class="form-label">Nama Pasien</label>
                        <select name="pasien_id" class="form-select" required>
                            <option value="">-- Pilih Pasien --</option>
                            @foreach($pasien as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pemeriksaan</label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Merokok</label>
                            <select name="merokok" class="form-select" required>
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alkohol</label>
                            <select name="alkohol" class="form-select" required>
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kurang Aktivitas Fisik</label>
                            <select name="kurang_aktivitas_fisik" class="form-select" required>
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Puskesmas</label>
                        <input type="text" name="puskesmas" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('petugas.faktor_resiko.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection