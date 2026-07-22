@extends('layouts.master')

@section('title', 'Halaman Laporan')

@section('content')

                    {{--
                    ==========================================================================
                    LOGIKA QUERY DATA (Self-contained agar tidak perlu ubah Controller/Routes)
                    ==========================================================================
                    --}}
                    @php
    $tab = request('tab'); // Tab laporan aktif
    $puskesmasId = request('puskesmas_id', 'all');
    $status = request('status', 'all');
    $search = request('search');

    // Mengambil daftar puskesmas untuk filter dropdown
    $puskesmasList = \App\Models\Puskesmas::all();

    // Inisialisasi variabel penampung data
    $data = null;
    $usiaData = [];

    // Query berdasarkan tab yang dipilih
    if ($tab === 'peserta') {
        $query = \App\Models\Peserta::with('puskesmas')->orderBy('dibuat_pada', 'desc');
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_rekam_medis', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(10)->appends(request()->query());

    } elseif ($tab === 'kegiatan') {
        $query = \App\Models\Kegiatan::orderBy('tanggal', 'desc');
        if ($search) {
            $query->where('nama_kegiatan', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%");
        }
        $data = $query->paginate(10)->appends(request()->query());

    } elseif ($tab === 'deteksi') {
        $query = \App\Models\DeteksiDiniPTM::with(['peserta', 'puskesmas'])->orderBy('tanggal_pemeriksaan', 'desc');
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }
        if ($search) {
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_rekam_medis', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(10)->appends(request()->query());

    } elseif ($tab === 'faktor') {
        $query = \App\Models\FaktorResikoPTM::with(['peserta', 'puskesmas'])->orderBy('dibuat_pada', 'desc');
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }
        if ($search) {
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(10)->appends(request()->query());

    } elseif ($tab === 'tindak_lanjut') {
        $query = \App\Models\TindakLanjutPTM::with(['peserta', 'puskesmas'])->orderBy('tanggal_tindak_lanjut', 'desc');
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }
        if ($search) {
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }
        $data = $query->paginate(10)->appends(request()->query());

    } elseif ($tab === 'status_ptm') {
        $data = \App\Models\DeteksiDiniPTM::selectRaw('hasil_skrining, COUNT(*) as jumlah')
            ->groupBy('hasil_skrining')
            ->get();

    } elseif ($tab === 'puskesmas') {
        $data = \App\Models\Puskesmas::withCount([
            'peserta as total_peserta',
            'deteksiDini as total_deteksi',
            'faktorResiko as total_faktor',
        ])
            ->orderBy('nama_puskesmas', 'asc')
            ->get();

    } elseif ($tab === 'evaluasi') {
        $semuaData = \App\Models\EvaluasiSus::with('user')->orderBy('created_at', 'desc')->get();
        $totalResponden = $semuaData->count();
        $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

        $predikat = 'Kurang Baik';
        $predikatColor = 'danger';
        if ($rataRataSkor >= 80.8) {
            $predikat = 'Excellent (Sangat Mudah)';
            $predikatColor = 'success';
        } elseif ($rataRataSkor >= 71.4) {
            $predikat = 'Good (Mudah Digunakan)';
            $predikatColor = 'primary';
        } elseif ($rataRataSkor >= 50.9) {
            $predikat = 'Acceptable (Cukup Layak)';
            $predikatColor = 'warning';
        }
        $data = $semuaData;

    } elseif ($tab === 'usia') {

        $pesertas = \App\Models\Peserta::all();
        $usiaData = [
            'remaja' => 0,
            'dewasa' => 0,
            'pra_lansia' => 0,
            'lansia' => 0
        ];



        foreach ($pesertas as $p) {
            if (!$p->tanggal_lahir)
                continue;
            $umur = \Carbon\Carbon::parse($p->tanggal_lahir)->age;
            if ($umur < 18) {
                $usiaData['remaja']++;
            } elseif ($umur <= 44) {
                $usiaData['dewasa']++;
            } elseif ($umur <= 59) {
                $usiaData['pra_lansia']++;
            } else {
                $usiaData['lansia']++;
            }
        }
    }

                    @endphp

                    <div class="container py-4">

                        {{-- ================= HEADER HALAMAN ================= --}}
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-slate-800 mb-1">
                                <i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>
                                Pusat Dokumen & Laporan PTM
                            </h4>
                            <p class="text-muted small mb-0">
                                Pilih kategori laporan untuk memuat preview data secara analitis dan mencetak lembar laporan resmi.
                            </p>
                        </div>

                        {{-- ================= KATEGORI 1: LAPORAN REGISTRASI & MASTER ================= --}}
                        <div class="mb-4">
                            <div class="border-bottom pb-1 mb-3">
                                <span class="fw-bold text-primary small text-uppercase">
                                    <i class="bi bi-folder-fill me-1"></i> Laporan Registrasi & Master
                                </span>
                            </div>
                            <div class="row g-3">

                                <!-- KEGIATAN PTM -->
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="?tab=kegiatan#preview-section" class="text-decoration-none">
                                        <div
                                            class="report-card d-flex align-items-center gap-3 {{ $tab === 'kegiatan' ? 'active-card border-dark' : '' }}">
                                            <div class="icon-box bg-dark-soft flex-shrink-0">
                                                <i class="bi bi-calendar-event text-dark"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Laporan Kegiatan PTM</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.2;">Rekap pelaksanaan
                                                    kegiatan</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- ================= KATEGORI 2: LAPORAN SKRINING & KLINIS ================= --}}
                        <div class="mb-4">
                            <div class="border-bottom pb-1 mb-3">
                                <span class="fw-bold text-danger small text-uppercase">
                                    <i class="bi bi-folder-fill me-1"></i> Laporan Skrining & Medis
                                </span>
                            </div>
                            <div class="row g-3">

                                <!-- HASIL SKRINING PTM -->
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="?tab=status_ptm#preview-section" class="text-decoration-none">
                                        <div
                                            class="report-card d-flex align-items-center gap-3 {{ $tab === 'status_ptm' ? 'active-card border-secondary' : '' }}">
                                            <div class="icon-box bg-secondary-soft flex-shrink-0">
                                                <i class="bi bi-clipboard-pulse text-secondary"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Hasil Skrining PTM</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.2;">Rekap hasil status
                                                    skrining</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- ================= KATEGORI 3: LAPORAN ANALITIS & ESTIMASI TREN ================= --}}
                        <div class="mb-4">
                            <div class="border-bottom pb-1 mb-3">
                                <span class="fw-bold text-info-emphasis small text-uppercase">
                                    <i class="bi bi-folder-fill me-1 text-info"></i> Laporan Analitis & Estimasi Tren
                                </span>
                            </div>
                            <div class="row g-3">
                                <!-- REKAP PUSKESMAS -->
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="?tab=puskesmas#preview-section" class="text-decoration-none">
                                        <div
                                            class="report-card d-flex align-items-center gap-3 {{ $tab === 'puskesmas' ? 'active-card border-info' : '' }}">
                                            <div class="icon-box bg-info-soft flex-shrink-0">
                                                <i class="bi bi-bar-chart-fill text-info"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Rekap Puskesmas</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.2;">Rekap data PTM per
                                                    puskesmas</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- KELOMPOK USIA -->
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="?tab=usia#preview-section" class="text-decoration-none">
                                        <div
                                            class="report-card d-flex align-items-center gap-3 {{ $tab === 'usia' ? 'active-card border-purple' : '' }}">
                                            <div class="icon-box bg-purple-soft flex-shrink-0">
                                                <i class="bi bi-person-lines-fill text-purple"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">PTM Kelompok Usia</h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.2;">Rekap berdasarkan
                                                    kelompok usia</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!-- LAPORAN EVALUASI SISTEM -->
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <a href="?tab=evaluasi#preview-section" class="text-decoration-none">
                                        <div
                                            class="report-card d-flex align-items-center gap-3 {{ $tab === 'evaluasi' ? 'active-card border-teal' : '' }}">
                                            <div class="icon-box bg-teal-soft flex-shrink-0">
                                                <i class="bi bi-patch-check-fill text-teal"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 13.5px;">Evaluasi Sistem Oleh Pegawai </h6>
                                                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.2;">Rekap penilaian usability sistem
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>


                        {{-- ========================================================================== --}}
                        {{-- SECTION PREVIEW DATA DAN FILTER --}}
                        {{-- ========================================================================== --}}
                        <div id="preview-section" class="pt-2 mb-5">
                            @if(empty($tab))
                                {{-- TAMPILAN JIKA BELUM ADA TAB YANG DIPILIH --}}
                                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-light">
                                    <i class="bi bi-arrow-up-circle text-success mb-2" style="font-size: 40px;"></i>
                                    <h6 class="fw-bold text-dark mb-1">Silakan Pilih Laporan</h6>
                                    <p class="mb-0 small">Klik pada salah satu tombol laporan di atas untuk menampilkan preview data dan
                                        mencetak.</p>
                                </div>
                            @else
                                                    {{-- HEADER PREVIEW --}}
                                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                                                        <div
                                                            class="card-header bg-success text-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                            <div>
                                                                <h5 class="fw-bold mb-0" style="font-size: 15px;">
                                                                    <i class="bi bi-eye-fill me-2"></i>
                                                                    Preview:
                                                                    @if($tab === 'peserta') Laporan Peserta
                                                                    @elseif($tab === 'kegiatan') Laporan Kegiatan PTM
                                                                    @elseif($tab === 'deteksi') Laporan Deteksi Dini
                                                                    @elseif($tab === 'faktor') Laporan Faktor Risiko
                                                                    @elseif($tab === 'tindak_lanjut') Laporan Tindak Lanjut
                                                                    @elseif($tab === 'status_ptm') Laporan Hasil Skrining
                                                                    @elseif($tab === 'evaluasi') Laporan Evaluasi Sistem (SUS)
                                                                    @elseif($tab === 'puskesmas') Rekap Puskesmas
                                                                    @elseif($tab === 'usia') PTM Berdasarkan Kelompok Usia
                                                                    @endif
                                                                </h5>
                                                                <small class="opacity-75" style="font-size: 11px;">Tabel di bawah menampilkan preview baris data
                                                                    terbaru sebelum dicetak.</small>
                                                            </div>

                                                            {{-- TOMBOL PRINT DINAMIS --}}
                                                            <div>
                                                                @if($tab === 'peserta')
                                                                    <a href="{{ route('pengguna.verifikasi.print.peserta', ['status' => $status, 'puskesmas_id' => $puskesmasId]) }}"
                                                                        target="_blank" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"
                                                                        style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'kegiatan')
                                                                    <a href="{{ route('pengguna.laporan.kegiatan') }}" target="_blank"
                                                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'deteksi')
                                                                    <a href="{{ route('pengguna.verifikasi.print.deteksi', ['status' => $status, 'puskesmas_id' => $puskesmasId]) }}"
                                                                        target="_blank" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"
                                                                        style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'faktor')
                                                                    <a href="{{ route('pengguna.verifikasi.print.faktor', ['status' => $status, 'puskesmas_id' => $puskesmasId]) }}"
                                                                        target="_blank" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3"
                                                                        style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'tindak_lanjut')
                                                                    <a href="{{ route('pengguna.verifikasi.print.tindak_lanjut') }}" target="_blank"
                                                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'status_ptm')
                                                                    <a href="{{ route('pengguna.laporan.status_ptm') }}" target="_blank"
                                                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'puskesmas')
                                                                    <a href="{{ route('pengguna.rekap.puskesmas.print') }}" target="_blank"
                                                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                @elseif($tab === 'usia')
                                                                    <a href="{{ route('pengguna.laporan.kelompok_usia.print') }}" target="_blank"
                                                                        class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                                                        <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                                                    </a>
                                                                    @elseif($tab === 'evaluasi')
                                <a href="{{ route('pengguna.evaluasi.cetak') }}" target="_blank" class="btn btn-warning btn-sm rounded-pill fw-bold shadow-sm px-3" style="font-size: 12px;">
                                    <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
                                </a>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- FILTER BAR (Hanya muncul jika tab membutuhkan filter) --}}
                                                        @if(in_array($tab, ['peserta', 'kegiatan', 'deteksi', 'faktor', 'tindak_lanjut']))
                                                            <div class="card-body bg-light border-bottom py-2 px-3">
                                                                <form method="GET" class="row g-2 align-items-end">
                                                                    <input type="hidden" name="tab" value="{{ $tab }}">

                                                                    @if(in_array($tab, ['peserta', 'deteksi', 'faktor', 'tindak_lanjut']))
                                                                        {{-- Filter Puskesmas --}}
                                                                        <div class="col-md-3 col-sm-6">
                                                                            <label class="form-label mb-1 text-muted"
                                                                                style="font-size: 11px; font-weight: 600;">Puskesmas</label>
                                                                            <select name="puskesmas_id" class="form-select form-select-sm rounded-3 shadow-sm border-0"
                                                                                onchange="this.form.submit()" style="font-size: 12px;">
                                                                                <option value="all">Semua Puskesmas</option>
                                                                                @foreach($puskesmasList as $p)
                                                                                    <option value="{{ $p->id }}" {{ $puskesmasId == $p->id ? 'selected' : '' }}>
                                                                                        {{ $p->nama_puskesmas }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    @endif

                                                                    @if(in_array($tab, ['peserta', 'deteksi', 'faktor']))
                                                                        {{-- Filter Status Verifikasi --}}
                                                                        <div class="col-md-3 col-sm-6">
                                                                            <label class="form-label mb-1 text-muted" style="font-size: 11px; font-weight: 600;">Status
                                                                                Data</label>
                                                                            <select name="status" class="form-select form-select-sm rounded-3 shadow-sm border-0"
                                                                                onchange="this.form.submit()" style="font-size: 12px;">
                                                                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                                                                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Tertunda</option>
                                                                                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Diterima</option>
                                                                                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                                                            </select>
                                                                        </div>
                                                                    @endif

                                                                    {{-- Kolom Pencarian --}}
                                                                    <div class="col-md-4 col-sm-8">
                                                                        <label class="form-label mb-1 text-muted" style="font-size: 11px; font-weight: 600;">Cari
                                                                            Data</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" name="search" class="form-control rounded-start-3 shadow-sm border-0"
                                                                                placeholder="Ketik kata kunci..." value="{{ $search }}" style="font-size: 12px;">
                                                                            <button class="btn btn-primary rounded-end-3 px-3 shadow-sm" type="submit">
                                                                                <i class="bi bi-search"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Tombol Reset Filter --}}
                                                                    <div class="col-md-2 col-sm-4">
                                                                        <a href="?tab={{ $tab }}#preview-section"
                                                                            class="btn btn-outline-secondary btn-sm rounded-3 w-100 shadow-sm"
                                                                            style="font-size: 12px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                                                        </a>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endif

                                                        {{-- TABEL DATA PREVIEW --}}
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">

                                                                {{-- 1. TABEL LAPORAN PESERTA --}}
                                                                @if($tab === 'peserta')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 60px;">No</th>
                                                                                <th>NIK</th>
                                                                                <th>Nama Lengkap</th>
                                                                                <th>No. Rekam Medis</th>
                                                                                <th>Puskesmas</th>
                                                                                <th class="text-center">Status</th>
                                                                                <th class="pe-4 text-end">Tanggal Daftar</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="ps-4 text-muted">{{ $data->firstItem() + $i }}</td>
                                                                                    <td class="fw-semibold text-dark">{{ $row->nik ?? '-' }}</td>
                                                                                    <td>{{ $row->nama_lengkap }}</td>
                                                                                    <td><span
                                                                                            class="badge bg-secondary-soft text-dark px-2 rounded">{{ $row->no_rekam_medis ?? '-' }}</span>
                                                                                    </td>
                                                                                    <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                                                                    <td class="text-center">
                                                                                        @if($row->status_verifikasi === 'approved')
                                                                                            <span class="badge bg-success rounded-pill px-3">Diterima</span>
                                                                                        @elseif($row->status_verifikasi === 'rejected')
                                                                                            <span class="badge bg-danger rounded-pill px-3">Ditolak</span>
                                                                                        @else
                                                                                            <span class="badge bg-warning text-dark rounded-pill px-3">Tertunda</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="pe-4 text-end text-muted">
                                                                                        {{ $row->dibuat_pada ? \Carbon\Carbon::parse($row->dibuat_pada)->format('d-m-Y') : '-' }}
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada data peserta ditemukan.
                                                                                    </td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 2. TABEL LAPORAN KEGIATAN PTM --}}
                                                                @elseif($tab === 'kegiatan')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 60px;">No</th>
                                                                                <th>Nama Kegiatan</th>
                                                                                <th>Jenis Kegiatan</th>
                                                                                <th>Lokasi</th>
                                                                                <th class="text-center">Jumlah Peserta</th>
                                                                                <th>Keterangan</th>
                                                                                <th class="pe-4 text-end">Tanggal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="ps-4 text-muted">{{ $data->firstItem() + $i }}</td>
                                                                                    <td class="fw-bold text-dark">{{ $row->nama_kegiatan }}</td>
                                                                                    <td><span
                                                                                            class="badge bg-info-soft text-dark px-3 rounded-pill">{{ $row->jenis_kegiatan }}</span>
                                                                                    </td>
                                                                                    <td>{{ $row->lokasi }}</td>
                                                                                    <td class="text-center fw-semibold text-primary">{{ $row->jumlah_peserta ?? '-' }} orang
                                                                                    </td>
                                                                                    <td class="text-muted text-truncate" style="max-width: 250px;">
                                                                                        {{ $row->keterangan ?? '-' }}</td>
                                                                                    <td class="pe-4 text-end text-muted">
                                                                                        {{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') : '-' }}
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada data kegiatan ditemukan.
                                                                                    </td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 3. TABEL LAPORAN DETEKSI DINI --}}
                                                                @elseif($tab === 'deteksi')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 60px;">No</th>
                                                                                <th>Nama Pasien</th>
                                                                                <th class="text-center">Tekanan Darah</th>
                                                                                <th class="text-center">Gula Darah</th>
                                                                                <th>Puskesmas</th>
                                                                                <th class="text-center">Status</th>
                                                                                <th class="pe-4 text-end">Tanggal Periksa</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="ps-4 text-muted">{{ $data->firstItem() + $i }}</td>
                                                                                    <td class="fw-semibold text-dark">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                                                                    <td class="text-center fw-semibold text-danger">{{ $row->tekanan_darah ?? '-' }} mmHg
                                                                                    </td>
                                                                                    <td class="text-center fw-semibold text-warning">{{ $row->gula_darah ?? '-' }} mg/dL
                                                                                    </td>
                                                                                    <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                                                                    <td class="text-center">
                                                                                        @if($row->status_verifikasi === 'approved')
                                                                                            <span class="badge bg-success rounded-pill px-3">Diterima</span>
                                                                                        @elseif($row->status_verifikasi === 'rejected')
                                                                                            <span class="badge bg-danger rounded-pill px-3">Ditolak</span>
                                                                                        @else
                                                                                            <span class="badge bg-warning text-dark rounded-pill px-3">Tertunda</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="pe-4 text-end text-muted">
                                                                                        {{ $row->tanggal_pemeriksaan ? \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') : '-' }}
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="7" class="text-center py-5 text-muted">Tidak ada data deteksi dini
                                                                                        ditemukan.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 4. TABEL LAPORAN FAKTOR RISIKO --}}
                                                                @elseif($tab === 'faktor')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 60px;">No</th>
                                                                                <th>Nama Pasien</th>
                                                                                <th class="text-center">Merokok</th>
                                                                                <th class="text-center">Konsumsi Alkohol</th>
                                                                                <th class="text-center">Kurang Fisik</th>
                                                                                <th>Puskesmas</th>
                                                                                <th class="text-center">Status</th>
                                                                                <th class="pe-4 text-end">Tanggal Periksa</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="ps-4 text-muted">{{ $data->firstItem() + $i }}</td>
                                                                                    <td class="fw-semibold text-dark">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                                                                    <td class="text-center">
                                                                                        <span
                                                                                            class="badge {{ strtolower($row->merokok) === 'ya' ? 'bg-danger' : 'bg-success' }} px-3 rounded-pill">{{ $row->merokok ?? '-' }}</span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span
                                                                                            class="badge {{ strtolower($row->alkohol) === 'ya' ? 'bg-danger' : 'bg-success' }} px-3 rounded-pill">{{ $row->alkohol ?? '-' }}</span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <span
                                                                                            class="badge {{ strtolower($row->kurang_fisik) === 'ya' ? 'bg-danger' : 'bg-success' }} px-3 rounded-pill">{{ $row->kurang_fisik ?? '-' }}</span>
                                                                                    </td>
                                                                                    <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                                                                    <td class="text-center">
                                                                                        @if($row->status_verifikasi === 'approved')
                                                                                            <span class="badge bg-success rounded-pill px-3">Diterima</span>
                                                                                        @elseif($row->status_verifikasi === 'rejected')
                                                                                            <span class="badge bg-danger rounded-pill px-3">Ditolak</span>
                                                                                        @else
                                                                                            <span class="badge bg-warning text-dark rounded-pill px-3">Tertunda</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="pe-4 text-end text-muted">
                                                                                        {{ $row->tanggal_pemeriksaan ? \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') : '-' }}
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="8" class="text-center py-5 text-muted">Tidak ada data faktor risiko
                                                                                        ditemukan.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 5. TABEL LAPORAN TINDAK LANJUT --}}
                                                                @elseif($tab === 'tindak_lanjut')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th class="ps-4" style="width: 60px;">No</th>
                                                                                <th>Nama Pasien</th>
                                                                                <th>Jenis Tindak Lanjut</th>
                                                                                <th>Keterangan Petugas</th>
                                                                                <th>Puskesmas</th>
                                                                                <th class="pe-4 text-end">Tanggal Tindak Lanjut</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="ps-4 text-muted">{{ $data->firstItem() + $i }}</td>
                                                                                    <td class="fw-semibold text-dark">{{ optional($row->peserta)->nama_lengkap ?? '-' }}</td>
                                                                                    <td><span
                                                                                            class="badge bg-info text-dark px-3 rounded-pill">{{ ucwords(str_replace('_', ' ', $row->jenis_tindak_lanjut)) }}</span>
                                                                                    </td>
                                                                                    <td class="text-muted">{{ $row->catatan_petugas ?? '-' }}</td>
                                                                                    <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                                                                    <td class="pe-4 text-end text-muted">
                                                                                        {{ $row->tanggal_tindak_lanjut ? \Carbon\Carbon::parse($row->tanggal_tindak_lanjut)->format('d-m-Y') : '-' }}
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="6" class="text-center py-5 text-muted">Tidak ada data tindak lanjut
                                                                                        ditemukan.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 6. TABEL HASIL SKRINING PTM --}}
                                                                @elseif($tab === 'status_ptm')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light text-center">
                                                                            <tr>
                                                                                <th style="width: 80px;">No</th>
                                                                                <th class="text-start ps-5">Status Kesehatan (Hasil Skrining)</th>
                                                                                <th style="width: 250px;">Jumlah Peserta</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr>
                                                                                    <td class="text-center text-muted">{{ $i + 1 }}</td>
                                                                                    <td class="fw-bold ps-5 text-start">
                                                                                        {{ $row->hasil_skrining ?? 'Tidak Teridentifikasi' }}</td>
                                                                                    <td class="text-center text-primary fw-bold" style="font-size: 14px;">{{ $row->jumlah }}
                                                                                        orang</td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="3" class="text-center py-5 text-muted">Tidak ada data hasil skrining.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                            <tr class="table-light fw-bold text-center">
                                                                                <td colspan="2" class="text-end pe-5">Total Keseluruhan Pemeriksaan:</td>
                                                                                <td class="text-primary" style="font-size: 15px;">{{ $data->sum('jumlah') }} orang</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 7. TABEL REKAP PUSKESMAS --}}
                                                                @elseif($tab === 'puskesmas')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light text-center">
                                                                            <tr>
                                                                                <th style="width: 80px;">No</th>
                                                                                <th class="text-start ps-4">Nama Puskesmas</th>
                                                                                <th>Jumlah Peserta Terdaftar</th>
                                                                                <th>Jumlah Pemeriksaan Deteksi</th>
                                                                                <th>Jumlah Pengisian Faktor Risiko</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($data as $i => $row)
                                                                                <tr class="text-center">
                                                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                                                    <td class="text-start ps-4 fw-bold text-dark">{{ $row->nama_puskesmas }}</td>
                                                                                    <td><span
                                                                                            class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-bold">{{ $row->total_peserta }}</span>
                                                                                    </td>
                                                                                    <td><span
                                                                                            class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill fw-bold">{{ $row->total_deteksi }}</span>
                                                                                    </td>
                                                                                    <td><span
                                                                                            class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-bold">{{ $row->total_faktor }}</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr>
                                                                                    <td colspan="5" class="text-center py-5 text-muted">Tidak ada data rekap puskesmas.</td>
                                                                                </tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>

                                                                    {{-- 8. TABEL KELOMPOK USIA --}}
                                                                @elseif($tab === 'usia')
                                                                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                        <thead class="table-light text-center">
                                                                            <tr>
                                                                                <th style="width: 80px;">No</th>
                                                                                <th class="text-start ps-4">Kategori Usia</th>
                                                                                <th>Rentang Umur (Tahun)</th>
                                                                                <th style="width: 250px;">Jumlah Peserta</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr class="text-center">
                                                                                <td class="text-muted">1</td>
                                                                                <td class="text-start ps-4 fw-bold">Remaja</td>
                                                                                <td>&lt; 18 Tahun</td>
                                                                                <td class="text-primary fw-bold" style="font-size: 14px;">{{ $usiaData['remaja'] }}
                                                                                    orang</td>
                                                                            </tr>
                                                                            <tr class="text-center">
                                                                                <td class="text-muted">2</td>
                                                                                <td class="text-start ps-4 fw-bold">Dewasa</td>
                                                                                <td>18 - 44 Tahun</td>
                                                                                <td class="text-primary fw-bold" style="font-size: 14px;">{{ $usiaData['dewasa'] }}
                                                                                    orang</td>
                                                                            </tr>
                                                                            <tr class="text-center">
                                                                                <td class="text-muted">3</td>
                                                                                <td class="text-start ps-4 fw-bold">Pra Lansia</td>
                                                                                <td>45 - 59 Tahun</td>
                                                                                <td class="text-primary fw-bold" style="font-size: 14px;">{{ $usiaData['pra_lansia'] }}
                                                                                    orang</td>
                                                                            </tr>
                                                                            <tr class="text-center">
                                                                                <td class="text-muted">4</td>
                                                                                <td class="text-start ps-4 fw-bold">Lansia</td>
                                                                                <td>&ge; 60 Tahun</td>
                                                                                <td class="text-primary fw-bold" style="font-size: 14px;">{{ $usiaData['lansia'] }}
                                                                                    orang</td>
                                                                            </tr>
                                                                            <tr class="table-light fw-bold text-center">
                                                                                <td colspan="3" class="text-end pe-5">Total Peserta Teridentifikasi Usia:</td>
                                                                                <td class="text-primary" style="font-size: 15px;">{{ array_sum($usiaData) }} orang</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    {{-- 9. TABEL EVALUASI SISTEM (SUS) --}}
                                                                @elseif($tab === 'evaluasi')
                                                                        {{-- RINGKASAN SKOR SUS --}}
                                                                        <div class="px-4 py-3 bg-light border-bottom d-flex align-items-center gap-4 flex-wrap">
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <div class="rounded-3 px-3 py-2 text-center" style="background: rgba(25,135,84,0.08); min-width: 90px;">
                                                                                    <div class="fw-bold text-success" style="font-size: 26px; line-height: 1;">{{ $rataRataSkor }}</div>
                                                                                    <div class="text-muted" style="font-size: 10px;">Rata-rata Skor SUS</div>
                                                                                </div>
                                                                                <div>
                                                                                    <span class="badge bg-{{ $predikatColor ?? 'secondary' }} rounded-pill px-3 py-2" style="font-size: 12px;">
                                                                                        {{ $predikat ?? '-' }}
                                                                                    </span>
                                                                                    <div class="text-muted mt-1" style="font-size: 11px;">dari <strong>{{ $totalResponden }}</strong> responden
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ms-auto text-muted" style="font-size: 11px; max-width: 320px;">
                                                                                <i class="bi bi-info-circle me-1"></i>
                                                                                Skor SUS &ge; 80.8 = Excellent | &ge; 71.4 = Good | &ge; 50.9 = Acceptable | &lt; 50.9 = Kurang Baik
                                                                            </div>
                                                                        </div>

                                                                        <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th class="ps-4" style="width: 60px;">No</th>
                                                                                    <th>Nama Responden</th>
                                                                                    <th class="text-center" style="width: 120px;">Skor SUS</th>
                                                                                    <th class="text-center" style="width: 200px;">Predikat</th>
                                                                                    <th>Saran / Komentar</th>
                                                                                    <th class="pe-4 text-end" style="width: 130px;">Tanggal Isi</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse($data as $i => $row)
                                                                                    @php
                $skor = $row->skor_sus ?? 0;
                $predRow = 'Kurang Baik';
                $colorRow = 'danger';
                if ($skor >= 80.8) {
                    $predRow = 'Excellent';
                    $colorRow = 'success';
                } elseif ($skor >= 71.4) {
                    $predRow = 'Good';
                    $colorRow = 'primary';
                } elseif ($skor >= 50.9) {
                    $predRow = 'Acceptable';
                    $colorRow = 'warning';
                }
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                                                                        <td>
                                                                                            <div class="fw-semibold text-dark">{{ optional($row->user)->name ?? '-' }}</div>
                                                                                            <div class="text-muted" style="font-size: 11px;">{{ optional($row->user)->email ?? '-' }}</div>
                                                                                        </td>
                                                                                        <td class="text-center fw-bold" style="font-size: 15px; color: #198754;">{{ $skor }}</td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge bg-{{ $colorRow }} rounded-pill px-3">{{ $predRow }}</span>
                                                                                        </td>
                                                                                        <td class="text-muted text-truncate" style="max-width: 280px;">
                                                                                            {{ $row->saran ?? '<span class="fst-italic">Tidak ada saran</span>' }}
                                                                                        </td>
                                                                                        <td class="pe-4 text-end text-muted">
                                                                                            {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') : '-' }}
                                                                                        </td>
                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="6" class="text-center py-5 text-muted">
                                                                                            <i class="bi bi-inbox text-secondary mb-2" style="font-size: 28px; display: block;"></i>
                                                                                            Belum ada data evaluasi yang diisi oleh responden.
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforelse
                                                                            </tbody>
                                                                        </table>
                                                                    @endif

                                                            </div>
                                                        </div>

                                                        {{-- PAGINATION CONTAINER (Hanya muncul jika variable data menggunakan Pagination) --}}
                                                        @if(in_array($tab, ['peserta', 'kegiatan', 'deteksi', 'faktor', 'tindak_lanjut']) && $data && method_exists($data, 'links'))
                                                            <div class="card-footer bg-white py-2 px-3 d-flex justify-content-between align-items-center"
                                                                style="font-size: 12px;">
                                                                <div class="text-muted">
                                                                    Menampilkan data ke <strong>{{ $data->firstItem() ?? 0 }}</strong> sampai
                                                                    <strong>{{ $data->lastItem() ?? 0 }}</strong> dari total <strong>{{ $data->total() }}</strong> baris
                                                                </div>
                                                                <div>
                                                                    {!! $data->links('pagination::bootstrap-5') !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>
                            @endif
                        </div>

                    </div>

                    {{-- ================= STYLING KARTU LAPORAN COMPACT ================= --}}
                    <style>
                        .report-card {
                            background: #ffffff;
                            border-radius: 12px;
                            padding: 14px 16px;
                            height: 100%;
                            border: 1px solid #eef1f4;
                            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
                            transition: all .2s ease;
                            cursor: pointer;
                        }

                        .report-card:hover {
                            transform: translateY(-4px);
                            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
                            border-color: #cbd5e1;
                        }

                        /* Styling Kartu Aktif */
                        .active-card {
                            background: #f8fafc;
                            border-width: 2px !important;
                            transform: translateY(-2px);
                            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.12) !important;
                        }

                        .icon-box {
                            width: 44px;
                            height: 44px;
                            border-radius: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 20px;
                        }

                        /* Background soft colors */
                        .bg-primary-soft {
                            background: rgba(13, 110, 253, .12) !important;
                        }

                        .bg-danger-soft {
                            background: rgba(220, 53, 69, .12) !important;
                        }

                        .bg-warning-soft {
                            background: rgba(255, 193, 7, .18) !important;
                        }

                        .bg-success-soft {
                            background: rgba(25, 135, 84, .12) !important;
                        }

                        .bg-info-soft {
                            background: rgba(13, 202, 240, .15) !important;
                        }

                        .bg-secondary-soft {
                            background: rgba(108, 117, 125, .15) !important;
                        }

                        .bg-purple-soft {
                            background: rgba(111, 66, 193, .15) !important;
                        }

                        .bg-dark-soft {
                            background: rgba(33, 37, 41, .12) !important;
                        }

                        .text-purple {
                            color: #6f42c1;
                        }

                        /* Custom Pagination Fix */
                        .pagination {
                            margin-bottom: 0;
                            gap: 2px;
                        }

                        .page-link {
                            padding: 0.25rem 0.5rem;
                            font-size: 0.75rem;
                            border-radius: 6px;
                        }

                        .bg-teal-soft { background: rgba(32, 178, 170, .15) !important; }
    .text-teal { color: #20b2aa; }
    .border-teal { border-color: #20b2aa !important; }
    .bg-teal { background-color: #20b2aa !important; }
                    </style>

@endsection