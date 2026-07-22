@extends('layouts.master')

@section('title', 'Tambah Petugas Puskesmas')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="header-text">
                    <h1>Tambah Petugas Baru</h1>
                    <p>Daftarkan akun dasar petugas dan tentukan penempatan Puskesmas.</p>
                </div>
            </div>
            <a href="{{ route('admin.data_petugas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
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

        <form action="{{ route('admin.data_petugas.store') }}" method="POST">
            @csrf
            <div class="row g-4 justify-content-center">

                {{-- DATA PROFIL & KEPEGAWAIAN PETUGAS --}}
                <div class="col-lg-10 mx-auto">
                    <div class="data-card p-4">
                        <div class="section-title mb-4">
                            <i class="bi bi-person-vcard text-primary"></i>
                            <span>Profil & Kepegawaian Petugas</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pegawai" class="form-control"
                                    placeholder="Contoh: Siti Aminah, Amd.Kep" value="{{ old('nama_pegawai') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Induk Pegawai (NIP) / NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control" placeholder="NIP atau NIK KTP"
                                    value="{{ old('nip') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="" disabled selected>— Pilih Jenis Kelamin —</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="{{ old('tanggal_lahir') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telepon / HP <span class="text-danger">*</span></label>
                                <input type="text" name="telepon" class="form-control" placeholder="Contoh: 0812XXXXXXXX"
                                    value="{{ old('telepon') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Puskesmas Penempatan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-hospital text-danger"></i></span>
                                    <select name="puskesmas_id" class="form-select" required>
                                        <option value="" disabled selected>— Pilih Wilayah Tugas —</option>
                                        @foreach($puskesmas ?? [] as $p)
                                            <option value="{{ $p->id }}" {{ old('puskesmas_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_puskesmas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Perawat Pelaksana PTM"
                                    value="{{ old('jabatan') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Poli / Program Tugas <span class="text-danger">*</span></label>
                                <input type="text" name="bidang" class="form-control" placeholder="Contoh: Poli Umum / Program PTM"
                                    value="{{ old('bidang') }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap tempat tinggal..." required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn-action-primary px-5 py-2">
                                <i class="bi bi-person-check-fill me-2"></i> Daftarkan Profil Petugas
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
        .form-select,
        .input-group-text {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: 14.5px;
        }

        .form-control:focus,
        .form-select:focus {
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