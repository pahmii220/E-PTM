@extends('layouts.master')
@section('title', 'Edit Deteksi Dini PTM')

@section('content')
    @php
    $savedDiagnoses = collect(explode(', ', $deteksi->diagnosa_penyakit ?? ''))
        ->map(fn($d) => trim($d))->filter()->all();

    // Parse sistolik/diastolik dari string "120/80"
    $tensiParts = explode('/', $deteksi->tekanan_darah ?? '');
    $oldSistolik = old('sistolik', $tensiParts[0] ?? '');
    $oldDiastolik = old('diastolik', $tensiParts[1] ?? '');
    @endphp

    <div class="ptm-wrap">

        {{-- HEADER --}}
        <div class="ptm-page-header">
            <div class="ptm-page-icon">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3" fill="#2aa8a0" opacity=".15"/><path d="M8 12h8M12 8v8" stroke="#2aa8a0" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <h4 class="ptm-page-title">Edit Deteksi Dini PTM</h4>
                <p class="ptm-page-sub">Perbarui data hasil pemeriksaan peserta</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="ptm-alert-error">
                <strong>Periksa kembali input Anda:</strong>
                <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('petugas.deteksi_dini.update', $deteksi->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        {{-- ===== SECTION 1: DATA PASIEN ===== --}}
        <div class="ptm-card">
            <h6 class="ptm-section-title"><span class="ptm-num">1</span> Data Pasien</h6>

            {{-- Pasien (readonly di edit) --}}
            <div class="ptm-field">
                <label class="ptm-label">Pasien</label>
                <input type="text" class="ptm-input ptm-readonly" value="{{ $deteksi->peserta->nama_lengkap }}" readonly>
            </div>

            {{-- Info Pasien --}}
            <div class="ptm-info-box">
                <div class="row g-2">
                    <div class="col-md-6"><span class="ptm-info-label">Nama Lengkap:</span> <strong>{{ $deteksi->peserta->nama_lengkap }}</strong></div>
                    <div class="col-md-6"><span class="ptm-info-label">Tanggal Lahir:</span> <strong>{{ optional($deteksi->peserta->tanggal_lahir)->format('d/m/Y') ?? '-' }}</strong></div>
                    <div class="col-md-6"><span class="ptm-info-label">NIK:</span> <strong>{{ $deteksi->peserta->nik ?? '-' }}</strong></div>
                    <div class="col-md-6"><span class="ptm-info-label">Jenis Kelamin:</span> <strong>{{ $deteksi->peserta->jenis_kelamin ?? '-' }}</strong></div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="ptm-label">Puskesmas</label>
                    @if(auth()->user()->role_name === 'admin')
                        <input type="text" class="ptm-input ptm-readonly" value="{{ $deteksi->puskesmas->nama_puskesmas ?? 'Mengikuti peserta' }}" readonly>
                    @else
                        <input type="text" class="ptm-input ptm-readonly" value="{{ auth()->user()->petugas->puskesmas->nama_puskesmas }}" readonly>
                        <input type="hidden" name="puskesmas_id" value="{{ auth()->user()->petugas->puskesmas_id }}">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="ptm-label">Tanggal Pemeriksaan <span class="ptm-req">*</span></label>
                    <input type="date" name="tanggal_pemeriksaan"
                        class="ptm-input @error('tanggal_pemeriksaan') is-invalid @enderror"
                        value="{{ old('tanggal_pemeriksaan', optional($deteksi->tanggal_pemeriksaan)->format('Y-m-d')) }}" required>
                    @error('tanggal_pemeriksaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: PEMERIKSAAN FISIK ===== --}}
        <div class="ptm-card">
            <h6 class="ptm-section-title"><span class="ptm-num">2</span> Pemeriksaan Fisik &amp; Klinis</h6>

            <p class="ptm-sublabel">Tekanan Darah</p>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="ptm-label-sm">Sistolik (mmHg)</label>
                    <input type="number" name="sistolik" id="sistolik" class="ptm-input" placeholder="120" value="{{ $oldSistolik }}">
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">Diastolik (mmHg)</label>
                    <input type="number" name="diastolik" id="diastolik" class="ptm-input" placeholder="80" value="{{ $oldDiastolik }}">
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">Gula Darah Sewaktu</label>
                    <div class="ptm-input-unit">
                        <input type="number" step="0.1" name="gula_darah" id="gula_darah" class="ptm-input" placeholder="100"
                            value="{{ old('gula_darah', $deteksi->gula_darah) }}">
                        <span class="ptm-unit">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="ptm-label-sm">Kolesterol Total</label>
                    <div class="ptm-input-unit">
                        <input type="number" step="0.1" name="kolesterol" id="kolesterol" class="ptm-input" placeholder="200"
                            value="{{ old('kolesterol', $deteksi->kolesterol) }}">
                        <span class="ptm-unit">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="ptm-label-sm">Tinggi Badan <span class="ptm-req">*</span></label>
                    <div class="ptm-input-unit">
                        <input type="number" step="0.1" name="tinggi_badan" id="tinggi_badan"
                            class="ptm-input @error('tinggi_badan') is-invalid @enderror"
                            placeholder="170" value="{{ old('tinggi_badan', $deteksi->tinggi_badan) }}" required>
                        <span class="ptm-unit">cm</span>
                    </div>
                    @error('tinggi_badan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">Berat Badan <span class="ptm-req">*</span></label>
                    <div class="ptm-input-unit">
                        <input type="number" step="0.01" name="berat_badan" id="berat_badan"
                            class="ptm-input @error('berat_badan') is-invalid @enderror"
                            placeholder="65" value="{{ old('berat_badan', $deteksi->berat_badan) }}" required>
                        <span class="ptm-unit">kg</span>
                    </div>
                    @error('berat_badan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">IMT (Indeks Massa Tubuh)</label>
                    <div class="ptm-imt-box">
                        <input type="text" id="imt_display" class="ptm-imt-val" readonly
                            value="{{ old('imt', $deteksi->imt) }}" placeholder="—">
                        <span class="ptm-imt-label" id="imt_label">—</span>
                    </div>
                    <input type="hidden" name="imt" id="imt_hidden" value="{{ $deteksi->imt }}">
                </div>
            </div>
        </div>

        {{-- ===== SECTION 3: FAKTOR RISIKO ===== --}}
        <div class="ptm-card">
            <h6 class="ptm-section-title"><span class="ptm-num">3</span> Faktor Risiko PTM</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="ptm-label-sm">Merokok? <span class="ptm-req">*</span></label>
                    <select name="merokok" class="ptm-select" required>
                        <option value="Tidak" {{ old('merokok', optional($deteksi->faktorRisiko)->merokok) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                        <option value="Ya"    {{ old('merokok', optional($deteksi->faktorRisiko)->merokok) == 'Ya' ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">Riwayat Keluarga PTM? <span class="ptm-req">*</span></label>
                    <select name="riwayat_keluarga" class="ptm-select" required>
                        <option value="Tidak" {{ old('riwayat_keluarga', optional($deteksi->faktorRisiko)->riwayat_keluarga) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                        <option value="Ya"    {{ old('riwayat_keluarga', optional($deteksi->faktorRisiko)->riwayat_keluarga) == 'Ya' ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="ptm-label-sm">Kurang Aktivitas Fisik? <span class="ptm-req">*</span></label>
                    <select name="kurang_aktivitas_fisik" class="ptm-select" required>
                        <option value="Tidak" {{ old('kurang_aktivitas_fisik', optional($deteksi->faktorRisiko)->kurang_aktivitas_fisik) == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                        <option value="Ya"    {{ old('kurang_aktivitas_fisik', optional($deteksi->faktorRisiko)->kurang_aktivitas_fisik) == 'Ya' ? 'selected' : '' }}>Ya</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ===== SECTION 4: DIAGNOSA ===== --}}
        <div class="ptm-card">
            <h6 class="ptm-section-title"><span class="ptm-num">4</span>Hasil Skrining Pemeriksaan & Diagnosa Penyakit</h6>
            <p class="ptm-desc">Diagnosa dihitung otomatis. Tambah penyakit penyerta secara manual di bawah jika diperlukan.</p>

            <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="ptm-sublabel mb-0">Status Deteksi PTM</p>
                <button type="button" id="btnToggleKriteria" class="btn btn-sm btn-link text-decoration-none shadow-none p-0" style="font-size: 0.85rem; color: #2aa8a0;">
                    <i class="bi bi-info-circle me-1"></i> Lihat Kriteria
                </button>
            </div>

            {{-- Info Kriteria --}}
            <div id="kriteriaCollapse" class="mb-3" style="display: none; transition: all 0.3s;">
                <div class="card card-body bg-light border-0 shadow-sm p-3" style="font-size: 0.85rem;">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.9rem;">Cara Perhitungan (Auto-Checked):</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-2 ps-3">
                                <li><strong>Hipertensi:</strong> Sistolik ≥ 140 atau Diastolik ≥ 90</li>
                                <li><strong>Pre-Hipertensi:</strong> Sistolik 130-139 atau Diastolik 85-89</li>
                                <li><strong>Gula Darah Tinggi:</strong> Gula Darah ≥ 200 mg/dL</li>
                                <li><strong>Gula Darah Batas:</strong> Gula Darah 140-199 mg/dL</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 ps-3">
                                <li><strong>Hiperkolesterolemia:</strong> Kolesterol > 200 mg/dL</li>
                                <li><strong>Obesitas:</strong> IMT ≥ 27.0</li>
                                <li><strong>Overweight:</strong> IMT 25.0 - 26.9</li>
                                <li><strong>Normal:</strong> Tidak ada indikasi di atas.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top text-muted" style="font-size: 0.8rem; font-style: italic;">
                        *Sumber Kriteria: Pedoman Pencegahan dan Pengendalian PTM, Kementerian Kesehatan RI.
                    </div>
                </div>
            </div>
            <div class="ptm-badge-row" id="auto-badge-container"></div>
            <p class="ptm-empty-msg" id="no-ptm-msg" style="display:none;">Isi data pemeriksaan fisik untuk melihat hasil deteksi otomatis.</p>

            {{-- Hidden auto-checks --}}
            <div class="d-none">
                <input type="checkbox" name="diagnosa_penyakit[]" value="Hipertensi"             id="cbHipertensi" {{ in_array('Hipertensi', $savedDiagnoses) ? 'checked' : '' }}>
                <input type="checkbox" name="diagnosa_penyakit[]" value="Gula Darah Tinggi (DM)" id="cbDiabetes"   {{ (in_array('Gula Darah Tinggi (DM)', $savedDiagnoses) || in_array('Diabetes Melitus', $savedDiagnoses)) ? 'checked' : '' }}>
                <input type="checkbox" name="diagnosa_penyakit[]" value="Obesitas"               id="cbObesitas"   {{ in_array('Obesitas', $savedDiagnoses) ? 'checked' : '' }}>
                <input type="checkbox" name="diagnosa_penyakit[]" value="Normal"                 id="cbNormal"     {{ in_array('Normal', $savedDiagnoses) ? 'checked' : '' }}>
                <input type="hidden"   name="tekanan_darah"                                       id="tekanan_darah_hidden" value="{{ $deteksi->tekanan_darah }}">
            </div>

            <hr class="ptm-divider">

            {{-- Manual PTM Lainnya --}}
            <p class="ptm-sublabel">Jenis PTM Lainnya <span class="ptm-opt">(Opsional, pilih manual)</span></p>
            <select class="form-control select2-manual" name="diagnosa_penyakit[]" multiple="multiple" style="width: 100%;">
                @php
                    $ptmList = [
                        'Hipertensi','Diabetes Melitus','Pre-Hipertensi','Prediabetes',
                        'Obesitas','Kolesterol Tinggi',
                        'Gangguan Penglihatan Miopia','Gangguan Penglihatan Katarak',
                        'Gangguan Pendengaran','Gangguan Pendengaran Presbikusis',
                        'Gangguan Jantung','Jantung Koroner','Gangguan Stroke','PPOK Umum',
                        'Kanker Payudara','Kanker Serviks','Kanker Paru','Kanker Kolorektal','Thalassemia'
                    ];
                @endphp
                @foreach($ptmList as $ptm)
                    <option value="{{ $ptm }}" {{ in_array($ptm, $savedDiagnoses) ? 'selected' : '' }}>
                        {{ $ptm }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ACTION --}}
        <div class="ptm-actions">
            <a href="{{ route('petugas.deteksi_dini.index') }}" class="ptm-btn-back">← Kembali</a>
            <button type="submit" class="ptm-btn-save">Simpan Perubahan</button>
        </div>

        </form>
    </div>

    @include('petugas.deteksi_dini._style_script', ['mode' => 'edit', 'deteksi' => $deteksi])
@endsection