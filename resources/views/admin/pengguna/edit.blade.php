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
            <div class="col-lg-8">
                <div class="data-card p-4">
                    <div class="section-title mb-4">
                        <i class="bi bi-card-text text-primary"></i>
                        <span>Informasi Identitas & Lokasi</span>
                    </div>

                    <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- FOTO SECTION --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Foto Profil</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="edit-avatar-preview">
                                        @if($user->pegawaiDinkes && $user->pegawaiDinkes->foto)
                                            <img src="{{ asset('storage/' . $user->pegawaiDinkes->foto) }}" id="preview-img" class="img-thumbnail rounded-4">
                                        @else
                                            <div id="placeholder-preview" class="preview-placeholder rounded-4">
                                                <i class="bi bi-camera fs-1"></i>
                                            </div>
                                            <img id="preview-img" class="img-thumbnail rounded-4 d-none">
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="foto" id="foto-input" class="form-control mb-2" accept="image/*">
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, WEBP. Maks 2MB.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="Nama_Lengkap" class="form-control" value="{{ old('Nama_Lengkap', $user->pegawaiDinkes->nama_pegawai ?? $user->Nama_Lengkap) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" value="{{ old('nip', $user->pegawaiDinkes->nip ?? $user->nip) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $user->pegawaiDinkes->jabatan ?? '') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bidang</label>
                                <input type="text" name="bidang" class="form-control" value="{{ old('bidang', $user->pegawaiDinkes->bidang ?? '') }}">
                            </div>

                            {{-- WILAYAH KERJA BARU --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $user->pegawaiDinkes->provinsi ?? '') }}" placeholder="Masukkan Provinsi">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kabupaten / Kota</label>
                                <input type="text" name="kabupaten_kota" class="form-control" value="{{ old('kabupaten_kota', $user->pegawaiDinkes->kabupaten_kota ?? '') }}" placeholder="Masukkan Kabupaten/Kota">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" class="form-control">{{ old('alamat', $user->pegawaiDinkes->alamat ?? '') }}</textarea>
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

            {{-- KANAN: AKSES & KEAMANAN --}}
            <div class="col-lg-4">
                <div class="data-card p-4">
                    <div class="section-title mb-4">
                        <i class="bi bi-shield-lock text-warning"></i>
                        <span>Hak Akses & Keamanan</span>
                    </div>

                    <form action="{{ route('admin.pengguna.updateAccess', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Username Sistem</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control bg-light" value="{{ $user->Username }}" readonly>
                            </div>
                            <small class="text-muted">Username tidak dapat diubah oleh admin.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Level Akses (Role)</label>
                            <select name="role_name" class="form-select border-2">
                                <option value="pegawai" {{ $user->role_name == 'pegawai' ? 'selected' : '' }}>Pegawai (Dinkes)</option>
                                <option value="petugas" {{ $user->role_name == 'petugas' ? 'selected' : '' }}>Petugas (Lapangan)</option>
                                <option value="admin" {{ $user->role_name == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Status Keaktifan</label>
                            <div class="p-3 border rounded-3 {{ $user->status_aktif ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="status_aktif" value="1" id="statusSwitch" {{ $user->status_aktif ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="statusSwitch">
                                        Akun {{ $user->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-shield-check me-2"></i> Perbarui Hak Akses
                        </button>
                    </form>

                    <div class="mt-4 p-3 bg-light rounded-3">
                        <div class="small text-muted fw-bold mb-2">INFO KEAMANAN:</div>
                        <div class="small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Admin hanya bisa mengubah hak akses. Reset password hanya bisa dilakukan oleh user melalui menu profil mereka.
                        </div>
                    </div>
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
            padding: 10px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 14.5px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* AVATAR PREVIEW STYLE */
        .edit-avatar-preview { width: 120px; height: 120px; flex-shrink: 0; }
        .edit-avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        .preview-placeholder {
            width: 100%; height: 100%; background-color: #f1f5f9;
            color: #cbd5e1; display: flex; align-items: center; justify-content: center;
            border: 2px dashed #cbd5e1;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white; border: none; padding: 12px 24px; border-radius: 12px;
            font-weight: 600; transition: all 0.2s;
        }
        .btn-action-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3); }

        .alert-modern { display: flex; align-items: flex-start; padding: 16px; border-radius: 16px; border: none; }
        .alert-danger { background-color: #fef2f2; color: #b91c1c; border-left: 5px solid #ef4444; }
    </style>

    {{-- ================= SCRIPT PREVIEW FOTO ================= --}}
    <script>
        document.getElementById('foto-input').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const img = document.getElementById('preview-img');
                const placeholder = document.getElementById('placeholder-preview');

                img.src = URL.createObjectURL(file);
                img.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
            }
        };
    </script>
@endsection