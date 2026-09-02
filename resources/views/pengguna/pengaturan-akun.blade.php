@extends('layouts.master')

@section('title', 'Pengaturan Akun')


@section('content')
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        <div class="container" style="max-width: 950px;">

            {{-- ================= HEADER HALAMAN ================= --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Pengaturan Akun</h3>
                    <p class="text-muted mb-0">Kelola keamanan, username, dan kata sandi akun Anda.</p>
                </div>
                <a href="{{ route('pengguna.dashboard') }}"
                    class="btn btn-light bg-white border shadow-sm rounded-pill px-4 fw-medium hover-lift">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>


            @if ($errors->any())
                <div
                    class="alert alert-danger shadow-sm rounded-4 border-0 mb-4 bg-white border-start border-danger border-4 p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                        <h6 class="fw-bold text-danger mb-0">Terdapat Kesalahan Input</h6>
                    </div>
                    <ul class="text-muted small mb-0 ps-4">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ================= INFO AKUN PANEL ================= --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-3 border-end bg-light p-4 text-center d-flex flex-column justify-content-center">
                            <i class="bi bi-shield-lock-fill text-primary mb-2" style="font-size: 2.5rem;"></i>
                            <h6 class="fw-bold text-dark mb-0">Keamanan Akun</h6>
                        </div>
                        <div class="col-md-9 p-4">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <p class="text-muted small text-uppercase fw-bold tracking-wider mb-1">
                                        Username Saat Ini
                                    </p>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded p-1 d-flex">
                                            <i class="bi bi-person-badge"></i>
                                        </div>

                                        {{-- Ganti 'username' menjadi 'Username' (U besar) --}}
                                        <span class="fw-bold text-dark fs-5">
                                            {{ auth()->user()->Username }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Role Akses</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-medium">
                                            <i class="bi bi-person-gear me-1"></i> {{ auth()->user()->role_name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Status Akun</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="fw-semibold text-dark">Aktif</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Status Profil</p>
                                    <div class="d-flex align-items-center gap-2">
                                        @if(auth()->user()->profilDinkesLengkap())
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span class="fw-semibold text-dark">Lengkap</span>
                                        @else
                                            <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                            <span class="fw-semibold text-dark">Belum Lengkap</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= FORM PENGATURAN ================= --}}
            <div class="row g-4">

                {{-- KARTU GANTI USERNAME --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-primary border-4 hover-lift-card">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                                <div class="bg-primary-subtle text-primary rounded p-2 d-flex"><i
                                        class="bi bi-person-vcard"></i></div>
                                Ganti Username
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('pengguna.ganti.username') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark">Username Baru <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-at"></i></span>
                                        <input type="text" name="username" class="form-control border-start-0 ps-0"
                                            placeholder="Masukkan username baru" required>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label fw-semibold text-dark">Password Saat Ini <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-key"></i></span>
                                        <input type="password" name="password" class="form-control border-start-0 ps-0"
                                            placeholder="Ketik password untuk konfirmasi" required>
                                    </div>
                                </div>

                                <button
                                    class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm hover-lift mt-auto">
                                    <i class="bi bi-save me-1"></i> Simpan Username
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- KARTU GANTI PASSWORD --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-success border-4 hover-lift-card">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                                <div class="bg-success-subtle text-success rounded p-2 d-flex"><i
                                        class="bi bi-fingerprint"></i></div>
                                Ganti Password
                            </h5>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <form method="POST" action="{{ route('pengguna.ganti.password') }}"
                                class="d-flex flex-column h-100">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Password Lama <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-unlock"></i></span>
                                        <input type="password" name="password_lama" class="form-control border-start-0 ps-0"
                                            placeholder="Masukkan password lama" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Password Baru <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-lock"></i></span>
                                        <input type="password" name="password_baru" class="form-control border-start-0 ps-0"
                                            placeholder="Buat password baru" required>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label fw-semibold text-dark">Konfirmasi Password Baru <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i
                                                class="bi bi-check2-all"></i></span>
                                        <input type="password" name="password_baru_confirmation"
                                            class="form-control border-start-0 ps-0" placeholder="Ketik ulang password baru"
                                            required>
                                    </div>
                                </div>

                                <button
                                    class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm hover-lift mb-4">
                                    <i class="bi bi-shield-lock me-1"></i> Perbarui Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Utility CSS (Sama dengan halaman Profil) */
        .tracking-wider {
            letter-spacing: 0.05em;
        }

        /* Input Group Seamless */
        .input-group-flat .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .input-group-flat:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            border-radius: 0.5rem;
        }

        /* Hover Effects */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        .hover-lift-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
    </style>
@endpush