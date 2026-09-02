@extends('layouts.master')

@section('title', 'Pengaturan Profil Pegawai')

@section('content')
    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        <div class="container" style="max-width: 950px;">

            {{-- Bagian Header Halaman --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Pengaturan Profil</h3>
                    <p class="text-muted mb-0">Kelola informasi pribadi dan penugasan Anda di sistem PTM.</p>
                </div>

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

            <form method="POST" action="{{ route('pengguna.pegawai_dinkes.update', Auth::user()->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- KARTU UTAMA PROFIL --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

                    {{-- Banner / Cover Header --}}
                    <div class="profile-cover bg-primary-gradient"
                        style="height: 140px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);"></div>

                    <div class="card-body px-4 px-md-5 pb-5">

                        {{-- BLOK FOTO PROFIL (Tarik ke atas agar menimpa cover) --}}
                        <div class="d-flex flex-column flex-sm-row align-items-sm-end mb-5" style="margin-top: -65px;">

                            {{-- Area Avatar --}}
                            <div class="position-relative d-inline-block me-4 text-center">
                                <div class="bg-white rounded-circle p-1 shadow-sm mb-2">
                                    <div class="bg-light rounded-circle border d-flex align-items-center justify-content-center overflow-hidden avatar-container position-relative"
                                        style="width: 120px; height: 120px; cursor: pointer;"
                                        onclick="document.getElementById('input_foto').click()"
                                        title="Klik untuk mengubah foto">

                                        <img id="preview_img"
                                            src="{{ optional($pegawai)->foto ? asset('storage/' . $pegawai->foto) : '' }}"
                                            alt="Foto Profil" class="{{ optional($pegawai)->foto ? '' : 'd-none' }}"
                                            style="width: 100%; height: 100%; object-fit: cover;">

                                        <i id="default_icon"
                                            class="bi bi-person text-secondary {{ optional($pegawai)->foto ? 'd-none' : '' }}"
                                            style="font-size: 4rem;"></i>

                                        <div
                                            class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 overlay-hover transition-all">
                                            <i class="bi bi-camera-fill text-white fs-3"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Hapus --}}
                                <button type="button"
                                    class="btn btn-danger btn-sm position-absolute rounded-circle shadow-sm {{ optional($pegawai)->foto ? '' : 'd-none' }}"
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
                                <h4 class="fw-bolder text-dark mb-1">{{ Auth::user()->username ?? 'Pegawai Dinkes' }}</h4>
                                <span
                                    class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill border border-primary-subtle fw-medium">
                                    <i class="bi bi-shield-check me-1"></i> Pegawai Dinas Kesehatan
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
                                <label class="form-label fw-semibold text-dark">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-person"></i></span>
                                    <input type="text" name="nama_pegawai" class="form-control border-start-0 ps-0"
                                        value="{{ old('nama_pegawai', optional($pegawai)->nama_pegawai) }}"
                                        placeholder="Contoh: dr. Budi Santoso" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">NIP</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="bi bi-credit-card-2-front"></i>
                                    </span>

                                    {{-- Input text sekarang memiliki name="nip" dan readonly dihapus --}}
                                    <input type="text" name="nip" class="form-control border-start-0 ps-0"
                                        value="{{ old('nip', Auth::user()->nip ?? optional($pegawai)->nip) }}"
                                        placeholder="Masukkan NIP Anda">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Lahir</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-calendar-event"></i></span>
                                    <input type="date" name="tgl_lahir" class="form-control border-start-0 ps-0"
                                        value="{{ old('tgl_lahir', optional($pegawai)->tgl_lahir) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">No. Telepon / WhatsApp</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-telephone"></i></span>
                                    <input type="text" name="telepon" class="form-control border-start-0 ps-0"
                                        value="{{ old('telepon', optional($pegawai)->telepon) }}"
                                        placeholder="08xx xxxx xxxx">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Alamat Domisili</label>
                                <textarea name="alamat" class="form-control shadow-sm rounded-3 p-3" rows="2"
                                    placeholder="Masukkan alamat lengkap domisili saat ini">{{ old('alamat', optional($pegawai)->alamat) }}</textarea>
                            </div>
                        </div>

                        {{-- ================= FORM DATA PEKERJAAN ================= --}}
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-0 border-bottom pb-2">
                                    Informasi Jabatan & Wilayah</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Jabatan Saat Ini</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-briefcase"></i></span>
                                    <input type="text" name="jabatan" class="form-control border-start-0 ps-0"
                                        value="{{ old('jabatan', optional($pegawai)->jabatan) }}"
                                        placeholder="Contoh: Kepala Seksi / Staff">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Bidang</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-building"></i></span>
                                    <input type="text" name="bidang" class="form-control border-start-0 ps-0"
                                        value="{{ old('bidang', optional($pegawai)->bidang) }}" placeholder="Contoh: P2PTM">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Provinsi</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-map"></i></span>
                                    <select name="provinsi" class="form-select border-start-0 ps-0">
                                        <option value="">-- Pilih Provinsi --</option>
                                        <option value="Kalimantan Selatan" {{ old('provinsi', optional($pegawai)->provinsi) == 'Kalimantan Selatan' ? 'selected' : '' }}>
                                            Kalimantan Selatan</option>
                                        <option value="Kalimantan Tengah" {{ old('provinsi', optional($pegawai)->provinsi) == 'Kalimantan Tengah' ? 'selected' : '' }}>
                                            Kalimantan Tengah</option>
                                        <option value="Kalimantan Timur" {{ old('provinsi', optional($pegawai)->provinsi) == 'Kalimantan Timur' ? 'selected' : '' }}>
                                            Kalimantan Timur</option>
                                            <option value="Kalimantan Barat" {{ old('provinsi', optional($pegawai)->provinsi) == 'Kalimantan Barat' ? 'selected' : '' }}>
                                                Kalimantan Barat</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Kabupaten / Kota</label>
                                <div class="input-group input-group-flat shadow-sm rounded-3">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i
                                            class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="kabupaten_kota" class="form-control border-start-0 ps-0"
                                        value="{{ old('kabupaten_kota', optional($pegawai)->kabupaten_kota) }}"
                                        placeholder="Contoh: Banjarmasin">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Action Form --}}
                    <div class="card-footer bg-light border-top p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('pengguna.dashboard') }}"
                            class="btn btn-light bg-white border shadow-sm rounded-pill px-4 fw-medium hover-lift">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm hover-lift">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Utility CSS */
        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .input-group-flat .form-control:focus {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .input-group-flat:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            border-radius: 0.5rem;
        }

        /* Hover Effect Buttons */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        /* Avatar Overlays */
        .avatar-container .overlay-hover {
            transition: opacity 0.3s ease;
        }

        .avatar-container:hover .overlay-hover {
            opacity: 1 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Preview Gambar
        function previewFoto(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('preview_img').src = e.target.result;
                    document.getElementById('preview_img').classList.remove('d-none');
                    document.getElementById('default_icon').classList.add('d-none');
                    document.getElementById('hapus_foto_input').value = '0';

                    const btnHapus = document.getElementById('btn_hapus_foto');
                    if (btnHapus) btnHapus.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Hapus Gambar
        function hapusFoto() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil ini?')) {
                document.getElementById('hapus_foto_input').value = '1';
                document.getElementById('input_foto').value = '';

                document.getElementById('preview_img').classList.add('d-none');
                document.getElementById('preview_img').src = '';
                document.getElementById('default_icon').classList.remove('d-none');

                const btnHapus = document.getElementById('btn_hapus_foto');
                if (btnHapus) btnHapus.classList.add('d-none');
            }
        }
    </script>
@endpush