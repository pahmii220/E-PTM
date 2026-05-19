@extends('layouts.master')

@section('title', 'Tambah Data Kegiatan PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Tambah Data Kegiatan PTM</h4>
                <small class="opacity-75">
                    Isi data kegiatan PTM dengan lengkap sebelum disimpan
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('petugas.kegiatan.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- NAMA KEGIATAN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nama Kegiatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_kegiatan" class="form-control rounded-3"
                                placeholder="Contoh: Posbindu PTM" required>
                        </div>

                        {{-- JENIS KEGIATAN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jenis Kegiatan <span class="text-danger">*</span>
                            </label>

                            <select name="jenis_kegiatan" class="form-select rounded-3" required>

                                <option value="">-- Pilih Jenis --</option>
                                <option value="Posbindu PTM">Posbindu PTM</option>
                                <option value="Skrining PTM">Skrining PTM</option>
                                <option value="Penyuluhan">Penyuluhan</option>
                                <option value="Pemeriksaan PTM">Pemeriksaan PTM</option>

                            </select>
                        </div>

                        {{-- TANGGAL KEGIATAN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Kegiatan <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="tanggal" class="form-control rounded-3" required>
                        </div>

                        {{-- JUMLAH PESERTA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jumlah Peserta
                            </label>

                            <input type="number" name="jumlah_peserta" class="form-control rounded-3"
                                placeholder="Contoh: 30">
                        </div>

                        {{-- LOKASI --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Lokasi Kegiatan <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="lokasi" class="form-control rounded-3"
                                placeholder="Contoh: Desa Sungai Andai" required>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Keterangan
                            </label>

                            <textarea name="keterangan" rows="3" class="form-control rounded-3"
                                placeholder="Catatan tambahan kegiatan"></textarea>
                        </div>

                    </div>

                    {{-- ================= ACTION ================= --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a href="{{ route('petugas.kegiatan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">

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