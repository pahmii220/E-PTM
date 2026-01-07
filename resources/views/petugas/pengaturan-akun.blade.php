@extends('layouts.master')

@section('title', 'Pengaturan Akun')

@section('content')
    <div class="container py-4" style="max-width:800px">

        <div class="mb-4">
            <h3 class="fw-bold mb-1">Pengaturan Akun</h3>
            <p class="text-muted mb-0">
                Kelola keamanan akun Anda. Demi keamanan, Anda akan otomatis logout setelah perubahan akun.
            </p>
        </div>

        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">

            {{-- ================= GANTI USERNAME ================= --}}
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-person-badge me-1"></i> Ganti Username
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('petugas.ganti.username') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Username Baru</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username baru"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password saat ini"
                                    required>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan Username
                                </button>
                            </div>

                            <small class="text-muted d-block mt-2">
                                Demi keamanan, Anda akan logout otomatis setelah perubahan.
                            </small>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ================= GANTI PASSWORD ================= --}}
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-shield-lock me-1"></i> Ganti Password
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('petugas.ganti.password') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="password_lama" class="form-control" placeholder="Password lama"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password_baru" class="form-control"
                                    placeholder="Minimal 8 karakter" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_baru_confirmation" class="form-control" required>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-success">
                                    <i class="bi bi-key-fill me-1"></i> Ganti Password
                                </button>
                            </div>

                            <small class="text-muted d-block mt-2">
                                Anda akan logout otomatis setelah password diganti.
                            </small>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection