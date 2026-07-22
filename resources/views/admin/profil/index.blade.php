@extends('layouts.master')

@section('title', 'Profil Administrator')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <div class="container" style="max-width: 1000px;">
        
        {{-- ================= HEADER ================= --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Profil Administrator</h3>
                <p class="text-muted mb-0">Kelola identitas utama, kredensial login, dan pengaturan email sistem.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="btn btn-light bg-white border shadow-sm rounded-pill px-4 fw-medium hover-lift">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- ================= ALERTS ================= --}}
        @if(session('success'))
            <div class="alert alert-success shadow-sm rounded-4 border-0 d-flex align-items-center mb-4 p-3 bg-white border-start border-success border-4">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-success mb-0">Berhasil!</h6>
                    <span class="text-muted small">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-4 border-0 mb-4 bg-white border-start border-danger border-4 p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                    <h6 class="fw-bold text-danger mb-0">Gagal Menyimpan</h6>
                </div>
                <ul class="text-muted small mb-0 ps-4">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            {{-- ================= KARTU IDENTITAS (KIRI) ================= --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 text-center overflow-hidden">
                    <div class="bg-primary" style="height: 100px; background: linear-gradient(135deg, #4f46e5, #3b82f6);"></div>
                    <div class="card-body px-4 pb-5" style="margin-top: -50px;">
                        <div class="bg-white rounded-circle d-inline-flex justify-content-center align-items-center shadow-sm border border-4 border-white mb-3"
                            style="width: 100px; height: 100px;">
                            <img src="{{ asset('images/avatar.jpg') }}" alt="Admin Avatar" class="img-fluid rounded-circle" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->Nama_Lengkap) }}&background=e0e7ff&color=4f46e5&size=128'">
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $user->Nama_Lengkap }}</h4>
                        <p class="text-muted small mb-3">Super Administrator Sistem E-PTM</p>
                        
                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-success-subtle text-success fw-bold small">
                            <i class="bi bi-shield-fill-check me-2"></i> Akses Penuh
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= FORM PROFIL (KANAN) ================= --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                            <div class="bg-primary-subtle text-primary rounded p-2 d-flex"><i class="bi bi-person-lines-fill"></i></div>
                            Informasi Profil
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('admin.profil.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider">Nama Lengkap</label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                        <input type="text" name="Nama_Lengkap" class="form-control border-start-0 ps-0"
                                            value="{{ old('Nama_Lengkap', $user->Nama_Lengkap) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider mt-3">Username</label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" name="username" class="form-control border-start-0 ps-0"
                                            value="{{ old('username', $user->Username) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider mt-3">Email Bantuan (FAQ)</label>
                                    <div class="input-group input-group-flat shadow-sm rounded-3">
                                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0"
                                            value="{{ old('email', $user->email) }}" placeholder="admin@dinkes.go.id" required>
                                    </div>
                                    <div class="form-text small mt-1">Email ini menerima laporan dari Puskesmas.</div>
                                </div>
                            </div>
                            
                            <hr class="my-4 text-muted">
                            
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-warning me-2"></i> Keamanan (Ganti Password)</h6>
                            <p class="text-muted small mb-3">Kosongkan jika Anda tidak ingin mengganti password.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider">Password Lama</label>
                                    <input type="password" name="password_lama" class="form-control shadow-sm rounded-3" placeholder="Ketik password lama">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider">Password Baru</label>
                                    <input type="password" name="password_baru" class="form-control shadow-sm rounded-3" placeholder="Minimal 8 karakter">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark small text-uppercase tracking-wider">Konfirmasi Password</label>
                                    <input type="password" name="password_baru_confirmation" class="form-control shadow-sm rounded-3" placeholder="Ulangi password baru">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-5 py-2 shadow-sm hover-lift">
                                    <i class="bi bi-save me-1"></i> Simpan Profil
                                </button>
                            </div>
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
    .tracking-wider { letter-spacing: 0.05em; }
    .input-group-flat .form-control:focus { border-color: #dee2e6; box-shadow: none; }
    .input-group-flat:focus-within { box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25) !important; border-radius: 0.5rem; }
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
</style>
@endpush
