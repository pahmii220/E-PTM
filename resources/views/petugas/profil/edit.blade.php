@extends('layouts.master')

@section('title', 'Profil Petugas')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
        <div class="container" style="max-width: 950px;">

            {{-- ================= HEADER HALAMAN ================= --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Profil Petugas</h3>
                    <p class="text-muted mb-0">Lengkapi data diri dan instansi tempat Anda bertugas.</p>
                </div>
            </div>

            {{-- ================= PESAN NOTIFIKASI ================= --}}
            @if(session('success'))
                <div
                    class="alert alert-success shadow-sm rounded-4 border-0 d-flex align-items-center mb-4 p-3 bg-white border-start border-success border-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 32px; height: 32px;">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-success mb-0">Berhasil!</h6>
                        <span class="text-muted small">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

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

            <form method="POST" action="{{ route('petugas.profil.update') }}" enctype="multipart/form-data">
                @csrf

                {{-- KARTU UTAMA PROFIL --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="profile-cover"
                        style="height: 140px; background: linear-gradient(135deg, #064e3b 0%, #10b981 100%);"></div>

                    <div class="card-body px-4 px-md-5 pb-5">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-end mb-5" style="margin-top: -65px;">

                            {{-- Area Avatar --}}
                            <div class="position-relative d-inline-block me-4 text-center">
                                <div class="bg-white rounded-circle p-1 shadow-sm mb-2">
                                    <div class="avatar-container"
                                        style="width: 120px; height: 120px; cursor: pointer; border-radius: 50%; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: center; background-color: #e9ecef;"
                                        onclick="document.getElementById('input_foto').click()"
                                        title="Klik untuk mengubah foto">

                                        <img id="preview_img"
                                            src="{{ !empty($petugas->foto) ? asset('storage/' . $petugas->foto) : '' }}"
                                            alt="Foto Profil" class="{{ !empty($petugas->foto) ? '' : 'd-none' }}"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            onerror="this.classList.add('d-none'); document.getElementById('default_icon').classList.remove('d-none');">

                                        <i id="default_icon"
                                            class="bi bi-person text-secondary {{ !empty($petugas->foto) ? 'd-none' : '' }}"
                                            style="font-size: 4rem;"></i>

                                        <div
                                            class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 overlay-hover transition-all">
                                            <i class="bi bi-camera-fill text-white fs-3"></i>
                                        </div>
                                    </div>
                                </div>

                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute rounded-circle shadow-sm {{ !empty($petugas->foto) ? '' : 'd-none' }}"
                                    style="bottom: 35px; right: 0px; width: 32px; height: 32px; padding: 0; z-index: 10;"
                                    id="btn_hapus_foto" onclick="hapusFoto()" title="Hapus Foto">
                                    <i class="bi bi-trash-fill"></i>
                                </button>

                                <input type="file" name="foto" id="input_foto" class="d-none"
                                    accept="image/jpeg,image/png,image/jpg" onchange="previewFoto(event)">
                                <input type="hidden" name="hapus_foto" id="hapus_foto_input" value="0">
                            </div>

                            {{-- Nama & Status Teks --}}
                            <div class="pb-2 text-center text-sm-start mt-3 mt-sm-0">
                                <h4 class="fw-bolder text-dark mb-1">{{ Auth::user()->Username ?? 'Petugas PTM' }}</h4>
                                <span
                                    class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success-subtle fw-medium">
                                    <i class="bi bi-hospital me-1"></i> Petugas Puskesmas
                                </span>
                            </div>
                        </div>

                        {{-- ================= FORM DATA PRIBADI ================= --}}
                        <div class="row g-4 mb-5">
                            <div class="col-12">
                                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-0 border-bottom pb-2">
                                    Informasi Pribadi</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">NIP</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-credit-card-2-front"></i></span>
                                    <input type="text" name="nip" class="form-control border-start-0 ps-0"
                                        value="{{ old('nip', $petugas->nip ?? Auth::user()->nip ?? '') }}" placeholder="Masukkan NIP (Jika ada)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-person"></i></span>
                                    <input type="text" name="nama_pegawai" class="form-control border-start-0 ps-0"
                                        value="{{ old('nama_pegawai', $petugas->nama_pegawai ?? Auth::user()->Nama_Lengkap ?? '') }}"
                                        placeholder="Contoh: Siti Aminah, S.Kep" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Lahir <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tanggal_lahir" class="form-control border-start-0 ps-0"
                                        value="{{ old('tanggal_lahir', $petugas->tanggal_lahir ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Alamat Email <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0"
                                        value="{{ old('email', Auth::user()->email ?? '') }}"
                                        placeholder="email.anda@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">No. Telepon / WA <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-telephone"></i></span>
                                    <input type="text" name="telepon" class="form-control border-start-0 ps-0"
                                        value="{{ old('telepon', $petugas->telepon ?? '') }}" placeholder="08xx xxxx xxxx"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Alamat Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="alamat" class="form-control border-start-0 ps-0"
                                        value="{{ old('alamat', $petugas->alamat ?? '') }}"
                                        placeholder="Masukkan domisili saat ini" required>
                                </div>
                            </div>
                        </div>

                        {{-- ================= FORM DATA PEKERJAAN ================= --}}
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-0 border-bottom pb-2">
                                    Informasi Instansi & Jabatan</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Jabatan</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-briefcase"></i></span>
                                    <input type="text" name="jabatan" class="form-control border-start-0 ps-0"
                                        value="{{ old('jabatan', $petugas->jabatan ?? '') }}"
                                        placeholder="Contoh: Petugas / PJ Program PTM">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Bidang / Unit</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-diagram-3"></i></span>
                                    <input type="text" name="bidang" class="form-control border-start-0 ps-0"
                                        value="{{ old('bidang', $petugas->bidang ?? '') }}"
                                        placeholder="Contoh: Poli PTM / KIA">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-dark">Puskesmas Tempat Bertugas <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-hospital"></i></span>
                                    <select name="puskesmas_id" class="form-select border-start-0 ps-0" required>
                                        <option value="">-- Pilih Puskesmas --</option>
                                        @foreach($puskesmas as $p)
                                            <option value="{{ $p->id }}" {{ ($petugas->puskesmas_id ?? '') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_puskesmas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER ACTION --}}
                    <div class="card-footer bg-light border-top p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('petugas.dashboard') }}"
                            class="btn btn-light bg-white border shadow-sm rounded-pill px-4 fw-medium hover-lift">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm hover-lift">
                            <i class="bi bi-save me-1"></i> Simpan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .avatar-container:hover .overlay-hover {
            opacity: 1 !important;
        }

        .overlay-hover {
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        .input-group-flat:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
            border-radius: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function previewFoto(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('preview_img');
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                    document.getElementById('default_icon').classList.add('d-none');
                    document.getElementById('hapus_foto_input').value = '0';
                    const btnHapus = document.getElementById('btn_hapus_foto');
                    if (btnHapus) btnHapus.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function hapusFoto() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil ini?')) {
                document.getElementById('hapus_foto_input').value = '1';
                document.getElementById('input_foto').value = '';
                document.getElementById('preview_img').classList.add('d-none');
                document.getElementById('default_icon').classList.remove('d-none');
                const btnHapus = document.getElementById('btn_hapus_foto');
                if (btnHapus) btnHapus.classList.add('d-none');
            }
        }
    </script>
@endpush