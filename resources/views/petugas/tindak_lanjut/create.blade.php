@extends('layouts.master')

@section('title', 'Tambah Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Tambah Tindak Lanjut PTM</h4>
                <small class="opacity-75">
                    Input tindak lanjut berdasarkan hasil skrining PTM
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                {{-- ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <strong>Periksa kembali input Anda:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('petugas.tindak_lanjut.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        {{-- DETEKSI DINI --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Peserta / Pasien
                            </label>
                            <select name="deteksi_dini_id" class="form-select rounded-3" required>
                                @foreach ($daftarDeteksi as $d)
                                    <option value="{{ $d->id }}" {{ $d->id == $deteksiTerpilih->id ? 'selected' : '' }}>
                                        {{ $d->pasien->nama_lengkap }}
                                        • {{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }}
                                        • {{ ucfirst(str_replace('_', ' ', $d->status_risiko)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Data diambil dari hasil deteksi dini PTM
                            </small>
                        </div>

                        {{-- JENIS TINDAK LANJUT --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jenis Tindak Lanjut <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_tindak_lanjut" class="form-select rounded-3" required>
                                <option value="">-- Pilih --</option>
                                <option value="edukasi">Edukasi</option>
                                <option value="anjuran_gaya_hidup">Anjuran Gaya Hidup</option>
                                <option value="rujukan">Rujukan</option>
                                <option value="monitoring">Monitoring</option>
                                <option value="tidak_ada">Tidak Ada</option>
                            </select>
                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Tindak Lanjut
                            </label>
                            <input type="date" name="tanggal_tindak_lanjut" class="form-control rounded-3"
                                value="{{ old('tanggal_tindak_lanjut') }}">
                        </div>

                        {{-- CATATAN --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Catatan Petugas
                            </label>
                            <textarea name="catatan_petugas" class="form-control rounded-3" rows="4"
                                placeholder="Catatan edukasi, anjuran, rujukan, atau keterangan tambahan...">{{ old('catatan_petugas') }}</textarea>
                        </div>

                    </div>

                    {{-- ACTION --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.deteksi_dini.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f8fafc;
        }

        .form-label {
            font-size: .9rem;
        }
    </style>
@endsection