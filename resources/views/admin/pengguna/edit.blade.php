@extends('layouts.master')

@section('title', 'Edit Pegawai Dinas Kesehatan')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div class="header-text">
                    <h1>Edit Data Pegawai</h1>
                    <p>Perbarui informasi identitas, lokasi penugasan, dan foto profil pegawai.</p>
                </div>
            </div>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- ================= ALERT ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-modern shadow-sm">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <div class="ms-2">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="row g-4">
            {{-- KIRI: DATA IDENTITAS & LOKASI --}}
            <div class="col-lg-10 mx-auto">
                <div class="data-card p-4">
                    <div class="section-title mb-4">
                        <i class="bi bi-card-text text-primary"></i>
                        <span>Informasi Identitas & Lokasi</span>
                    </div>

                    <form action="{{ route('admin.pengguna.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="Nama_Lengkap" class="form-control" value="{{ old('Nama_Lengkap', $pegawai->nama_pegawai) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" value="{{ old('nip', $pegawai->nip) }}" placeholder="Contoh: 198504122010011005">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $pegawai->telepon) }}" placeholder="Contoh: 081234567890">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan</label>
                                @php
                                    $jabatanList = [
                                        'Kepala Seksi P2PTM',
                                        'Epidemiolog Kesehatan Ahli Pertama',
                                        'Penata Layanan Operasional',
                                        'Pengelola Data PTM',
                                        'Penelaah Teknis Kebijakan',
                                        'Administrator Kesehatan Ahli Pertama',
                                        'Pengadministrasi Perkantoran',
                                    ];
                                    $currentJabatan = old('jabatan', $pegawai->jabatan ?? '');
                                @endphp
                                <select name="jabatan" class="form-select">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($jabatanList as $j)
                                        <option value="{{ $j }}" {{ $currentJabatan == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                    {{-- Jika jabatan lama tidak ada di list, tampilkan sebagai opsi terpilih --}}
                                    @if($currentJabatan && !in_array($currentJabatan, $jabatanList))
                                        <option value="{{ $currentJabatan }}" selected>{{ $currentJabatan }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pangkat / Golongan</label>
                                @php
                                    $golonganList = [
                                        'IX',
                                        'Penata (III/c)',
                                        'Pembina (IV/a)',
                                        'Penata Muda Tk.1 (III/b)',
                                    ];
                                    $currentGolongan = old('golongan', $pegawai->golongan ?? '');
                                @endphp
                                <select name="golongan" class="form-select">
                                    <option value="">-- Pilih Golongan --</option>
                                    @foreach($golonganList as $g)
                                        <option value="{{ $g }}" {{ $currentGolongan == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                    @if($currentGolongan && !in_array($currentGolongan, $golonganList))
                                        <option value="{{ $currentGolongan }}" selected>{{ $currentGolongan }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Bidang / Subdit</label>
                                <input type="text" name="bidang" class="form-control" value="{{ old('bidang', $pegawai->bidang ?? 'P2PTM') }}">
                            </div>

                            {{-- WILAYAH KERJA BARU --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $pegawai->provinsi ?? 'Kalimantan Selatan') }}" placeholder="Masukkan Provinsi">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kabupaten / Kota</label>
                                <input type="text" name="kabupaten_kota" class="form-control" value="{{ old('kabupaten_kota', $pegawai->kabupaten_kota ?? '') }}" placeholder="Masukkan Kabupaten/Kota">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', optional($pegawai->user)->email ?? '') }}" placeholder="email@contoh.com">
                                <small class="text-muted">Alamat email pengguna/sistem.</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap tempat tinggal">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn-action-primary px-5">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }

        .page-header { display: flex; align-items: center; gap: 16px; }
        .header-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #fff; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; box-shadow: 0 4px 12px rgba(67, 56, 202, 0.2);
        }
        .header-text h1 { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .header-text p { font-size: 14px; color: #64748b; margin: 0; }

        .data-card {
            background: #ffffff; border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04); border: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 18px; font-weight: 700; color: #0f172a;
            display: flex; align-items: center; gap: 10px;
        }

        .form-control, .form-select {
            padding: 12px 14px; border-radius: 10px;
            border: 1.5px solid #e2e8f0; font-size: 14.5px;
        }
        .form-control:focus, .form-select:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }

        .btn-action-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white; border: none; padding: 12px 24px; border-radius: 12px;
            font-weight: 600; transition: all 0.2s;
        }
        .btn-action-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3); }

        .alert-modern { display: flex; align-items: flex-start; padding: 16px; border-radius: 16px; border: none; }
        .alert-danger { background-color: #fef2f2; color: #b91c1c; border-left: 5px solid #ef4444; }
    </style>
@endsection