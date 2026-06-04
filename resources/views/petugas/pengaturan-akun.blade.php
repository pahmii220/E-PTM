@extends('layouts.master')

@section('title', 'Pengaturan Akun')

@section('content')

    <div class="container-fluid px-md-5 py-4">

        {{-- HEADER --}}
        <div class="page-header mb-5">
            <div class="header-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="header-text">
                <h1>Pengaturan Keamanan</h1>
                <p>Kelola kredensial dan tingkatkan keamanan akun Anda dengan mudah.</p>
            </div>
        </div>

        {{-- ALERT SUCCESS / ERROR --}}
        @if(session('success'))
            <div class="alert alert-success alert-modern shadow-sm">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="row g-4">

            {{-- GANTI USERNAME --}}
            <div class="col-lg-6">
                <div class="setting-card">
                    <div class="card-title">
                        <div class="icon-wrapper bg-primary-subtle text-primary">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <span>Ganti Username</span>
                    </div>

                    <form method="POST" action="{{ route('petugas.ganti.username') }}">
                        @csrf

                        <div class="form-group">
                            <label>Username Baru</label>
                            <input type="text" name="username" class="@error('username') is-invalid @enderror"
                                placeholder="Masukkan username baru" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="error-text"><i class="bi bi-info-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" name="password" class="@error('password') is-invalid @enderror"
                                placeholder="Konfirmasi Password Anda" required>
                            @error('password')
                                <div class="error-text"><i class="bi bi-info-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-action btn-primary mt-3">
                            <i class="bi bi-save me-2"></i> Simpan Username
                        </button>

                        <div class="note">
                            <i class="bi bi-info-circle"></i> Username digunakan untuk proses login ke dalam sistem.
                        </div>
                    </form>
                </div>
            </div>

            {{-- GANTI PASSWORD --}}
            <div class="col-lg-6">
                <div class="setting-card">
                    <div class="card-title">
                        <div class="icon-wrapper bg-success-subtle text-success">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <span>Ganti Password</span>
                    </div>

                    <form method="POST" action="{{ route('petugas.ganti.password') }}">
                        @csrf

                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" class="@error('password_lama') is-invalid @enderror"
                                placeholder="Masukkan password lama" required>
                            @error('password_lama')
                                <div class="error-text"><i class="bi bi-info-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="@error('password_baru') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" required>
                            @error('password_baru')
                                <div class="error-text"><i class="bi bi-info-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" placeholder="Ulangi password baru"
                                required>
                        </div>

                        <button type="submit" class="btn-action btn-success mt-3">
                            <i class="bi bi-shield-check me-2"></i> Perbarui Password
                        </button>

                        <div class="note">
                            <i class="bi bi-info-circle"></i> Gunakan kombinasi angka, huruf, dan simbol agar lebih aman.
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- STYLE --}}
    <style>
        body {
            background-color: #f8fafc;
            /* Warna latar belakang lebih lembut */
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4338ca;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.1);
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .header-text p {
            font-size: 15px;
            color: #64748b;
            margin: 0;
        }

        /* CARD */
        .setting-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .setting-card:hover {
            box-shadow: 0 10px 32px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* FORM */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 15px;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: #94a3b8;
        }

        .form-group input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .form-group input.is-invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .form-group input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .error-text {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* BUTTON */
        .btn-action {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 15px;
            color: #fff;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        /* ALERT */
        .alert-modern {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: none;
            font-weight: 500;
        }

        .alert-success.alert-modern {
            background-color: #ecfdf5;
            color: #065f46;
            border-left: 5px solid #10b981;
        }

        .alert-danger.alert-modern {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 5px solid #ef4444;
        }

        /* NOTE */
        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
            background: #f8fafc;
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>

@endsection