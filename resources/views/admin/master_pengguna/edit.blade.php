@extends('layouts.master')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container-fluid px-md-5 py-4">

    <div class="mb-4">
        <a href="{{ route('admin.master_pengguna.index') }}" class="btn btn-light border shadow-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Pengguna: {{ $pengguna->Username }}</h4>
            <p class="text-muted small mt-1">Perbarui informasi akun pengguna atau reset password.</p>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('admin.master_pengguna.update', $pengguna->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="Nama_Lengkap" class="form-control border-2 @error('Nama_Lengkap') is-invalid @enderror" value="{{ old('Nama_Lengkap', $pengguna->Nama_Lengkap) }}" required>
                        @error('Nama_Lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="Username" class="form-control border-2 @error('Username') is-invalid @enderror" value="{{ old('Username', $pengguna->Username) }}" required>
                        @error('Username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control border-2 @error('email') is-invalid @enderror" value="{{ old('email', $pengguna->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Role Akses <span class="text-danger">*</span></label>
                        <select name="role_name" class="form-select border-2 @error('role_name') is-invalid @enderror" required>
                            <option value="admin" {{ (old('role_name', $pengguna->role_name) == 'admin') ? 'selected' : '' }}>Administrator</option>
                            <option value="kepala_p2ptm" {{ (old('role_name', $pengguna->role_name) == 'kepala_p2ptm') ? 'selected' : '' }}>Kepala P2PTM</option>
                            <option value="pegawai" {{ (old('role_name', $pengguna->role_name) == 'pegawai') ? 'selected' : '' }}>Pegawai Dinkes</option>
                            <option value="petugas" {{ (old('role_name', $pengguna->role_name) == 'petugas') ? 'selected' : '' }}>Petugas Puskesmas</option>
                        </select>
                        @error('role_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark"><i class="bi bi-key-fill text-warning me-2"></i>Reset Password (Opsional)</h6>
                        <p class="text-muted small mb-3">Kosongkan field ini jika Anda tidak ingin mengubah password akun ini.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="password" name="password" class="form-control border-2 @error('password') is-invalid @enderror" placeholder="Ketik password baru...">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="background-color: #4f46e5; border:none;">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
