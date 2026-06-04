@extends('layouts.master')

@section('title', 'Edit Data Petugas')

@section('content')
    <div class="container-fluid px-md-5 py-4">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="page-header">
                <div class="header-icon">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div class="header-text">
                    <h1>Edit Data Petugas</h1>
                    <p>Perbarui informasi identitas, penempatan wilayah, dan hak akses akun petugas.</p>
                </div>
            </div>
            <a href="{{ route('admin.data_petugas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">

            {{-- ================================================= --}}
            {{-- KIRI: DATA PROFIL & PENEMPATAN --}}
            {{-- ================================================= --}}
            <div class="col-lg-8">
                <div class="data-card p-4 h-100">
                    <div class="section-title mb-4">
                        <i class="bi bi-person-vcard text-primary"></i>
                        <span>Informasi Identitas & Penempatan</span>
                    </div>

                    <form action="{{ route('admin.data_petugas.update', $petugas->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pegawai" class="form-control" required
                                    value="{{ old('nama_pegawai', $petugas->nama_pegawai) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" value="{{ old('nip', $petugas->nip) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" required
                                    value="{{ old('jabatan', $petugas->jabatan) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bidang</label>
                                <input type="text" name="bidang" class="form-control"
                                    value="{{ old('bidang', $petugas->bidang) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control"
                                    value="{{ old('tanggal_lahir', optional($petugas->tanggal_lahir)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telepon</label>
                                <input type="text" name="telepon" class="form-control"
                                    value="{{ old('telepon', $petugas->telepon) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Puskesmas (Wilayah Tugas)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i
                                            class="bi bi-hospital text-danger"></i></span>
                                    <select name="puskesmas_id" class="form-select">
                                        <option value="">-- Belum Ada Puskesmas --</option>
                                        @foreach($puskesmas as $p)
                                            <option value="{{ $p->id }}" {{ old('puskesmas_id', $petugas->puskesmas_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_puskesmas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2"
                                    class="form-control">{{ old('alamat', $petugas->alamat) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn-action-primary px-5">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================================================= --}}
            {{-- KANAN: HAK AKSES & KEAMANAN --}}
            {{-- ================================================= --}}
            <div class="col-lg-4">
                <div class="data-card p-4 h-100">
                    <div class="section-title mb-4">
                        <i class="bi bi-shield-lock text-warning"></i>
                        <span>Hak Akses & Keamanan</span>
                    </div>

                    @if($petugas->user)
                        <form action="{{ route('admin.data_petugas.update', $petugas->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_account_only" value="1">

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Username Sistem</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-at"></i></span>
                                    <input type="text" class="form-control bg-light" value="{{ $petugas->user->Username }}"
                                        readonly>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Level Akses (Role)</label>
                                <select name="role_name" class="form-select border-2">
                                    <option value="petugas" {{ $petugas->user->role_name == 'petugas' ? 'selected' : '' }}>Petugas
                                        (Lapangan)</option>
                                    <option value="pegawai" {{ $petugas->user->role_name == 'pegawai' ? 'selected' : '' }}>Pegawai
                                        (Dinkes)</option>
                                    <option value="admin" {{ $petugas->user->role_name == 'admin' ? 'selected' : '' }}>
                                        Administrator</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label fw-bold">Status Keaktifan</label>
                                <div id="statusContainer"
                                    class="p-3 border rounded-3 transition-colors {{ $petugas->user->status_aktif ? 'bg-success-subtle border-success-subtle' : 'bg-danger-subtle border-danger-subtle' }}">
                                    <div class="form-check form-switch m-0 d-flex align-items-center">
                                        {{-- HIDDEN INPUT DIHAPUS DARI SINI --}}
                                        <input class="form-check-input fs-5 me-2 mt-0" type="checkbox" name="status_aktif"
                                            value="1" id="statusSwitch" onchange="toggleStatusText(this)" {{ $petugas->user->status_aktif ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" id="statusLabel" for="statusSwitch">
                                            Akun {{ $petugas->user->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">Nonaktifkan untuk memblokir login sementara.</small>
                            </div>

                            <div class="mt-auto pt-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="bi bi-shield-check me-2"></i> Perbarui Hak Akses
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold">Belum Ada Akun</h6>
                            <p class="small text-muted mb-0">Petugas ini belum memiliki akun untuk login ke dalam sistem.</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
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
            padding: 10px 14px;
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

        .transition-colors {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
    </style>

    {{-- ================= SCRIPT ================= --}}
    <script>
        function toggleStatusText(checkbox) {
            const label = document.getElementById('statusLabel');
            const container = document.getElementById('statusContainer');

            if (checkbox.checked) {
                label.innerText = 'Akun Aktif';
                // Ubah warna background ke hijau
                container.classList.remove('bg-danger-subtle', 'border-danger-subtle');
                container.classList.add('bg-success-subtle', 'border-success-subtle');
            } else {
                label.innerText = 'Akun Nonaktif';
                // Ubah warna background ke merah
                container.classList.remove('bg-success-subtle', 'border-success-subtle');
                container.classList.add('bg-danger-subtle', 'border-danger-subtle');
            }
        }
    </script>
@endsection