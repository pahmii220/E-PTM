@extends('layouts.master')

@section('title', 'Riwayat Medis Pasien')

@section('content')
    <div class="container-fluid py-4" style="max-width:1400px; margin: auto;">

        {{-- ================= HEADER / NAV BAR ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <a href="{{ route('petugas.deteksi_dini.riwayat') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Pemeriksaan
                </a>
                <h3 class="fw-extrabold text-dark mb-0">
                    <i class="bi bi-file-earmark-medical-fill text-teal me-2"></i> Rekam Medis & Riwayat Pasien
                </h3>
            </div>
            
            <a href="{{ route('petugas.deteksi_dini.create', ['peserta_id' => $peserta->id]) }}" class="btn btn-teal btn-lg rounded-pill px-4 shadow-sm fw-bold text-white hover-up" style="background-color: #0f766e; border: none;">
                <i class="bi bi-heart-pulse-fill me-2"></i> Mulai Pemeriksaan Baru
            </a>
        </div>

        <div class="row g-4">

            {{-- ================= KOLOM KIRI: DETAIL PROFIL PASIEN ================= --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 24px;">
                    <div class="card-body p-4 text-center">
                        {{-- Avatar --}}
                        <div class="mx-auto bg-teal text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; background-color: #0f766e;">
                            <i class="bi bi-person-fill fs-1"></i>
                        </div>

                        <h4 class="fw-bold text-dark mb-1">{{ $peserta->nama_lengkap }}</h4>
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill mb-4" style="font-size: 0.85rem;">
                            No. RM: {{ $peserta->no_rekam_medis }}
                        </span>

                        <hr class="text-muted opacity-25">

                        {{-- Details List --}}
                        <div class="text-start mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="text-muted d-block">Nomor Induk Kependudukan (NIK)</small>
                                    <span class="fw-semibold text-dark">{{ $peserta->nik }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Jenis Kelamin</small>
                                    <span class="fw-semibold text-dark">
                                        <i class="bi bi-gender-{{ $peserta->jenis_kelamin === 'Laki-laki' ? 'male text-primary' : 'female text-pink' }} me-1"></i>
                                        {{ $peserta->jenis_kelamin }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Umur</small>
                                    <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($peserta->tanggal_lahir)->age }} Tahun</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Tempat, Tanggal Lahir</small>
                                    <span class="fw-semibold text-dark">{{ $peserta->tempat_lahir }}, {{ \Carbon\Carbon::parse($peserta->tanggal_lahir)->format('d F Y') }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Pekerjaan</small>
                                    <span class="fw-semibold text-dark">{{ $peserta->pekerjaan }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Kecamatan Domisili</small>
                                    <span class="fw-semibold text-dark">Kec. {{ $peserta->kecamatan }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Alamat Lengkap</small>
                                    <span class="fw-semibold text-dark">{{ $peserta->alamat }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Nomor HP / Kontak</small>
                                    <span class="fw-semibold text-dark">
                                        <i class="bi bi-telephone-fill text-success me-1"></i> {{ $peserta->kontak }}
                                    </span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block">Puskesmas Terdaftar</small>
                                    <span class="fw-semibold text-dark">
                                        <i class="bi bi-hospital text-teal me-1"></i> {{ $peserta->puskesmas->nama_puskesmas ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= KOLOM KANAN: TIMELINE KUNJUNGAN ================= --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-clock-history text-teal me-2"></i> Garis Waktu Riwayat Pemeriksaan PTM
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        @if($riwayatKunjungan->isEmpty())
                            {{-- EMPTY STATE --}}
                            <div class="text-center py-5">
                                <div class="bg-teal-subtle text-teal rounded-circle p-3 d-inline-flex mb-3" style="background-color: #f0fdfa;">
                                    <i class="bi bi-clipboard-x fs-1 text-teal"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Belum Ada Riwayat Skrining</h5>
                                <p class="text-muted max-w-md mx-auto" style="max-width: 400px;">
                                    Pasien ini belum pernah melakukan pemeriksaan fisik deteksi dini PTM. Klik tombol di bawah untuk membuat pemeriksaan pertama.
                                </p>
                                <a href="{{ route('petugas.deteksi_dini.create', ['peserta_id' => $peserta->id]) }}" class="btn btn-teal rounded-pill px-4 shadow-sm" style="background-color: #0f766e; color: white;">
                                    Input Skrining Perdana
                                </a>
                            </div>
                        @else
                            {{-- TIMELINE CONTAINER --}}
                            <div class="position-relative" style="padding-left: 2.75rem; min-height: 200px;">
                                
                                {{-- Linea Vertikal Timeline --}}
                                <div class="position-absolute h-100" style="left: 16px; top: 0; width: 3px; background-color: #cbd5e1; z-index: 1;"></div>

                                @foreach($riwayatKunjungan as $key => $kunjungan)
                                    <div class="position-relative mb-5" style="z-index: 2;">
                                        
                                        {{-- Timeline Node Badge/Dot (Mathematically centered on the 16px line) --}}
                                        <div class="position-absolute rounded-circle bg-teal text-white d-flex align-items-center justify-content-center shadow-sm" 
                                             style="left: -37px; top: 4px; width: 20px; height: 20px; background-color: #0f766e; border: 4px solid #ffffff; z-index: 10;">
                                        </div>

                                        {{-- Header Kunjungan --}}
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3" style="overflow: visible !important;">
                                            <div style="overflow: visible !important;">
                                                <div class="fw-bold text-dark" style="font-size: 1.2rem !important; line-height: 1.4 !important; overflow: visible !important;">
                                                    {{ \Carbon\Carbon::parse($kunjungan->tanggal_pemeriksaan)->format('d F Y') }}
                                                </div>
                                            </div>
                                            
                                            {{-- Badges Diagnosa --}}
                                            <div class="d-flex gap-1 flex-wrap">
                                                @php
                                                    $diags = array_map('trim', explode(',', $kunjungan->diagnosa_penyakit ?? ''));
                                                @endphp
                                                @foreach($diags as $d)
                                                    @if($d === 'Normal')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill">Normal</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill">{{ $d }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Konten Hasil Pemeriksaan --}}
                                        <div class="card border border-slate-100 rounded-3 shadow-sm mb-3" style="margin-top: 1.25rem !important; background-color: #f8fafc; border: 1px solid #cbd5e1 !important;">
                                            <div class="card-body p-3">
                                                
                                                <div class="row g-3 text-dark">
                                                    {{-- Vital Stats --}}
                                                    <div class="col-md-7 border-end border-slate-200/60">
                                                        <span class="d-block fw-bold text-muted uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Hasil Klinis & Antropometri</span>
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block" style="font-size: 0.8rem;">Tekanan Darah</small>
                                                                <span class="fw-bold">{{ $kunjungan->tekanan_darah }}</span> <small class="text-muted">mmHg</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block" style="font-size: 0.8rem;">Gula Darah</small>
                                                                <span class="fw-bold">{{ $kunjungan->gula_darah ?? '-' }}</span> <small class="text-muted">mg/dL</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block" style="font-size: 0.8rem;">Kolesterol</small>
                                                                <span class="fw-bold">{{ $kunjungan->kolesterol ?? '-' }}</span> <small class="text-muted">mg/dL</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block" style="font-size: 0.8rem;">IMT</small>
                                                                <span class="fw-bold">{{ $kunjungan->imt }}</span> <small class="text-muted">({{ $kunjungan->berat_badan }}kg / {{ $kunjungan->tinggi_badan }}cm)</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Pola Hidup / Faktor Risiko --}}
                                                    <div class="col-md-5">
                                                        <span class="d-block fw-bold text-muted uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Faktor Risiko Pola Hidup</span>
                                                        <div class="row g-2 text-dark" style="font-size: 0.85rem;">
                                                            <div class="col-12">
                                                                <strong>Merokok:</strong> 
                                                                <span class="text-{{ optional($kunjungan->faktor_risiko)->merokok === 'Ya' ? 'danger' : 'success' }} fw-semibold">
                                                                    {{ optional($kunjungan->faktor_risiko)->merokok ?? '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="col-12">
                                                                <strong>Kurang Aktivitas Fisik:</strong> 
                                                                <span class="text-{{ optional($kunjungan->faktor_risiko)->kurang_aktivitas_fisik === 'Ya' ? 'danger' : 'success' }} fw-semibold">
                                                                    {{ optional($kunjungan->faktor_risiko)->kurang_aktivitas_fisik ?? '-' }}
                                                                </span>
                                                            </div>
                                                            <div class="col-12">
                                                                <strong>Riwayat Keluarga:</strong> 
                                                                <span class="text-{{ optional($kunjungan->faktor_risiko)->riwayat_keluarga === 'Ya' ? 'warning' : 'success' }} fw-semibold">
                                                                    {{ optional($kunjungan->faktor_risiko)->riwayat_keluarga ?? '-' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        {{-- Bersarang: Tindak Lanjut --}}
                                        @if($kunjungan->tindakLanjut)
                                            <div class="card border border-teal-100 rounded-3 shadow-sm" style="background-color: #f0fdfa; border-left: 4px solid #0d9488 !important;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="fw-bold text-teal mb-0">
                                                            <i class="bi bi-clipboard2-pulse-fill me-1"></i> Tindak Lanjut: 
                                                            @php
                                                                $jenisMap = [
                                                                    'edukasi' => 'Edukasi / Penyuluhan',
                                                                    'anjuran_gaya_hidup' => 'Anjuran Gaya Hidup',
                                                                    'rujukan' => 'Rujukan Faskes Lanjutan',
                                                                    'monitoring' => 'Monitoring Berkala',
                                                                    'tidak_ada' => 'Tidak Ada Tindakan'
                                                                ];
                                                            @endphp
                                                            {{ $jenisMap[$kunjungan->tindakLanjut->jenis_tindak_lanjut] ?? $kunjungan->tindakLanjut->jenis_tindak_lanjut }}
                                                        </h6>
                                                        
                                                        <span class="badge bg-{{ $kunjungan->tindakLanjut->status_tindak_lanjut === 'sudah' ? 'success' : 'warning' }} px-2 py-1 rounded">
                                                            {{ $kunjungan->tindakLanjut->status_tindak_lanjut === 'sudah' ? 'Sudah Dilakukan' : 'Belum Dilakukan' }}
                                                        </span>
                                                    </div>
                                                    
                                                    <p class="mb-0 text-dark small leading-relaxed" style="white-space: pre-wrap;">{{ $kunjungan->tindakLanjut->catatan_petugas }}</p>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-teal-100/50 text-muted" style="font-size: 0.75rem;">
                                                        <span>
                                                            <i class="bi bi-calendar-event me-1"></i> Rencana Tindak Lanjut: {{ \Carbon\Carbon::parse($kunjungan->tindakLanjut->tanggal_tindak_lanjut)->format('d-m-Y') }}
                                                        </span>
                                                        <a href="{{ route('petugas.tindak_lanjut.edit', $kunjungan->tindakLanjut->id) }}" class="text-teal fw-bold text-decoration-none">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-between align-items-center py-2.5 px-3 border-0 rounded-3 shadow-sm" style="color: #664d03; background-color: #fff3cd; border: 1px solid #ffecb5 !important;">
                                                <span class="small fw-semibold"><i class="bi bi-exclamation-circle me-1"></i> Pasien ini belum memiliki catatan tindak lanjut untuk pemeriksaan ini.</span>
                                                <a href="{{ route('petugas.tindak_lanjut.create', $kunjungan->id) }}" class="btn btn-warning btn-sm fw-bold rounded-2 px-3">
                                                    <i class="bi bi-plus-lg me-1"></i> Buat Tindak Lanjut
                                                </a>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach

                            </div>
                        @endif

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

        .text-teal {
            color: #0f766e;
        }

        .bg-teal {
            background-color: #0f766e;
        }

        .bg-pink {
            background-color: #ec4899;
        }

        /* Hover animation for buttons */
        .hover-up {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-up:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2) !important;
        }
    </style>
@endpush
