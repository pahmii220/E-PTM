@extends('layouts.master')

@section('title', 'Pengaturan Akun')

@section('content')

    <div class="container-fluid px-5 py-4">

        {{-- HEADER --}}
        <div class="page-header mb-4">
            <div class="header-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="header-text">
                <h1>Pengaturan Akun</h1>
                <p>Kelola keamanan akun Anda dengan aman dan nyaman.</p>
            </div>
        </div>

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success alert-modern">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">

            {{-- GANTI USERNAME --}}
            <div class="col-lg-6">
                <div class="setting-card">
                    <div class="card-title">
                        <i class="bi bi-person-badge-fill text-primary"></i>
                        <span>Ganti Username</span>
                    </div>

                    <form method="POST" action="{{ route('petugas.ganti.username') }}">
                        @csrf

                        <div class="form-group">
                            <label>Username Baru</label>
                            <input type="text" name="username" placeholder="Masukkan username baru" required>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password" placeholder="Password saat ini" required>
                        </div>

                        <button class="btn-action btn-primary">
                            <i class="bi bi-save"></i> Simpan Username
                        </button>

                        
                    </form>
                </div>
            </div>

            {{-- GANTI PASSWORD --}}
            <div class="col-lg-6">
                <div class="setting-card">
                    <div class="card-title">
                        <i class="bi bi-key-fill text-success"></i>
                        <span>Ganti Password</span>
                    </div>

                    <form method="POST" action="{{ route('petugas.ganti.password') }}">
                        @csrf

                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" placeholder="Password lama" required>
                        </div>

                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" placeholder="Minimal 8 karakter" required>
                        </div>

                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" required>
                        </div>

                        <button class="btn-action btn-success">
                            <i class="bi bi-shield-check"></i> Ganti Password
                        </button>

                        
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- STYLE --}}
    <style>
        body {
            background-color: #f5f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: #eef2ff;
            color: #4338ca;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .header-text {
            max-width: 480px;
        }

        .header-text h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .header-text p {
            font-size: 14.5px;
            color: #6b7280;
            margin: 0;
            line-height: 1.6;
        }

        /* CARD */
        .setting-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
            height: 100%;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        /* FORM */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            font-size: 15px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        /* BUTTON */
        .btn-action {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 15px;
            color: #fff;
            margin-top: 10px;
        }

        .btn-primary {
            background: #4f46e5;
        }

        .btn-success {
            background: #16a34a;
        }

        .btn-action:hover {
            opacity: .92;
        }

        /* ALERT */
        .alert-modern {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }

        /* NOTE */
        .note {
            margin-top: 14px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
    </style>

@endsection