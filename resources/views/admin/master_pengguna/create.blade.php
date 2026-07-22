@extends('layouts.master')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="container-fluid px-md-5 py-4">

    <div class="mb-4">
        <a href="{{ route('admin.master_pengguna.index') }}" class="btn btn-light border shadow-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Pengguna Baru</h4>
            <p class="text-muted small mt-1">Tambahkan akun baru untuk Admin, Kepala P2PTM, Pegawai, atau Petugas.</p>
        </div>
        
        <div class="card-body p-4">
            <form action="{{ route('admin.master_pengguna.store') }}" method="POST">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="Nama_Lengkap" class="form-control border-2 @error('Nama_Lengkap') is-invalid @enderror" value="{{ old('Nama_Lengkap') }}" required placeholder="Contoh: Budi Santoso">
                        @error('Nama_Lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="Username" class="form-control border-2 @error('Username') is-invalid @enderror" value="{{ old('Username') }}" required placeholder="Contoh: budi_s">
                        @error('Username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control border-2 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: budi@gmail.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control border-2 @error('password') is-invalid @enderror" required placeholder="Minimal 8 karakter">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">Role Akses <span class="text-danger">*</span></label>
                        <select name="role_name" id="roleSelect" class="form-select border-2 @error('role_name') is-invalid @enderror" required onchange="toggleProfileDropdowns()">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role_name') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="kepala_p2ptm" {{ old('role_name') == 'kepala_p2ptm' ? 'selected' : '' }}>Kepala P2PTM</option>
                            <option value="pegawai" {{ old('role_name') == 'pegawai' ? 'selected' : '' }}>Pegawai Dinkes</option>
                            <option value="petugas" {{ old('role_name') == 'petugas' ? 'selected' : '' }}>Petugas Puskesmas</option>
                        </select>
                        @error('role_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-muted small mt-2">
                            <i class="bi bi-info-circle-fill text-primary"></i> <b>Info:</b> Untuk role Pegawai dan Petugas, Anda dapat memilih profil yang telah dibuat sebelumnya agar otomatis tertaut ke akun ini.
                        </div>
                    </div>

                    {{-- DROPDOWN PEGAWAI --}}
                    <div class="col-md-12 d-none" id="pegawaiDropdownContainer">
                        <label class="form-label fw-bold text-success">Tautkan ke Profil Pegawai Dinkes (Opsional)</label>
                        <select name="pegawai_profile_id" id="pegawaiSelect" class="form-select border-2 border-success border-opacity-50" onchange="autoFillName(this)">
                            <option value="">-- Buat Akun Saja (Tanpa Menautkan Profil) --</option>
                            @forelse($unlinkedPegawai as $p)
                                <option value="{{ $p->id }}" data-nama="{{ $p->nama_pegawai }}">{{ $p->nama_pegawai }} (NIP: {{ $p->nip ?? '-' }})</option>
                            @empty
                                <option value="" disabled>Belum ada profil Pegawai tanpa akun.</option>
                            @endforelse
                        </select>
                        <small class="text-muted mt-1 d-block"><i class="bi bi-link-45deg me-1"></i>Pilih profil untuk menautkannya langsung dengan akun login ini.</small>
                    </div>

                    {{-- DROPDOWN PETUGAS --}}
                    <div class="col-md-12 d-none" id="petugasDropdownContainer">
                        <label class="form-label fw-bold text-success">Tautkan ke Profil Petugas PKM (Opsional)</label>
                        <select name="petugas_profile_id" id="petugasSelect" class="form-select border-2 border-success border-opacity-50" onchange="autoFillName(this)">
                            <option value="">-- Buat Akun Saja (Tanpa Menautkan Profil) --</option>
                            @forelse($unlinkedPetugas as $p)
                                <option value="{{ $p->id }}" data-nama="{{ $p->nama_pegawai }}">{{ $p->nama_pegawai }} ({{ $p->puskesmas->nama_puskesmas ?? '-' }})</option>
                            @empty
                                <option value="" disabled>Belum ada profil Petugas tanpa akun.</option>
                            @endforelse
                        </select>
                        <small class="text-muted mt-1 d-block"><i class="bi bi-link-45deg me-1"></i>Pilih profil untuk menautkannya langsung dengan akun login ini.</small>
                    </div>
                </div>

                <div class="mt-5 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" style="background-color: #4f46e5; border:none;">
                        <i class="bi bi-save me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleProfileDropdowns() {
        const role = document.getElementById('roleSelect').value;
        const pegawaiContainer = document.getElementById('pegawaiDropdownContainer');
        const petugasContainer = document.getElementById('petugasDropdownContainer');
        const pegawaiSelect = document.getElementById('pegawaiSelect');
        const petugasSelect = document.getElementById('petugasSelect');
        const nameInput = document.querySelector('input[name="Nama_Lengkap"]');

        if (role === 'pegawai') {
            pegawaiContainer.classList.remove('d-none');
            petugasContainer.classList.add('d-none');
            petugasSelect.value = '';
        } else if (role === 'petugas') {
            petugasContainer.classList.remove('d-none');
            pegawaiContainer.classList.add('d-none');
            pegawaiSelect.value = '';
        } else {
            pegawaiContainer.classList.add('d-none');
            petugasContainer.classList.add('d-none');
            pegawaiSelect.value = '';
            petugasSelect.value = '';
        }
        
        // Kembalikan field Nama Lengkap agar bisa diedit jika ganti role
        nameInput.readOnly = false;
    }

    function autoFillName(selectElement) {
        const nameInput = document.querySelector('input[name="Nama_Lengkap"]');
        
        // Jika kembali memilih opsi kosong, buka kembali fieldnya
        if (!selectElement.value) {
            nameInput.readOnly = false;
            return;
        }
        
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const nama = selectedOption.getAttribute('data-nama');
        
        if (nama) {
            nameInput.value = nama;
            nameInput.readOnly = true; // Kunci field agar tidak diedit manual
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleProfileDropdowns();
    });
</script>
@endpush
