@extends('layouts.master')

@section('title', 'Edit Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1200px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#0f766e,#0d9488)">
            <div class="card-body text-white p-4">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square me-2"></i> Edit Tindak Lanjut PTM
                </h4>
                <small class="opacity-75">
                    Perbarui data tindak lanjut berdasarkan hasil pemeriksaan klinis terbaru.
                </small>
            </div>
        </div>

        {{-- ERROR / VALIDATION --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4">
                <strong>Periksa kembali input Anda:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">

            {{-- ================= LEFT COLUMN: SUMMARY BOX ================= --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-person-badge text-teal fs-5 me-1"></i> Data & Hasil Skrining Pasien
                        </h5>
                        <hr class="mt-3">
                    </div>
                    <div class="card-body p-4 pt-2">
                        
                        {{-- 1. IDENTITAS PESERTA --}}
                        <div class="bg-light p-3 rounded-3 mb-4 border border-slate-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-teal text-white rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="bi bi-person-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $tindakLanjut->peserta->nama_lengkap }}</h6>
                                    <small class="text-muted">NIK: {{ $tindakLanjut->peserta->nik }}</small>
                                </div>
                            </div>
                            <div class="row g-2 text-dark" style="font-size: 0.9rem;">
                                <div class="col-6"><strong>Umur:</strong> {{ \Carbon\Carbon::parse($tindakLanjut->peserta->tanggal_lahir)->age }} Tahun</div>
                                <div class="col-6"><strong>Gender:</strong> {{ $tindakLanjut->peserta->jenis_kelamin }}</div>
                                <div class="col-12 mt-1"><strong>Tanggal Periksa:</strong> {{ \Carbon\Carbon::parse($tindakLanjut->deteksiDini->tanggal_pemeriksaan)->format('d F Y') }}</div>
                            </div>
                        </div>

                        {{-- 2. HASIL CLINICAL METRICS --}}
                        <h6 class="fw-bold text-dark mb-2">Hasil Pemeriksaan Klinis</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Tensi Darah</small>
                                    <span class="fw-bold text-dark fs-6">{{ $tindakLanjut->deteksiDini->tekanan_darah }}</span> <small class="text-muted">mmHg</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Gula Darah</small>
                                    <span class="fw-bold text-dark fs-6">{{ $tindakLanjut->deteksiDini->gula_darah ?? '-' }}</span> <small class="text-muted">mg/dL</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Kolesterol</small>
                                    <span class="fw-bold text-dark fs-6">{{ $tindakLanjut->deteksiDini->kolesterol ?? '-' }}</span> <small class="text-muted">mg/dL</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">IMT (Kategori)</small>
                                    <span class="fw-bold text-dark fs-6">{{ $tindakLanjut->deteksiDini->imt }}</span> 
                                    @php
                                        $imt = (float)$tindakLanjut->deteksiDini->imt;
                                        $cat = 'Normal';
                                        if ($imt >= 27) $cat = 'Obesitas';
                                        elseif ($imt >= 25) $cat = 'Overweight';
                                        elseif ($imt < 18.5) $cat = 'Underweight';
                                    @endphp
                                    <small class="text-muted">({{ $cat }})</small>
                                </div>
                            </div>
                        </div>

                        {{-- 3. POLA HIDUP / FAKTOR RISIKO --}}
                        <h6 class="fw-bold text-dark mb-2">Faktor Risiko Pola Hidup</h6>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if(optional($faktor)->merokok === 'Ya')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded">Merokok</span>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-2 rounded">Tidak Merokok</span>
                            @endif

                            @if(optional($faktor)->riwayat_keluarga === 'Ya')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded">Ada Riwayat Keluarga</span>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-2 rounded">Tidak Ada Riwayat Keluarga</span>
                            @endif

                            @if(optional($faktor)->kurang_aktivitas_fisik === 'Ya')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded">Kurang Aktivitas Fisik</span>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-2 rounded">Cukup Aktivitas Fisik</span>
                            @endif
                        </div>

                        {{-- 4. DIAGNOSA PTM DARI SISTEM --}}
                        <h6 class="fw-bold text-dark mb-2">Diagnosa Terdeteksi</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $diags = array_map('trim', explode(',', $tindakLanjut->deteksiDini->diagnosa_penyakit ?? ''));
                            @endphp
                            @foreach($diags as $d)
                                @if($d === 'Normal')
                                    <span class="badge bg-success text-white px-3 py-2 rounded fs-6 fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Normal</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-2 rounded fs-6 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $d }}</span>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= RIGHT COLUMN: FORM TINDAK LANJUT ================= --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-pencil-square text-teal fs-5 me-1"></i> Edit Detail Tindak Lanjut
                        </h5>
                        <hr class="mt-3">
                    </div>
                    <div class="card-body p-4 pt-2">

                        {{-- SMART RECOMMENDATION BANNER --}}
                        <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-start shadow-sm" style="background-color: #f0fdfa; border-left: 4px solid #0f766e !important;">
                            <div class="text-teal me-3 fs-3"><i class="bi bi-robot"></i></div>
                            <div>
                                <h6 class="fw-bold text-teal mb-1">Terapkan Ulang Rekomendasi</h6>
                                <p class="text-muted small mb-2">
                                    Anda dapat menulis ulang saran tindakan menggunakan templat sistem berdasarkan diagnosa pasien.
                                </p>
                                <button type="button" id="btn-terapkan-rekomendasi" class="btn btn-teal btn-sm fw-bold rounded-2 px-3 shadow-sm" style="background-color: #0f766e; color: white;">
                                    <i class="bi bi-magic me-1"></i> Terapkan Rekomendasi Otomatis
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('petugas.tindak_lanjut.update', $tindakLanjut->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- JENIS TINDAK LANJUT --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis Tindak Lanjut <span class="text-danger">*</span></label>
                                    <select name="jenis_tindak_lanjut" class="form-select rounded-3 @error('jenis_tindak_lanjut') is-invalid @enderror" required
                                        oninvalid="this.setCustomValidity('Jenis tindak lanjut wajib dipilih.')"
                                        oninput="this.setCustomValidity('')">
                                        <option value="">-- Pilih --</option>
                                        <option value="edukasi" {{ old('jenis_tindak_lanjut', $tindakLanjut->jenis_tindak_lanjut) == 'edukasi' ? 'selected' : '' }}>Edukasi / Penyuluhan</option>
                                        <option value="anjuran_gaya_hidup" {{ old('jenis_tindak_lanjut', $tindakLanjut->jenis_tindak_lanjut) == 'anjuran_gaya_hidup' ? 'selected' : '' }}>Anjuran Gaya Hidup</option>
                                        <option value="rujukan" {{ old('jenis_tindak_lanjut', $tindakLanjut->jenis_tindak_lanjut) == 'rujukan' ? 'selected' : '' }}>Rujukan Faskes Lanjutan</option>
                                        <option value="monitoring" {{ old('jenis_tindak_lanjut', $tindakLanjut->jenis_tindak_lanjut) == 'monitoring' ? 'selected' : '' }}>Monitoring Berkala</option>
                                        <option value="tidak_ada" {{ old('jenis_tindak_lanjut', $tindakLanjut->jenis_tindak_lanjut) == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada Tindakan</option>
                                    </select>
                                    @error('jenis_tindak_lanjut')
                                        <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Tindak Lanjut</label>
                                    <input type="date" name="tanggal_tindak_lanjut" class="form-control rounded-3"
                                        value="{{ old('tanggal_tindak_lanjut', $tindakLanjut->tanggal_tindak_lanjut) }}" required
                                        oninvalid="this.setCustomValidity('Tanggal tindak lanjut wajib diisi.')"
                                        oninput="this.setCustomValidity('')">
                                </div>

                                {{-- STATUS --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Tindak Lanjut <span class="text-danger">*</span></label>
                                    <select name="status_tindak_lanjut" class="form-select rounded-3 @error('status_tindak_lanjut') is-invalid @enderror" required
                                        oninvalid="this.setCustomValidity('Status tindak lanjut wajib dipilih.')"
                                        oninput="this.setCustomValidity('')">
                                        <option value="belum" {{ old('status_tindak_lanjut', $tindakLanjut->status_tindak_lanjut) == 'belum' ? 'selected' : '' }}>Belum Dilakukan</option>
                                        <option value="sudah" {{ old('status_tindak_lanjut', $tindakLanjut->status_tindak_lanjut) == 'sudah' ? 'selected' : '' }}>Sudah Dilakukan</option>
                                    </select>
                                    @error('status_tindak_lanjut')
                                        <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- CATATAN PETUGAS / EDUKASI --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold">Catatan / Saran Medis Petugas <span class="text-danger">*</span></label>
                                    <textarea name="catatan_petugas" class="form-control rounded-3 @error('catatan_petugas') is-invalid @enderror" rows="7" required
                                        placeholder="Ketik saran pola makan, edukasi pencegahan, rincian obat awal, atau tujuan faskes rujukan di sini..."
                                        oninvalid="this.setCustomValidity('Catatan / saran medis petugas wajib diisi.')"
                                        oninput="this.setCustomValidity('')">{{ old('catatan_petugas', $tindakLanjut->catatan_petugas) }}</textarea>
                                    @error('catatan_petugas')
                                        <small class="text-danger mt-1 d-block fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('petugas.tindak_lanjut.index') }}"
                                    class="btn btn-outline-secondary rounded-3 px-4">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-teal rounded-3 px-4 shadow-sm" style="background-color: #0f766e; color: white;">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('styles')
    <style>
        body {
            background-color: #f1f5f9;
        }

        .form-control,
        .form-select {
            border-color: #cbd5e1;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.25);
        }

        .text-teal {
            color: #0f766e;
        }

        .bg-teal {
            background-color: #0f766e;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisSelect = document.querySelector('select[name="jenis_tindak_lanjut"]');
            const catatanText = document.querySelector('textarea[name="catatan_petugas"]');
            const btnTerapkan = document.getElementById('btn-terapkan-rekomendasi');

            // Ambil diagnosa dari PHP
            const diagnosaList = {!! json_encode(array_map('trim', explode(',', $tindakLanjut->deteksiDini->diagnosa_penyakit ?? ''))) !!};
            const hasHipertensi = diagnosaList.includes('Hipertensi');
            const hasDiabetes = diagnosaList.includes('Gula Darah Tinggi (DM)') || diagnosaList.includes('Diabetes Melitus');
            const hasObesitas = diagnosaList.includes('Obesitas') || diagnosaList.includes('Overweight');

            // Cari jika ada penyakit manual (spesifik)
            const manualPTM = [];
            const autoList = ['Normal', 'Hipertensi', 'Gula Darah Tinggi (DM)', 'Diabetes Melitus', 'Obesitas', 'Overweight', 'Pre-Hipertensi', 'Gula Darah Batas', 'Hiperkolesterolemia'];
            diagnosaList.forEach(d => {
                if (d && !autoList.includes(d)) {
                    manualPTM.push(d);
                }
            });

            // Ambil data personalisasi dari PHP
            const namaPasien = "{{ $tindakLanjut->peserta->nama_lengkap ?? 'Pasien' }}";
            const tensiPasien = "{{ $tindakLanjut->deteksiDini->tekanan_darah ?? '-' }}";
            const gulaPasien = "{{ $tindakLanjut->deteksiDini->gula_darah ?? '-' }}";
            const imtPasien = "{{ $tindakLanjut->deteksiDini->imt ?? '-' }}";

            // Bangun Rekomendasi
            let jenisRekomendasi = "edukasi";
            let saranArray = [];

            if (hasHipertensi) {
                const templateHipertensi = [
                    "Berdasarkan tensi " + tensiPasien + " mmHg pada " + namaPasien + ", disarankan untuk melakukan diet DASH (rendah garam), membatasi asupan kafein dan makanan asin, serta rutin berolahraga aerobik minimal 30 menit sehari.",
                    "Tensi " + namaPasien + " terukur " + tensiPasien + " mmHg. Edukasikan pembatasan garam dapur (maksimal 1 sendok teh/hari), kelola stress, cukup tidur, dan ingatkan untuk kontrol tekanan darah setiap 2 minggu.",
                    "Catatan penanganan tensi " + tensiPasien + " mmHg untuk " + namaPasien + ": Edukasikan olahraga kardio ringan secara teratur, hindari makanan cepat saji/olahan bergaram tinggi, dan anjurkan pemantauan tensi berkala."
                ];
                const pembukaHipertensi = templateHipertensi[Math.floor(Math.random() * templateHipertensi.length)];
                saranArray.push("- Hipertensi: " + pembukaHipertensi);
            }
            if (hasDiabetes) {
                const templateDiabetes = [
                    "Mengingat kadar gula darah " + namaPasien + " mencapai " + gulaPasien + " mg/dL, berikan edukasi pembatasan asupan karbohidrat sederhana/manis, perbanyak serat dari sayuran, dan anjurkan jalan kaki rutin.",
                    "Hasil gula darah " + gulaPasien + " mg/dL pada " + namaPasien + " menunjukkan indikasi risiko. Sarankan diet rendah indeks glikemik, kurangi minuman manis kemasan, dan lakukan pemantauan kadar gula berkala.",
                    "Saran untuk " + namaPasien + " (Gula Darah: " + gulaPasien + " mg/dL): Konseling pola makan rendah gula, perbanyak aktivitas fisik harian, dan ingatkan untuk minum air putih yang cukup."
                ];
                const pembukaDiabetes = templateDiabetes[Math.floor(Math.random() * templateDiabetes.length)];
                saranArray.push("- Gula Darah Tinggi: " + pembukaDiabetes);
            }
            if (hasObesitas) {
                const templateObesitas = [
                    "Kategori IMT " + imtPasien + " pada " + namaPasien + " menunjukkan obesitas. Rekomendasikan porsi piring makan seimbang (50% sayur/buah), batasi konsumsi gorengan, dan lakukan aktivitas fisik pembakar kalori.",
                    "Evaluasi gizi " + namaPasien + " dengan IMT " + imtPasien + ": Edukasikan pengurangan asupan lemak jenuh, hindari ngemil larut malam, dan naikkan frekuensi aktivitas fisik minimal 150 menit seminggu.",
                    "Saran diet untuk " + namaPasien + " (IMT: " + imtPasien + "): Kontrol porsi makan harian, kurangi asupan bersantan dan digoreng, serta rutinkan olahraga terukur."
                ];
                const pembukaObesitas = templateObesitas[Math.floor(Math.random() * templateObesitas.length)];
                saranArray.push("- Obesitas/Gizi: " + pembukaObesitas);
            }
            if (manualPTM.length > 0) {
                jenisRekomendasi = "rujukan";
                saranArray.push("- Temuan PTM (" + manualPTM.join(', ') + "): Rujuk " + namaPasien + " ke fasilitas kesehatan tingkat lanjut (Rumah Sakit) untuk konsultasi dokter spesialis dan pemeriksaan penunjang yang lebih mendalam.");
            }

            if (saranArray.length === 0) {
                jenisRekomendasi = "anjuran_gaya_hidup";
                const templateNormal = [
                    "Kondisi kesehatan " + namaPasien + " terpantau Normal. Pertahankan pola hidup sehat dengan konsumsi makanan bergizi seimbang, hindari asap rokok, cukup istirahat, dan lakukan skrining kesehatan tahunan.",
                    "Pemeriksaan " + namaPasien + " dalam batas Normal. Apresiasi pasien untuk tetap mempertahankan gaya hidup aktif, gizi seimbang, kelola stres, dan sarankan untuk skrining PTM secara berkala.",
                    "Hasil skrining " + namaPasien + " Normal. Berikan motivasi untuk konsisten menjaga pola makan sehat rendah lemak, olahraga teratur, dan lakukan pemeriksaan ulang tahun depan."
                ];
                const pembukaNormal = templateNormal[Math.floor(Math.random() * templateNormal.length)];
                saranArray.push("- Normal: " + pembukaNormal);
            }

            const saranText = "Saran Rekomendasi Medis (Personalized):\n" + saranArray.join("\n\n");

            // Event handler untuk button terapkan
            if (btnTerapkan) {
                btnTerapkan.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (jenisSelect) {
                        jenisSelect.value = jenisRekomendasi;
                    }
                    if (catatanText) {
                        catatanText.value = saranText;
                    }
                    
                    alert("Rekomendasi otomatis berhasil diterapkan! Anda dapat mengedit saran tersebut jika diperlukan.");
                });
            }
        });
    </script>
@endpush