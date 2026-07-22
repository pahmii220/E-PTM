@extends('layouts.master')

@section('title', 'Tambah Tindak Lanjut PTM')

@section('content')
    <div class="container-fluid py-4" style="max-width:1250px; margin: auto;">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#0f766e,#0d9488)">
            <div class="card-body text-white p-4">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-clipboard-plus me-2"></i> Tambah Tindak Lanjut PTM
                </h4>
                <small class="opacity-75">
                    Tentukan tindakan rujukan, edukasi, atau gaya hidup setelah melakukan deteksi dini PTM.
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

        {{-- SELECT PASIEN DROPDOWN CARD --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="mb-0" x-ignore>
                    <label class="form-label fw-bold text-dark fs-6"><i class="bi bi-search text-teal me-1"></i> Pilih Hasil Pemeriksaan Pasien</label>
                    <select name="deteksi_dini_id_select" id="deteksi_dini_id_select" class="form-select select2-deteksi" style="background-color: #f0fdfa;">
                        <option value="">-- Cari Nama Pasien / Tanggal Pemeriksaan --</option>
                        @foreach ($daftarDeteksi as $d)
                            @php
                                $fr = \App\Models\FaktorResikoPTM::where('peserta_id', $d->peserta_id)
                                    ->where('tanggal_pemeriksaan', $d->tanggal_pemeriksaan)
                                    ->first();

                                $imt = (float)$d->imt;
                                $cat = 'Normal';
                                if ($imt >= 27) $cat = 'Obesitas';
                                elseif ($imt >= 25) $cat = 'Overweight';
                                elseif ($imt < 18.5) $cat = 'Underweight';
                            @endphp
                            <option value="{{ $d->id }}"
                                {{ (isset($deteksiTerpilih) && $deteksiTerpilih->id == $d->id) ? 'selected' : '' }}
                                data-nik="{{ $d->peserta->nik }}"
                                data-nama="{{ $d->peserta->nama_lengkap }}"
                                data-umur="{{ \Carbon\Carbon::parse($d->peserta->tanggal_lahir)->age }} Tahun"
                                data-jk="{{ $d->peserta->jenis_kelamin }}"
                                data-tanggal-periksa="{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d F Y') }}"
                                data-tensi="{{ $d->tekanan_darah }}"
                                data-gula="{{ $d->gula_darah ?? '-' }}"
                                data-kolesterol="{{ $d->kolesterol ?? '-' }}"
                                data-imt="{{ $d->imt }}"
                                data-imt-kategori="{{ $cat }}"
                                data-merokok="{{ optional($fr)->merokok ?? 'Tidak' }}"
                                data-riwayat-keluarga="{{ optional($fr)->riwayat_keluarga ?? 'Tidak' }}"
                                data-kurang-aktivitas="{{ optional($fr)->kurang_aktivitas_fisik ?? 'Tidak' }}"
                                data-diagnosa="{{ $d->diagnosa_penyakit }}">
                                {{ $d->peserta->nama_lengkap }} — (Periksa: {{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }} | Diagnosa: {{ $d->diagnosa_penyakit }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-1 d-block">Pilihan ini berisi daftar pasien yang sudah memiliki data pemeriksaan fisik namun belum memiliki catatan tindak lanjut.</small>
                </div>
            </div>
        </div>

        {{-- PLACEHOLDER KOSONG --}}
        <div id="placeholder-kosong" class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <div class="bg-teal-subtle text-teal rounded-circle p-3 d-inline-flex mb-3" style="background-color: #f0fdfa;">
                    <i class="bi bi-file-earmark-person fs-1 text-teal"></i>
                </div>
                <h5 class="fw-bold text-dark">Belum Ada Pasien Terpilih</h5>
                <p class="text-muted mx-auto" style="max-width: 400px;">
                    Silakan pilih hasil pemeriksaan pasien pada dropdown di atas untuk melanjutkan penginputan rencana tindak lanjut medis.
                </p>
            </div>
        </div>

        {{-- MAIN CONTENT (HIDDEN BY DEFAULT UNTIL PASIEN SELECTED) --}}
        <div id="main-content-layout" class="row g-4 d-none">

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
                                    <h6 class="fw-bold text-dark mb-0" id="summary-nama">-</h6>
                                    <small class="text-muted">NIK: <span id="summary-nik">-</span></small>
                                </div>
                            </div>
                            <div class="row g-2 text-dark" style="font-size: 0.9rem;">
                                <div class="col-6"><strong>Umur:</strong> <span id="summary-umur">-</span></div>
                                <div class="col-6"><strong>Gender:</strong> <span id="summary-jk">-</span></div>
                                <div class="col-12 mt-1"><strong>Tanggal Periksa:</strong> <span id="summary-tgl">-</span></div>
                            </div>
                        </div>

                        {{-- 2. HASIL CLINICAL METRICS --}}
                        <h6 class="fw-bold text-dark mb-2">Hasil Pemeriksaan Klinis</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Tensi Darah</small>
                                    <span class="fw-bold text-dark fs-6" id="summary-tensi">-</span> <small class="text-muted">mmHg</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Gula Darah</small>
                                    <span class="fw-bold text-dark fs-6" id="summary-gula">-</span> <small class="text-muted">mg/dL</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">Kolesterol</small>
                                    <span class="fw-bold text-dark fs-6" id="summary-kolesterol">-</span> <small class="text-muted">mg/dL</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-2.5 rounded bg-white text-center">
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">IMT (Kategori)</small>
                                    <span class="fw-bold text-dark fs-6" id="summary-imt">-</span> 
                                    <small class="text-muted" id="summary-imt-kategori">(-)</small>
                                </div>
                            </div>
                        </div>

                        {{-- 3. POLA HIDUP / FAKTOR RISIKO --}}
                        <h6 class="fw-bold text-dark mb-2">Faktor Risiko Pola Hidup</h6>
                        <div class="d-flex flex-wrap gap-2 mb-4" id="summary-faktor-container">
                            {{-- Dihasilkan via JS --}}
                        </div>

                        {{-- 4. DIAGNOSA PTM DARI SISTEM --}}
                        <h6 class="fw-bold text-dark mb-2">Diagnosa Terdeteksi</h6>
                        <div class="d-flex flex-wrap gap-2" id="summary-diagnosa-container">
                            {{-- Dihasilkan via JS --}}
                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= RIGHT COLUMN: FORM TINDAK LANJUT ================= --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-pencil-square text-teal fs-5 me-1"></i> Input Rencana Tindak Lanjut
                        </h5>
                        <hr class="mt-3">
                    </div>
                    <div class="card-body p-4 pt-2">

                        {{-- SMART RECOMMENDATION BANNER --}}
                        <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-start shadow-sm" style="background-color: #f0fdfa; border-left: 4px solid #0f766e !important;">
                            <div class="text-teal me-3 fs-3"><i class="bi bi-robot"></i></div>
                            <div>
                                <h6 class="fw-bold text-teal mb-1">Rekomendasi Tindak Lanjut Sistem</h6>
                                <p class="text-muted small mb-2" id="rekomendasi-teks">
                                    Berasarkan diagnosa yang terdeteksi, sistem menyarankan pemberian edukasi gaya hidup sehat dan pembatasan asupan risiko.
                                </p>
                                <button type="button" id="btn-terapkan-rekomendasi" class="btn btn-teal btn-sm fw-bold rounded-2 px-3 shadow-sm" style="background-color: #0f766e; color: white;">
                                    <i class="bi bi-magic me-1"></i> Terapkan Rekomendasi Otomatis
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('petugas.tindak_lanjut.store') }}" method="POST">
                            @csrf

                            {{-- HIDDEN INPUT UNTUK DETEKSI DINI ID (DI-SET VIA JS) --}}
                            <input type="hidden" name="deteksi_dini_id" id="deteksi_dini_id" value="">

                            <div class="row g-4">
                                {{-- JENIS TINDAK LANJUT --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis Tindak Lanjut <span class="text-danger">*</span></label>
                                    <select name="jenis_tindak_lanjut" class="form-select rounded-3" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="edukasi" {{ old('jenis_tindak_lanjut') == 'edukasi' ? 'selected' : '' }}>Edukasi / Penyuluhan</option>
                                        <option value="anjuran_gaya_hidup" {{ old('jenis_tindak_lanjut') == 'anjuran_gaya_hidup' ? 'selected' : '' }}>Anjuran Gaya Hidup</option>
                                        <option value="rujukan" {{ old('jenis_tindak_lanjut') == 'rujukan' ? 'selected' : '' }}>Rujukan Faskes Lanjutan</option>
                                        <option value="monitoring" {{ old('jenis_tindak_lanjut') == 'monitoring' ? 'selected' : '' }}>Monitoring Berkala</option>
                                        <option value="tidak_ada" {{ old('jenis_tindak_lanjut') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada Tindakan</option>
                                    </select>
                                </div>

                                {{-- TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Tindak Lanjut</label>
                                    <input type="date" name="tanggal_tindak_lanjut" class="form-control rounded-3"
                                        value="{{ old('tanggal_tindak_lanjut', date('Y-m-d')) }}" required>
                                </div>

                                {{-- CATATAN PETUGAS / EDUKASI --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold">Catatan / Saran Medis Petugas <span class="text-danger">*</span></label>
                                    <textarea name="catatan_petugas" class="form-control rounded-3" rows="7" required
                                        placeholder="Ketik saran pola makan, edukasi pencegahan, rincian obat awal, atau tujuan rumah sakit rujukan di sini...">{{ old('catatan_petugas') }}</textarea>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('petugas.tindak_lanjut.index') }}"
                                    class="btn btn-outline-secondary rounded-3 px-4">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-teal rounded-3 px-4 shadow-sm" style="background-color: #0f766e; color: white;">
                                    <i class="bi bi-save me-1"></i> Simpan Tindak Lanjut
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 40px; border-color: #cbd5e1;
        }
        .select2-container--bootstrap-5.select2-deteksi .select2-selection {
            background-color: #f0fdfa;
            border-color: #99f6e4;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // -- Initialize Select2
            $('#deteksi_dini_id_select').select2({ theme: 'bootstrap-5' });

            const jenisSelect = document.querySelector('select[name="jenis_tindak_lanjut"]');
            const catatanText = document.querySelector('textarea[name="catatan_petugas"]');
            const btnTerapkan = document.getElementById('btn-terapkan-rekomendasi');

            // Data penampung rekomendasi aktif
            let activeRecomJenis = "edukasi";
            let activeRecomSaran = "";

            // -- Update Page Content on Patient Selection Change
            $('#deteksi_dini_id_select').on('change', function() {
                var selectedOpt = $(this).find('option:selected');
                var val = selectedOpt.val();

                if (val) {
                    // Set value ke hidden input form
                    $('#deteksi_dini_id').val(val);

                    // Update Identitas Pasien
                    $('#summary-nama').text(selectedOpt.data('nama'));
                    $('#summary-nik').text(selectedOpt.data('nik'));
                    $('#summary-umur').text(selectedOpt.data('umur'));
                    $('#summary-jk').text(selectedOpt.data('jk'));
                    $('#summary-tgl').text(selectedOpt.data('tanggal-periksa'));

                    // Update Metrics Klinis
                    $('#summary-tensi').text(selectedOpt.data('tensi'));
                    $('#summary-gula').text(selectedOpt.data('gula'));
                    $('#summary-kolesterol').text(selectedOpt.data('kolesterol'));
                    $('#summary-imt').text(selectedOpt.data('imt'));
                    $('#summary-imt-kategori').text('(' + selectedOpt.data('imt-kategori') + ')');

                    // Update Faktor Risiko Pola Hidup (Badges)
                    var fContainer = $('#summary-faktor-container');
                    fContainer.empty();
                    
                    var merokok = selectedOpt.data('merokok');
                    var keluarga = selectedOpt.data('riwayat-keluarga');
                    var aktivitas = selectedOpt.data('kurang-aktivitas');

                    if (merokok === 'Ya') {
                        fContainer.append('<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded">Merokok</span>');
                    } else {
                        fContainer.append('<span class="badge bg-light text-muted border px-3 py-2 rounded">Tidak Merokok</span>');
                    }

                    if (keluarga === 'Ya') {
                        fContainer.append('<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded">Ada Riwayat Keluarga</span>');
                    } else {
                        fContainer.append('<span class="badge bg-light text-muted border px-3 py-2 rounded">Tidak Ada Riwayat Keluarga</span>');
                    }

                    if (aktivitas === 'Ya') {
                        fContainer.append('<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded">Kurang Aktivitas Fisik</span>');
                    } else {
                        fContainer.append('<span class="badge bg-light text-muted border px-3 py-2 rounded">Cukup Aktivitas Fisik</span>');
                    }

                    // Update Diagnosa Badges
                    var dContainer = $('#summary-diagnosa-container');
                    dContainer.empty();

                    var diagnosaText = selectedOpt.data('diagnosa') || '';
                    var diagnosaList = diagnosaText.split(',').map(function(item) { return item.trim(); });

                    diagnosaList.forEach(function(d) {
                        if (d === 'Normal') {
                            dContainer.append('<span class="badge bg-success text-white px-3 py-2 rounded fs-6 fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Normal</span>');
                        } else if (d) {
                            dContainer.append('<span class="badge bg-danger text-white px-3 py-2 rounded fs-6 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> ' + d + '</span>');
                        }
                    });

                    // Update Rekomendasi Teks & Draf Saran Medis
                    $('#rekomendasi-teks').html('Berdasarkan diagnosa <strong>' + diagnosaText + '</strong> pada pasien, sistem menyarankan pemberian edukasi gaya hidup sehat dan pembatasan asupan risiko.');

                    const hasHipertensi = diagnosaList.includes('Hipertensi');
                    const hasDiabetes = diagnosaList.includes('Gula Darah Tinggi (DM)') || diagnosaList.includes('Diabetes Melitus');
                    const hasObesitas = diagnosaList.includes('Obesitas') || diagnosaList.includes('Overweight');

                    const manualPTM = [];
                    const autoList = ['Normal', 'Hipertensi', 'Gula Darah Tinggi (DM)', 'Diabetes Melitus', 'Obesitas', 'Overweight', 'Pre-Hipertensi', 'Gula Darah Batas', 'Hiperkolesterolemia'];
                    diagnosaList.forEach(d => {
                        if (d && !autoList.includes(d)) {
                            manualPTM.push(d);
                        }
                    });

                    // Logika Personalisasi Saran Medis Dinamis (Clinical Decision Support)
                    var namaPasien = selectedOpt.data('nama') || 'Pasien';
                    var tensiPasien = selectedOpt.data('tensi') || '-';
                    var gulaPasien = selectedOpt.data('gula') || '-';
                    var imtPasien = selectedOpt.data('imt') || '-';

                    let saranArray = [];
                    activeRecomJenis = "edukasi";

                    if (hasHipertensi) {
                        var templateHipertensi = [
                            "Berdasarkan tensi " + tensiPasien + " mmHg pada " + namaPasien + ", disarankan untuk melakukan diet DASH (rendah garam), membatasi asupan kafein dan makanan asin, serta rutin berolahraga aerobik minimal 30 menit sehari.",
                            "Tensi " + namaPasien + " terukur " + tensiPasien + " mmHg. Edukasikan pembatasan garam dapur (maksimal 1 sendok teh/hari), kelola stress, cukup tidur, dan ingatkan untuk kontrol tekanan darah setiap 2 minggu.",
                            "Catatan penanganan tensi " + tensiPasien + " mmHg untuk " + namaPasien + ": Edukasikan olahraga kardio ringan secara teratur, hindari makanan cepat saji/olahan bergaram tinggi, dan anjurkan pemantauan tensi berkala."
                        ];
                        var pembukaHipertensi = templateHipertensi[Math.floor(Math.random() * templateHipertensi.length)];
                        saranArray.push("- Hipertensi: " + pembukaHipertensi);
                    }

                    if (hasDiabetes) {
                        var templateDiabetes = [
                            "Mengingat kadar gula darah " + namaPasien + " mencapai " + gulaPasien + " mg/dL, berikan edukasi pembatasan asupan karbohidrat sederhana/manis, perbanyak serat dari sayuran, dan anjurkan jalan kaki rutin.",
                            "Hasil gula darah " + gulaPasien + " mg/dL pada " + namaPasien + " menunjukkan indikasi risiko. Sarankan diet rendah indeks glikemik, kurangi minuman manis kemasan, dan lakukan pemantauan kadar gula berkala.",
                            "Saran untuk " + namaPasien + " (Gula Darah: " + gulaPasien + " mg/dL): Konseling pola makan rendah gula, perbanyak aktivitas fisik harian, dan ingatkan untuk minum air putih yang cukup."
                        ];
                        var pembukaDiabetes = templateDiabetes[Math.floor(Math.random() * templateDiabetes.length)];
                        saranArray.push("- Gula Darah Tinggi: " + pembukaDiabetes);
                    }

                    if (hasObesitas) {
                        var templateObesitas = [
                            "Kategori IMT " + imtPasien + " pada " + namaPasien + " menunjukkan obesitas. Rekomendasikan porsi piring makan seimbang (50% sayur/buah), batasi konsumsi gorengan, dan lakukan aktivitas fisik pembakar kalori.",
                            "Evaluasi gizi " + namaPasien + " dengan IMT " + imtPasien + ": Edukasikan pengurangan asupan lemak jenuh, hindari ngemil larut malam, dan naikkan frekuensi aktivitas fisik minimal 150 menit seminggu.",
                            "Saran diet untuk " + namaPasien + " (IMT: " + imtPasien + "): Kontrol porsi makan harian, kurangi asupan bersantan dan digoreng, serta rutinkan olahraga terukur."
                        ];
                        var pembukaObesitas = templateObesitas[Math.floor(Math.random() * templateObesitas.length)];
                        saranArray.push("- Obesitas/Gizi: " + pembukaObesitas);
                    }

                    if (manualPTM.length > 0) {
                        activeRecomJenis = "rujukan";
                        saranArray.push("- Temuan PTM (" + manualPTM.join(', ') + "): Rujuk " + namaPasien + " ke fasilitas kesehatan tingkat lanjut (Rumah Sakit) untuk konsultasi dokter spesialis dan pemeriksaan penunjang yang lebih mendalam.");
                    }

                    if (saranArray.length === 0) {
                        activeRecomJenis = "anjuran_gaya_hidup";
                        var templateNormal = [
                            "Kondisi kesehatan " + namaPasien + " terpantau Normal. Pertahankan pola hidup sehat dengan konsumsi makanan bergizi seimbang, hindari asap rokok, cukup istirahat, dan lakukan skrining kesehatan tahunan.",
                            "Pemeriksaan " + namaPasien + " dalam batas Normal. Apresiasi pasien untuk tetap mempertahankan gaya hidup aktif, gizi seimbang, kelola stres, dan sarankan untuk skrining PTM secara berkala.",
                            "Hasil skrining " + namaPasien + " Normal. Berikan motivasi untuk konsisten menjaga pola makan sehat rendah lemak, olahraga teratur, dan lakukan pemeriksaan ulang tahun depan."
                        ];
                        var pembukaNormal = templateNormal[Math.floor(Math.random() * templateNormal.length)];
                        saranArray.push("- Normal: " + pembukaNormal);
                    }

                    activeRecomSaran = "Saran Rekomendasi Medis (Personalized):\n" + saranArray.join("\n\n");

                    // Bersihkan form input sebelumnya
                    if (jenisSelect) jenisSelect.value = "";
                    if (catatanText) catatanText.value = "";

                    // Tampilkan layout utama & sembunyikan placeholder
                    $('#placeholder-kosong').addClass('d-none');
                    $('#main-content-layout').removeClass('d-none');
                } else {
                    // Sembunyikan layout utama & tampilkan placeholder jika tidak ada pasien dipilih
                    $('#placeholder-kosong').removeClass('d-none');
                    $('#main-content-layout').addClass('d-none');
                    $('#deteksi_dini_id').val('');
                }
            });

            // -- Event Handler untuk Button Terapkan
            if (btnTerapkan) {
                btnTerapkan.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (jenisSelect) {
                        jenisSelect.value = activeRecomJenis;
                    }
                    if (catatanText) {
                        catatanText.value = activeRecomSaran;
                    }
                });
            }

            // Trigger change awal jika ada pre-selected value
            if ($('#deteksi_dini_id_select').val()) {
                $('#deteksi_dini_id_select').trigger('change');
            }
        });
    </script>
@endpush