@extends('layouts.master')

@section('title', 'Tambah Data Pasien')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <div class="card-body text-white">
                <h4 class="fw-bold mb-0">Tambah Data Peserta</h4>
                <small class="opacity-75">
                    Lengkapi data peserta dengan benar sebelum disimpan
                </small>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <form action="{{ route('petugas.pasien.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" class="form-control rounded-3"
                                placeholder="Nama lengkap peserta" required>
                        </div>

                        {{-- NO RM --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nomor Rekam Medis <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="no_rekam_medis" class="form-control rounded-3"
                                placeholder="Contoh: RM-001" required>
                        </div>

                        {{-- TANGGAL LAHIR --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Lahir <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3" required>
                        </div>

                        {{-- JENIS KELAMIN --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_kelamin" class="form-select rounded-3" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        {{-- ALAMAT --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Alamat <span class="text-danger">*</span>
                            </label>
                            <textarea name="alamat" rows="3" class="form-control rounded-3"
                                placeholder="Alamat lengkap peserta" required></textarea>
                        </div>

                        {{-- KONTAK --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nomor Kontak / HP <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kontak" class="form-control rounded-3" placeholder="08xxxxxxxxxx"
                                required>
                        </div>

                        {{-- PUSKESMAS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Puskesmas <span class="text-danger">*</span>
                            </label>

                            @if(auth()->user()->role_name === 'admin')
                                <select name="puskesmas_id" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Puskesmas --</option>
                                    @foreach($puskesmas as $pkm)
                                        <option value="{{ $pkm->id }}">
                                            {{ $pkm->nama_puskesmas }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control rounded-3 bg-light"
                                    value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
                                <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                            @endif
                        </div>

                    </div>

                    {{-- ================= ACTION ================= --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('petugas.pasien.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
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