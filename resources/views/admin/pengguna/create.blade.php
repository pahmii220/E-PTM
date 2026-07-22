@extends('layouts.master')

@section('title', 'Tambah Pegawai Dinas Kesehatan')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="header-text">
                    <h1>Tambah Pegawai Baru</h1>
                    <p>Daftarkan akun dasar pegawai. Kelengkapan profil dapat diisi mandiri oleh pegawai nanti.</p>
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

        <form action="{{ route('admin.pengguna.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4 justify-content-center">

                {{-- KIRI: IDENTITAS DASAR --}}
                <div class="col-lg-10 mx-auto">
                    <div class="data-card p-4">
                        <div class="section-title mb-4">
                            <i class="bi bi-person-vcard text-primary"></i>
                            <span>Identitas & Profil Lengkap Pegawai</span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="Nama_Lengkap" class="form-control"
                                    placeholder="Contoh: Budi Santoso, S.KM" value="{{ old('Nama_Lengkap') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Induk Pegawai (NIP)</label>
                                <input type="text" name="nip" class="form-control" placeholder="Contoh: 198504122010011005"
                                    value="{{ old('nip') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="form-control" placeholder="Contoh: 081234567890" value="{{ old('telepon') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan</label>
                                <select name="jabatan" class="form-select">
                                    <option value="">-- Pilih Jabatan --</option>
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
                                    @endphp
                                    @foreach($jabatanList as $j)
                                        <option value="{{ $j }}" {{ old('jabatan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pangkat / Golongan</label>
                                <select name="golongan" class="form-select">
                                    <option value="">-- Pilih Golongan --</option>
                                    @php
                                        $golonganList = [
                                            'IX',
                                            'Penata (III/c)',
                                            'Pembina (IV/a)',
                                            'Penata Muda Tk.1 (III/b)',
                                        ];
                                    @endphp
                                    @foreach($golonganList as $g)
                                        <option value="{{ $g }}" {{ old('golongan') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Bidang / Subdit</label>
                                <input type="text" name="bidang" class="form-control" placeholder="Contoh: P2PTM" value="{{ old('bidang') ?? 'P2PTM' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" placeholder="Contoh: Kalimantan Selatan" value="{{ old('provinsi') ?? 'Kalimantan Selatan' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kabupaten / Kota</label>
                                <input type="text" name="kabupaten_kota" class="form-control" placeholder="Contoh: Banjarmasin" value="{{ old('kabupaten_kota') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Email</label>
                                <input type="email" name="email" class="form-control" placeholder="email@contoh.com"
                                    value="{{ old('email') }}">
                                <small class="text-muted">Digunakan untuk notifikasi email sistem.</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap Tempat Tinggal</label>
                                <textarea name="alamat" class="form-control" rows="3" placeholder="Contoh: Jl. Ahmad Yani No. 12, Kel. Pemurus Luar, Kec. Banjarmasin Timur">{{ old('alamat') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Foto Profil (Opsional)</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Format gambar (JPG, PNG, WEBP), Maksimal 2MB.</small>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn-action-primary px-5 py-2">
                                <i class="bi bi-person-check-fill me-2"></i> Daftarkan Profil Pegawai
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.2);
        }

        .header-text h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .header-text p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .data-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control,
        .input-group-text {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 14.5px;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .alert-modern {
            display: flex;
            align-items: flex-start;
            padding: 16px;
            border-radius: 16px;
            border: none;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 5px solid #ef4444;
        }

        #togglePassword:hover i {
            color: #4f46e5 !important;
        }

        .input-group:focus-within .form-control {
            border-color: #6366f1;
        }

        .input-group:focus-within .input-group-text {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
    </style>

    {{-- ================= SCRIPT ================= --}}
    <script>
        // Script untuk Toggle Show/Hide Password
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    </script>
@endsection