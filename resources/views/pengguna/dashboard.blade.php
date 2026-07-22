@extends('layouts.master')

@section('title', 'Dashboard Pegawai — PTM Monitor')

@section('content')
        @php 
            use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// 1. Mengatur zona waktu ke WITA
$waktuSekarang = Carbon::now('Asia/Makassar');
$jam = $waktuSekarang->format('H');

// Logika Ucapan
if ($jam >= 5 && $jam < 12) {
    $ucapan = 'Selamat Pagi';
    $ikonUcapan = 'bi-brightness-alt-high-fill text-yellow-300';
} elseif ($jam >= 12 && $jam < 15) {
    $ucapan = 'Selamat Siang';
    $ikonUcapan = 'bi-sun-fill text-yellow-400';
} elseif ($jam >= 15 && $jam < 18) {
    $ucapan = 'Selamat Sore';
    $ikonUcapan = 'bi-cloud-sun-fill text-orange-300';
} else {
    $ucapan = 'Selamat Malam';
    $ikonUcapan = 'bi-moon-stars-fill text-blue-200';
}

// Logika Tanggal Indonesia Manual (Anti-Error)
$namaHari = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$namaBulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];
$tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

// =======================================================================
// 2. JALAN PINTAS MENGAMBIL DATA LANGSUNG DARI DATABASE (BYPASS CONTROLLER)
// =======================================================================

// Ambil Total KPI
$realTotalPeserta = \App\Models\Peserta::count();
$realTotalDeteksi = \App\Models\DeteksiDiniPTM::count();
$realTotalFaktor = \App\Models\FaktorResikoPTM::count();
// Hitung Jumlah Puskesmas Unik yang Memiliki Antrean Verifikasi (Pending)
$pkmPendingDeteksi = \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'pending')->pluck('puskesmas_id')->toArray();
$pkmPendingFaktor = \App\Models\FaktorResikoPTM::where('status_verifikasi', 'pending')->pluck('puskesmas_id')->toArray();
$pkmPendingPeserta = \App\Models\Peserta::where('status_verifikasi', 'pending')->pluck('puskesmas_id')->toArray();
$allPendingPkm = array_unique(array_merge($pkmPendingDeteksi, $pkmPendingFaktor, $pkmPendingPeserta));
$realPending = count($allPendingPkm);

$realApproved = \App\Models\Peserta::whereIn('status_verifikasi', ['approved', 'terverifikasi'])->count();
$realRejected = \App\Models\Peserta::where('status_verifikasi', 'rejected')->count();
$realTotalVerif = $realApproved + $realRejected + $realPending;

// Ambil Data Skrining PTM untuk Grafik Donat
$skNormal = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
$skDicurigai = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
$skRisiko = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() +
    \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();

$totalSkrining = $skNormal + $skDicurigai + $skRisiko;

// Ambil 7 Jenis Penyakit PTM Terbanyak (Excluding Normal)
$topPenyakit = DB::table('deteksi_dini_ptm')
    ->select('diagnosa_penyakit', DB::raw('count(*) as total'))
    ->whereNotNull('diagnosa_penyakit')
    ->where('diagnosa_penyakit', '!=', '')
    ->where('diagnosa_penyakit', '!=', 'Normal')
    ->groupBy('diagnosa_penyakit')
    ->orderByDesc('total')
    ->limit(7)
    ->get();

$penyakitLabels = $topPenyakit->pluck('diagnosa_penyakit')->toArray();
$penyakitCounts = $topPenyakit->pluck('total')->toArray();

// Ambil 5 Puskesmas Teraktif
$topPuskesmas = DB::table('puskesmas')
    ->leftJoin('deteksi_dini_ptm', 'deteksi_dini_ptm.puskesmas_id', '=', 'puskesmas.id')
    ->select('puskesmas.nama_puskesmas', 'puskesmas.kecamatan', DB::raw('count(deteksi_dini_ptm.id) as total_skrining'))
    ->groupBy('puskesmas.id', 'puskesmas.nama_puskesmas', 'puskesmas.kecamatan')
    ->orderByDesc('total_skrining')
    ->limit(5)
    ->get();

// Ambil Semua Puskesmas beserta statistik untuk Peta Leaflet
$semuaPuskesmas = DB::table('puskesmas')
    ->leftJoin('deteksi_dini_ptm', 'deteksi_dini_ptm.puskesmas_id', '=', 'puskesmas.id')
    ->select(
        'puskesmas.id',
        'puskesmas.nama_puskesmas',
        'puskesmas.kecamatan',
        'puskesmas.alamat',
        'puskesmas.latitude',
        'puskesmas.longitude',
        DB::raw('count(deteksi_dini_ptm.id) as total_skrining'),
        DB::raw("SUM(CASE WHEN deteksi_dini_ptm.status_verifikasi = 'pending' THEN 1 ELSE 0 END) as total_pending"),
        DB::raw("SUM(CASE WHEN deteksi_dini_ptm.hasil_skrining LIKE '%Risiko%' OR deteksi_dini_ptm.hasil_skrining LIKE '%Resiko%' THEN 1 ELSE 0 END) as total_risiko")
    )
    ->groupBy('puskesmas.id', 'puskesmas.nama_puskesmas', 'puskesmas.kecamatan', 'puskesmas.alamat', 'puskesmas.latitude', 'puskesmas.longitude')
    ->orderBy('puskesmas.kecamatan')
    ->orderBy('puskesmas.nama_puskesmas')
    ->get();

// Ambil data detail jenis penyakit teratas per Puskesmas (Top 3)
$rekapPenyakitPkm = DB::table('deteksi_dini_ptm')
    ->select('puskesmas_id', 'diagnosa_penyakit', DB::raw('count(*) as total'))
    ->whereNotNull('diagnosa_penyakit')
    ->where('diagnosa_penyakit', '!=', '')
    ->where('diagnosa_penyakit', '!=', 'Normal')
    ->groupBy('puskesmas_id', 'diagnosa_penyakit')
    ->orderBy('puskesmas_id')
    ->orderByDesc('total')
    ->get()
    ->groupBy('puskesmas_id');

foreach ($semuaPuskesmas as $pkm) {
    $detailPenyakit = [];
    if (isset($rekapPenyakitPkm[$pkm->id])) {
        foreach ($rekapPenyakitPkm[$pkm->id]->take(3) as $peny) {
            $detailPenyakit[] = [
                'nama' => $peny->diagnosa_penyakit,
                'total' => $peny->total
            ];
        }
    }
    $pkm->detail_penyakit = $detailPenyakit;
}
        @endphp

        <div class="container py-2">

            {{-- ================= NOTIFIKASI SURAT TUGAS DITOLAK ================= --}}
            @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                @php
                    $rejectedSuratTugas = \App\Models\SuratTugasLuar::where('pegawai_id', auth()->user()->pegawaiDinkes->id ?? null)
                        ->where('status_persetujuan', 'ditolak')
                        ->where('updated_at', '>=', now()->subDays(3)) // Muncul selama 3 hari terakhir
                        ->get();
                @endphp
                
                @if($rejectedSuratTugas->count() > 0)
                    @foreach($rejectedSuratTugas as $rst)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm mb-4 flex justify-between items-center transition-all duration-500">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 text-red-500 fs-3">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <div>
                                <h3 class="text-red-800 text-sm font-bold mb-0">Pengajuan Surat Tugas Ditolak!</h3>
                                <p class="text-red-700 text-xs mt-1 mb-0">Pengajuan ke <strong>{{ $rst->lokasi_tujuan ?? optional($rst->puskesmas)->nama_puskesmas }}</strong> ditolak. Alasan: "{{ $rst->catatan_kepala }}"</p>
                            </div>
                        </div>
                        <a href="{{ route('pengguna.surat_tugas.index') }}" class="btn btn-danger btn-sm fw-bold shadow-sm rounded-lg px-3">
                            Ke Surat Tugas
                        </a>
                    </div>
                    @endforeach
                @endif
            @endif

            {{-- ================= PERINGATAN PROFIL BELUM LENGKAP ================= --}}
            @if(auth()->check() && auth()->user()->role_name === 'pegawai' && !auth()->user()->profilDinkesLengkap())
                <div id="alert-profil" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow-sm mb-4 flex justify-between items-center transition-all duration-500">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 text-yellow-500 fs-3">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-yellow-800 text-sm font-bold mb-0">Profil Anda Belum Lengkap!</h3>
                            <p class="text-yellow-700 text-xs mt-1 mb-0">Silakan lengkapi profil dan identitas Anda untuk memaksimalkan penggunaan sistem.</p>
                        </div>
                    </div>
                    <a href="{{ route('pengguna.pegawai_dinkes.edit', auth()->id()) }}" class="btn btn-warning btn-sm fw-bold shadow-sm rounded-lg px-3">
                        Lengkapi Sekarang
                    </a>
                </div>
            @endif

            {{-- ================= SPANDUK SELAMAT DATANG ================= --}}
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg mb-6 relative overflow-hidden text-white flex justify-between items-center">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                    <i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i>
                </div>
                <div class="absolute bottom-0 right-32 -mb-10 opacity-10">
                    <i class="bi bi-circle-fill" style="font-size: 10rem;"></i>
                </div>

                <div class="relative z-10">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi {{ $ikonUcapan }} fs-4"></i>
                        <h1 class="h4 fw-bold mb-0 tracking-wide">{{ $ucapan }}, {{ Auth::user()->username ?? 'Pegawai Dinkes' }}!</h1>
                    </div>
                    <p class="text-blue-100 text-sm mb-0 mt-2">Berikut adalah ringkasan data Penyakit Tidak Menular (PTM) dan antrean verifikasi hari ini.</p>
                </div>

                <div class="relative z-10 hidden md:block text-right">
                    <div class="text-blue-100 text-xs fw-semibold uppercase tracking-wider mb-1">Tanggal Hari Ini</div>
                    <div class="fs-5 fw-bold">{{ $tanggalIndo }}</div>
                </div>
            </div>
            {{-- ================= KARTU INDIKATOR (KPI) ================= --}}
            <div class="row g-3 mb-4">
                {{-- Total Peserta --}}
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-50 text-blue-600 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Total Pasien Terdaftar</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPeserta) }} Orang</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Antrean Puskesmas --}}
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-2xl hover:shadow-md justify-content-center transition duration-300 transform hover:-translate-y-1 h-100 relative overflow-hidden">
                        @if($realPending > 0)
                            <div class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full m-3 animate-pulse shadow-sm border border-white"></div>
                        @endif
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-amber-50 text-amber-500 d-flex align-items-center justify-content-center text-3xl shadow-inner">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Antrean Puskesmas (Pending)</p>
                                <h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realPending) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= PUSAT STATISTIK & ANALISIS DATA PTM ================= --}}
            <div class="row g-4">

                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="bi bi-bar-chart-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Tren Jenis Penyakit Tidak Menular</h5>
                                </div>
                            </div>
                            <div style="height: 280px; width: 100%;">
                                <canvas id="monitorChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm h-100 border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="bg-purple-100 text-purple-600 p-2 rounded-lg"><i class="bi bi-pie-chart-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Proporsi Hasil Skrining PTM</h5>
                                </div>
                            </div>
                            <div style="height: 280px; width: 100%;" class="d-flex align-items-center justify-content-center position-relative">

                                @if($totalSkrining == 0)
                                    <div class="text-center py-5">
                                        <i class="bi bi-pie-chart text-gray-300 d-block mb-2" style="font-size: 3rem;"></i>
                                        <p class="text-gray-400 text-sm mb-0">Data hasil skrining belum tersedia</p>
                                    </div>
                                    <canvas id="skriningChart" class="d-none"></canvas>
                                @else
                                    <canvas id="skriningChart"></canvas>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>


            </div>
            {{-- ================= PETA SEBARAN PUSKESMAS ================= --}}
            <div class="row g-4 mt-2 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-2xl overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100 flex-wrap gap-3">
                                <div class="flex items-center gap-2">
                                    <div class="bg-red-100 text-red-600 p-2 rounded-lg"><i class="bi bi-geo-alt-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Peta Sebaran Lokasi Wilayah Puskesmas</h5>
                                </div>
                                <div style="min-width: 250px;">
                                    <select id="pilihPuskesmasMap" class="form-select rounded-pill shadow-sm border-gray-200">
                                        <option value="">Tampilkan Semua Puskesmas</option>
                                        @foreach($puskesmasList as $pkm)
                                            @if($pkm->latitude && $pkm->longitude)
                                                <option value="{{ $pkm->id }}">{{ $pkm->nama_puskesmas }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="puskesmasMap" style="height: 450px; width: 100%; border-radius: 12px; z-index: 1;" class="shadow-sm border"></div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ================= PEMANTAUAN KEPATUHAN LAPORAN PUSKESMAS (SEMENTARA DISEMBUNYIKAN) ================= 
            <div class="row g-4 mt-2">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-2xl">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-b pb-3 border-gray-100">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-amber-100 text-amber-600 p-2 rounded-lg"><i class="bi bi-bell-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Pemantauan Kepatuhan Laporan Bulanan Puskesmas</h5>
                                </div>
                                <form method="GET" action="{{ route('pengguna.dashboard') }}" class="d-flex align-items-center gap-2 bg-light p-2 rounded-xl border border-gray-200">
                                    <span class="small fw-semibold text-gray-600 me-1 d-none d-sm-inline"><i class="bi bi-calendar-event me-1 text-amber-600"></i>Periode:</span>
                                    
                                    <select name="kepatuhan_bulan" class="form-select form-select-sm border-0 bg-transparent fw-semibold" style="width: 130px; cursor: pointer;" onchange="this.form.submit()">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $filterKepatuhanBulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>

                                    <select name="kepatuhan_tahun" class="form-select form-select-sm border-0 bg-transparent fw-semibold" style="width: 90px; cursor: pointer;" onchange="this.form.submit()">
                                        @for ($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year; $y++)
                                            <option value="{{ $y }}" {{ $filterKepatuhanTahun == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.9rem;">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th class="py-3">No</th>
                                            <th class="py-3 text-start">Nama Puskesmas</th>
                                            <th class="py-3 text-start">Kecamatan</th>
                                            <th class="py-3">Total Skrining Periode Terpilih</th>
                                            <th class="py-3">Status Pengiriman</th>
                                            <th class="py-3">Aksi Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rekapKepatuhan as $no => $pkm)
                                            <tr>
                                                <td>{{ $no + 1 }}</td>
                                                <td class="text-start fw-semibold">{{ $pkm->nama_puskesmas }}</td>
                                                <td class="text-start text-muted">{{ $pkm->kecamatan }}</td>
                                                <td>
                                                    <span class="badge bg-blue-100 text-blue-800 fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                                                        {{ $pkm->total_skrining_bulan_ini }} Orang
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($pkm->total_dilaporkan_bulan_ini > 0)
                                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Sudah Melapor
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
                                                            <i class="bi bi-x-circle-fill me-1"></i> Belum Melapor
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pkm->total_dilaporkan_bulan_ini > 0)
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-medium" disabled>
                                                            <i class="bi bi-check-lg me-1"></i> Selesai
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold btn-send-reminder text-dark shadow-sm hover:bg-yellow-500 transition" 
                                                            data-id="{{ $pkm->id }}" 
                                                            data-nama="{{ $pkm->nama_puskesmas }}" 
                                                            data-bulan="{{ $waktuKepatuhan->translatedFormat('F Y') }}">
                                                            <i class="bi bi-bell-fill me-1"></i> Kirim Pengingat
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">Data Puskesmas tidak ditemukan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            --}}

        </div>
@endsection

@push('styles')
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .kpi-card {
            text-align: center !important;
        }
        .kpi-card .card-body {
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }
        .kpi-card .w-14 {
            margin-bottom: 0.75rem !important;
        }
        /* Custom Leaflet Tooltip untuk Puskesmas */
        .pkm-tooltip {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            min-width: 220px;
        }
        .pkm-tooltip .leaflet-tooltip-content {
            margin: 0;
        }
        .pkm-tooltip::before {
            border-top-color: white;
        }
        .kec-label-pegawai {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            text-shadow: 0 1px 3px rgba(255,255,255,0.9), 0 0 6px rgba(255,255,255,0.8);
            pointer-events: none;
        }
        .kec-label-pegawai::before { display: none !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. INISIALISASI GRAFIK BATANG HORIZONTAL (7 PENYAKIT TERBANYAK)
            const kanvasBatang = document.getElementById('monitorChart');
            if (kanvasBatang) {
                const labelsPenyakit = {!! json_encode($penyakitLabels) !!};
                const countsPenyakit = {!! json_encode($penyakitCounts) !!};

                new Chart(kanvasBatang.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsPenyakit,
                        datasets: [{
                            label: 'Jumlah Kasus',
                            data: countsPenyakit,
                            backgroundColor: 'rgba(59, 130, 246, 0.85)', // Biru Modern
                            hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                            borderRadius: 6,
                            barThickness: 20
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Membuat bar chart horizontal
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { color: '#f3f4f6', borderDash: [5, 5] } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. INISIALISASI GRAFIK DONAT (STATISTIK PTM)
            const kanvasDonat = document.getElementById('skriningChart');
            if (kanvasDonat && !kanvasDonat.classList.contains('d-none')) {
                // Ambil data skrining langsung dari PHP bypass
                const dataSkrining = [
                    {{ $skNormal }},
                    {{ $skDicurigai }},
                    {{ $skRisiko }}
                ];

                new Chart(kanvasDonat.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Normal', 'Dicurigai PTM', 'Risiko Tinggi'],
                        datasets: [{
                            data: dataSkrining,
                            backgroundColor: [
                                '#10b981', // Hijau Emerald (Normal)
                                '#f59e0b', // Amber/Kuning (Dicurigai)
                                '#ef4444'  // Merah (Risiko Tinggi)
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: { boxWidth: 12, font: { size: 12, weight: '500' }, padding: 15 }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Setup CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Kirim Pengingat Laporan Bulanan via AJAX
            $('.btn-send-reminder').click(function() {
                var btn = $(this);
                var id = btn.data('id');
                var nama = btn.data('nama');
                var bulan = btn.data('bulan');

                if (confirm('Kirim pengingat tagihan laporan bulanan ke ' + nama + '?')) {
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...');

                    $.ajax({
                        url: '/reminder/send',
                        type: 'POST',
                        data: {
                            puskesmas_id: id,
                            bulan_nama: bulan
                        },
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                btn.removeClass('btn-warning').addClass('btn-success text-white').html('<i class="bi bi-check-lg me-1"></i> Terkirim').prop('disabled', true);
                            } else {
                                alert('Gagal mengirim pengingat.');
                                btn.prop('disabled', false).html('<i class="bi bi-bell-fill me-1"></i> Kirim Pengingat');
                            }
                        },
                        error: function() {
                            alert('Terjadi kesalahan pada server.');
                            btn.prop('disabled', false).html('<i class="bi bi-bell-fill me-1"></i> Kirim Pengingat');
                        }
                    });
                }
            });
        });
    </script>

    <!-- Leaflet.js Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // INISIALISASI PETA SEBARAN PUSKESMAS
            var mapElement = document.getElementById('puskesmasMap');
            if (mapElement) {
               var map = L.map('puskesmasMap', {
                    minZoom: 12 // Kunci zoom terkecil di area Banjarmasin
                }).setView([-3.316694, 114.590111], 12); // Default ke Banjarmasin

                // Base Tile Layers (Peta Standar & Satelit)
                var googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google Maps'
                });

                var googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google Maps Satellite'
                });

                var esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles © Esri'
                });

                var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });

                // Default Layer Active
                googleRoadmap.addTo(map);

                // Control Switcher Layer
                var baseMaps = {
                    "🗺️ Peta Standar": googleRoadmap,
                    "🛰️ Satelit (Google)": googleSatellite,
                    "🌍 Satelit (Esri)": esriSatellite,
                    "📍 OpenStreetMap": osmLayer
                };

                L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

                // --- BATAS WILAYAH KECAMATAN ---
                fetch("{{ asset('geojson_banjarmasin.json') }}")
                    .then(response => response.json())
                    .then(data => {
                        L.geoJSON(data, {
                            style: function (feature) {
                                let color = '#3388ff'; // default
                                let fillColor = '#3388ff';
                                const nama = feature.properties.name || '';

                                if (nama.includes('Utara')) {
                                    fillColor = '#facc15'; // Kuning
                                    color = '#ca8a04';
                                } else if (nama.includes('Barat')) {
                                    fillColor = '#38bdf8'; // Biru terang
                                    color = '#0284c7';
                                } else if (nama.includes('Tengah')) {
                                    fillColor = '#c084fc'; // Ungu
                                    color = '#9333ea';
                                } else if (nama.includes('Timur')) {
                                    fillColor = '#f87171'; // Merah
                                    color = '#dc2626';
                                } else if (nama.includes('Selatan')) {
                                    fillColor = '#fbcfe8'; // Merah muda/Pink
                                    color = '#db2777';
                                }

                                return {
                                    color: color,
                                    fillColor: fillColor,
                                    fillOpacity: 0.4,
                                    weight: 2,
                                    dashArray: '3'
                                };
                            },
                            onEachFeature: function (feature, layer) {
                                if (feature.properties && feature.properties.name) {
                                    layer.bindTooltip("<strong>" + feature.properties.name + "</strong>", {
                                        permanent: false,
                                        direction: "center"
                                    });
                                }
                            }
                        }).addTo(map);
                    })
                    .catch(err => console.error("Gagal memuat batas kecamatan:", err));
                // --- END BATAS WILAYAH ---

                var puskesmasList = {!! json_encode($puskesmasList) !!};
                var bounds = [];
                var mapMarkers = {}; // Simpan referensi marker berdasarkan ID

                puskesmasList.forEach(function(pkm) {
                    if (pkm.latitude && pkm.longitude) {
                        var lat = parseFloat(pkm.latitude);
                        var lng = parseFloat(pkm.longitude);

                        var alamatLengkap = pkm.alamat || 'Alamat tidak tersedia';
                        var statusBadge = pkm.deteksi_dini_count > 0 
                            ? '<span style="background:#dcfce7; color:#166534; padding:2px 6px; border-radius:12px; font-size:10px; font-weight:bold;">Aktif Melapor</span>'
                            : '<span style="background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:12px; font-size:10px; font-weight:bold;">Belum Ada Data</span>';

                        var popupContent = `
                            <div style="font-family: 'Inter', sans-serif; min-width: 240px; padding: 2px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px; gap: 8px;">
                                    <h6 style="margin:0; font-weight:800; color:#0f172a; font-size:14px; line-height: 1.3;">
                                        ${pkm.nama_puskesmas}
                                    </h6>
                                    <div>${statusBadge}</div>
                                </div>

                                <p style="margin: 0 0 12px 0; font-size:11px; color:#64748b; line-height: 1.4; display: flex; align-items: start; gap: 6px;">
                                    <i class="bi bi-geo-alt-fill text-danger" style="margin-top: 1px;"></i> 
                                    <span>${alamatLengkap}</span>
                                </p>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 5px;">
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; border-radius: 6px; text-align: center;">
                                        <i class="bi bi-people-fill" style="color: #3b82f6; font-size: 16px; margin-bottom: 2px; display: block;"></i>
                                        <div style="font-size: 10px; color: #64748b; margin-bottom: 2px; font-weight:600;">Peserta PTM</div>
                                        <strong style="color: #0f172a; font-size: 14px;">${pkm.peserta_count || 0}</strong>
                                    </div>
                                    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; border-radius: 6px; text-align: center;">
                                        <i class="bi bi-clipboard2-pulse-fill" style="color: #10b981; font-size: 16px; margin-bottom: 2px; display: block;"></i>
                                        <div style="font-size: 10px; color: #64748b; margin-bottom: 2px; font-weight:600;">Pemeriksaan</div>
                                        <strong style="color: #0f172a; font-size: 14px;">${pkm.deteksi_dini_count || 0}</strong>
                                    </div>
                                </div>
                            </div>
                        `;

                        var marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                        bounds.push([lat, lng]);
                        mapMarkers[pkm.id] = marker; // Menyimpan marker dengan kunci ID puskesmas
                    }
                });

                // Fit bounds jika ada data marker
                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }

                // Event Listener untuk Dropdown Pencarian
                var selectPuskesmas = document.getElementById('pilihPuskesmasMap');
                if (selectPuskesmas) {
                    selectPuskesmas.addEventListener('change', function() {
                        var selectedId = this.value;

                        if (selectedId === "") {
                            // Reset ke tampilan semua puskesmas
                            if (bounds.length > 0) {
                                map.fitBounds(bounds, { padding: [50, 50] });
                                map.closePopup();
                            }
                        } else {
                            // Zoom ke marker spesifik
                            var targetMarker = mapMarkers[selectedId];
                            if (targetMarker) {
                                map.setView(targetMarker.getLatLng(), 16);
                                targetMarker.openPopup();
                            }
                        }
                    });
                }

                // --- LEGENDA PETA ---
                var legend = L.control({position: 'bottomright'});
                legend.onAdd = function (map) {
                    var div = L.DomUtil.create('div', 'info legend');
                    div.style.backgroundColor = 'white';
                    div.style.padding = '10px';
                    div.style.borderRadius = '8px';
                    div.style.boxShadow = '0 1px 3px rgba(0,0,0,0.2)';
                    div.style.fontSize = '12px';
                    div.style.lineHeight = '1.5';

                    div.innerHTML += '<h6 style="margin:0 0 5px 0;font-weight:bold;font-size:13px;border-bottom:1px solid #ddd;padding-bottom:5px;">Wilayah Kecamatan</h6>';

                    var grades = ["Utara", "Barat", "Tengah", "Timur", "Selatan"];
                    var colors = ["#facc15", "#38bdf8", "#c084fc", "#f87171", "#fbcfe8"];

                    for (var i = 0; i < grades.length; i++) {
                        div.innerHTML +=
                            '<div style="display:flex; align-items:center; margin-bottom:2px;">' +
                            '<i style="background:' + colors[i] + '; width: 14px; height: 14px; display: inline-block; margin-right: 8px; opacity: 0.7; border: 1px solid #999; border-radius: 3px;"></i> ' +
                            '<span>Banjarmasin ' + grades[i] + '</span></div>';
                    }
                    return div;
                };
                legend.addTo(map);
                // --- END LEGENDA ---

            }
        });
    </script>
@endpush