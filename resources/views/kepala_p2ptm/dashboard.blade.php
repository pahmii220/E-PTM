@extends('layouts.master')

@section('title', 'Dashboard Eksekutif')

@section('content')
    @php 
        use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;
    use App\Models\Puskesmas;
    use App\Models\Peserta;
    use App\Models\User;

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

    // Logic Tanggal
    $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

    // --- DATA TAMBAHAN UNTUK PEGAWAI & PUSKESMAS ---
    $totalPegawai = User::where('role_name', 'pegawai')->count();

    // Data untuk Chart Puskesmas
    $puskesmasList = Puskesmas::all();
    $puskesmasLabels = $puskesmasList->pluck('nama_puskesmas')->toArray();
    $puskesmasData = $puskesmasList->map(function ($p) {
        return Peserta::where('puskesmas_id', $p->id)->count();
    })->toArray();

    // Data untuk Ranking & Sebaran Zona Kasus PTM
    $puskesmasRanking = $puskesmasList->map(function ($p) {
        $jumlahPeserta = Peserta::where('puskesmas_id', $p->id)->count();
        $zona = 'hijau';
        if ($jumlahPeserta > 50) {
            $zona = 'merah';
        } elseif ($jumlahPeserta >= 10) {
            $zona = 'oranye';
        }
        return [
            'nama' => $p->nama_puskesmas,
            'jumlah' => $jumlahPeserta,
            'zona' => $zona
        ];
    })->sortByDesc('jumlah');
    // Data untuk Peta Sebaran PTM Per Daerah (Kecamatan)
    $kecamatanData = $puskesmasList->groupBy('kecamatan')->map(function ($items, $name) {
        $totalPeserta     = 0;
        $totalDeteksi     = 0;
        $totalFaktor      = 0;
        $totalRisikoTinggi= 0;
        $puskesmasNamaList = [];

        foreach ($items as $p) {
            $puskesmasNamaList[] = $p->nama_puskesmas;
            $totalPeserta += \App\Models\Peserta::where('puskesmas_id', $p->id)->count();
            $totalDeteksi += \App\Models\DeteksiDiniPTM::where('puskesmas_id', $p->id)->count();
            $totalFaktor  += \App\Models\FaktorResikoPTM::where('puskesmas_id', $p->id)->count();
            // Risiko Tinggi = hasil_skrining mengandung kata "Risiko" atau "Resiko"
            $totalRisikoTinggi += \App\Models\DeteksiDiniPTM::where('puskesmas_id', $p->id)
                ->where(function($q) {
                    $q->where('hasil_skrining', 'LIKE', '%Risiko%')
                      ->orWhere('hasil_skrining', 'LIKE', '%Resiko%');
                })->count();
        }

        $centers = [
            'Banjarmasin Timur'   => ['lat' => -3.324, 'lng' => 114.620],
            'Banjarmasin Tengah'  => ['lat' => -3.320, 'lng' => 114.590],
            'Banjarmasin Selatan' => ['lat' => -3.365, 'lng' => 114.590],
            'Banjarmasin Utara'   => ['lat' => -3.295, 'lng' => 114.590],
            'Banjarmasin Barat'   => ['lat' => -3.330, 'lng' => 114.565],
        ];

        $center = $centers[$name] ?? ['lat' => -3.3186, 'lng' => 114.5944];
        $color = 'green';
        if ($totalPeserta > 50) {
            $color = 'red';
        } elseif ($totalPeserta >= 10) {
            $color = 'orange';
        }

        return [
            'nama'          => $name,
            'puskesmas'     => implode(', ', $puskesmasNamaList),
            'jumlah'        => $totalPeserta,
            'deteksi'       => $totalDeteksi,
            'faktor'        => $totalFaktor,
            'risikoTinggi'  => $totalRisikoTinggi,
            'lat'           => $center['lat'],
            'lng'           => $center['lng'],
            'color'         => $color
        ];
    })->values()->toArray();
    @endphp

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        {{-- ================= SPANDUK SAMBUTAN ================= --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-4 p-3.5 shadow-lg mb-4 relative overflow-hidden text-white d-flex justify-content-between align-items-center">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                <i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i>
            </div>

            <div class="relative z-10">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="bi {{ $ikonUcapan }} fs-2"></i>
                    <h2 class="fw-bold mb-0 tracking-wide">{{ $ucapan }}, {{ Auth::user()->username ?? 'Kepala P2PTM' }}!</h2>
                </div>
                <p class="text-blue-100 mb-0 mt-2 fs-5">Berikut adalah ringkasan data Penyakit Tidak Menular (PTM) dan analisis hari ini.</p>
            </div>

            <div class="relative z-10 d-none d-md-block text-end">
                <div class="text-blue-100 small text-uppercase fw-bold opacity-75 mb-1">Tanggal Hari Ini</div>
                <div class="fs-4 fw-bold">{{ $tanggalIndo }}</div>
            </div>
        </div>

        {{-- BARIS 1: STATISTIK UTAMA --}}
        <div class="row g-4 mb-4">
            @php 
                $cards = [
        ['title' => 'Total Peserta', 'value' => $data['totalPeserta'], 'icon' => 'bi-people', 'color' => 'primary'],
        ['title' => 'Deteksi Dini', 'value' => $data['totalDeteksi'], 'icon' => 'bi-activity', 'color' => 'success'],
        ['title' => 'Faktor Risiko', 'value' => $data['totalRisiko'], 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
        // Card Puskesmas diganti menjadi Pegawai Dinkes
        ['title' => 'Pegawai Dinkes', 'value' => $totalPegawai, 'icon' => 'bi-person-badge-fill', 'color' => 'info']
    ];
            @endphp

            @foreach($cards as $card)
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted text-uppercase small fw-bold mb-1">{{ $card['title'] }}</p>
                            <h2 class="fw-extrabold mb-0 text-dark">{{ number_format($card['value']) }}</h2>
                        </div>
                        <div class="bg-{{ $card['color'] }}-subtle p-3 rounded-4 text-{{ $card['color'] }}">
                            <i class="bi {{ $card['icon'] }} fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- BARIS 2: ANALISIS GRAFIK --}}
        <div class="row g-4 mb-4">
            {{-- Grafik diganti dari Volume Data menjadi Puskesmas --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-3 me-3"><i class="bi bi-hospital fs-4"></i></div>
                            <h4 class="fw-bold mb-0 text-dark">Sebaran Peserta per Puskesmas</h4>
                        </div>
                        <a href="{{ route('kepala.dashboard.print') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm">
                            <i class="bi bi-printer me-1"></i> Cetak Laporan
                        </a>
                    </div>
                    <div class="mx-auto" style="height: 250px; width: 100%;">
                        <canvas id="puskesmasChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple-50 text-purple-600 p-2 rounded-3 me-3"><i class="bi bi-pie-chart-fill fs-4"></i></div>
                        <h4 class="fw-bold mb-0 text-dark">Proporsi Skrining</h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
                        @if($totalSkrining == 0)
                            <div class="text-center text-muted py-5"><i class="bi bi-pie-chart fs-1 opacity-25"></i><p class="small">Data belum tersedia</p></div>
                        @else
                            <canvas id="skriningChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2.5: PETA SEBARAN KASUS PTM (LEAFLET.JS) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-red-50 text-red-600 p-2 rounded-3 me-3"><i class="bi bi-map-fill fs-4"></i></div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Peta Geografis Sebaran Kasus PTM</h4>
                            <small class="text-muted">Klik area berwarna untuk melihat info detail kasus di tiap Kecamatan (Wilayah Kerja).</small>
                        </div>
                    </div>
                    
                    {{-- Map Container --}}
                    <div id="ptmMap" style="height: 380px;" class="rounded-4 overflow-hidden border border-slate-200"></div>
                </div>
            </div>
        </div>

        {{-- BARIS 3: TARGET PELAPORAN & SEBARAN ZONA KASUS --}}
        <div class="row g-4 mb-4">
            {{-- Target Pelaporan --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <div>
                            <h4 class="fw-bold text-dark mb-0">Target Pelaporan Puskesmas</h4>
                            <small class="text-muted">{{ $data['sudah'] }} dari {{ $data['total'] }} Puskesmas telah melapor</small>
                        </div>
                        <span class="fw-bold text-success fs-3">{{ $data['persentase'] }}%</span>
                    </div>
                    <div class="progress rounded-pill bg-slate-200" style="height: 16px;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $data['persentase'] }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Sebaran Zona Kasus --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-red-50 text-red-600 p-2 rounded-3 me-3"><i class="bi bi-geo-alt-fill fs-5"></i></div>
                            <h4 class="fw-bold mb-0 text-dark">Ranking & Zona Kasus PTM</h4>
                        </div>
                        <span class="badge bg-slate-100 text-slate-600 rounded-pill px-2.5 py-1 text-xs">Berdasarkan Total Peserta</span>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 250px;">
                        <ul class="list-group list-group-flush">
                            @php $rank = 1; @endphp
                            @forelse($puskesmasRanking as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fw-bold text-slate-400 text-sm" style="width: 20px;">#{{ $rank++ }}</span>
                                        <div>
                                            <div class="fw-bold text-slate-800 text-sm">{{ $item['nama'] }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $item['jumlah'] }} Peserta terdaftar</div>
                                        </div>
                                    </div>
                                    <div>
                                        @if($item['zona'] === 'merah')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 text-[10px] font-bold">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Zona Merah
                                            </span>
                                        @elseif($item['zona'] === 'oranye')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 text-[10px] font-bold">
                                                <i class="bi bi-info-circle-fill me-1"></i> Zona Oranye
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 text-[10px] font-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Zona Hijau
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4 border-0">Belum ada data sebaran kasus</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    <style>
        .welcome-banner { background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%); }
        .rounded-4 { border-radius: 1.5rem !important; }
        .transition-hover { transition: all 0.3s ease; }
        .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .fw-extrabold { font-weight: 800; }
        .text-white-75 { color: rgba(255,255,255,0.75); }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-purple-50 { background-color: #f5f3ff; }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.font.family = "'Segoe UI', sans-serif";
            
            // Bar Chart: PUSKESMAS
            new Chart(document.getElementById('puskesmasChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($puskesmasLabels) !!},
                    datasets: [{
                        label: 'Jumlah Peserta',
                        data: {!! json_encode($puskesmasData) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderRadius: 8,
                        barPercentage: 0.5,
                        categoryPercentage: 0.5,
                        maxBarThickness: 60
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { borderDash: [4,4] } },
                        x: { grid: { display: false } }
                    } 
                }
            });

            // Doughnut Chart: SKRINING
            @if($totalSkrining > 0)
            new Chart(document.getElementById('skriningChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'Dicurigai', 'Risiko'],
                    datasets: [{
                        data: [{{ $skNormal }}, {{ $skDicurigai }}, {{ $skRisiko }}],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, cutout: '75%', 
                    plugins: { 
                        legend: { 
                            position: 'bottom', 
                            labels: { usePointStyle: true, padding: 20, font: { size: 14 } } 
                        } 
                    } 
                }
            });
            @endif

            // ===== PETA KOTA BANJARMASIN =====
            const mapRegions = {!! json_encode($kecamatanData) !!};

            // Kunci peta hanya di area Kota Banjarmasin
            var bjmBounds = L.latLngBounds(
                L.latLng(-3.43, 114.47),
                L.latLng(-3.24, 114.68)
            );

            var map = L.map('ptmMap', {
                maxBounds: bjmBounds,
                maxBoundsViscosity: 1.0,
                minZoom: 11,
                maxZoom: 15
            }).setView([-3.328, 114.578], 12);

            // Tile CartoDB Positron — bersih, flat, tanpa satelit
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            // Lookup data DB per kecamatan
            var regionLookup = {};
            mapRegions.forEach(function(r) { regionLookup[r.nama] = r; });

            function getKecColor(nama) {
                var r = regionLookup[nama];
                if (!r) return '#94a3b8';
                if (r.color === 'red')    return '#ef4444';
                if (r.color === 'orange') return '#f59e0b';
                return '#10b981';
            }

            // GeoJSON 5 Kecamatan Banjarmasin
            var kecamatanGeoJSON = {
                "type": "FeatureCollection",
                "features": [
                    { "type": "Feature", "properties": { "nama": "Banjarmasin Utara" },
                      "geometry": { "type": "Polygon", "coordinates": [[[114.555,-3.270],[114.620,-3.270],[114.630,-3.305],[114.600,-3.310],[114.570,-3.308],[114.555,-3.290],[114.555,-3.270]]] } },
                    { "type": "Feature", "properties": { "nama": "Banjarmasin Timur" },
                      "geometry": { "type": "Polygon", "coordinates": [[[114.600,-3.308],[114.640,-3.305],[114.645,-3.340],[114.610,-3.345],[114.590,-3.335],[114.590,-3.315],[114.600,-3.308]]] } },
                    { "type": "Feature", "properties": { "nama": "Banjarmasin Tengah" },
                      "geometry": { "type": "Polygon", "coordinates": [[[114.555,-3.308],[114.600,-3.308],[114.590,-3.335],[114.570,-3.340],[114.550,-3.335],[114.548,-3.315],[114.555,-3.308]]] } },
                    { "type": "Feature", "properties": { "nama": "Banjarmasin Barat" },
                      "geometry": { "type": "Polygon", "coordinates": [[[114.510,-3.295],[114.555,-3.290],[114.555,-3.308],[114.548,-3.335],[114.520,-3.338],[114.505,-3.318],[114.510,-3.295]]] } },
                    { "type": "Feature", "properties": { "nama": "Banjarmasin Selatan" },
                      "geometry": { "type": "Polygon", "coordinates": [[[114.520,-3.338],[114.548,-3.335],[114.590,-3.335],[114.610,-3.345],[114.615,-3.390],[114.560,-3.400],[114.520,-3.380],[114.510,-3.360],[114.520,-3.338]]] } }
                ]
            };

            // ===== Panel Info Dinamis (hover) =====
            var infoPanel = L.control({ position: 'topleft' });
            infoPanel.onAdd = function() {
                this._div = L.DomUtil.create('div', 'map-info-panel');
                this.reset();
                return this._div;
            };
            infoPanel.reset = function() {
                this._div.innerHTML =
                    '<div style="font-family:sans-serif;padding:10px 14px;background:white;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.13);min-width:190px;">' +
                    '<p style="margin:0;font-size:11px;color:#94a3b8;"><i class="bi bi-cursor"></i> Arahkan kursor ke area kecamatan</p>' +
                    '</div>';
            };
            infoPanel.update = function(r, color) {
                this._div.innerHTML =
                    '<div style="font-family:sans-serif;padding:10px 14px;background:white;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.13);min-width:200px;">' +
                    '<div style="font-weight:700;font-size:13px;color:#1e293b;margin-bottom:6px;padding-bottom:6px;border-bottom:2px solid ' + color + ';">' +
                    '<span style="color:' + color + ';margin-right:5px;">&#9632;</span>' + r.nama + '</div>' +
                    '<div style="font-size:11px;color:#475569;margin-bottom:2px;">📍 <b>' + r.puskesmas + '</b></div>' +
                    '<div style="margin-top:8px;display:grid;gap:4px;">' +
                    '<div style="display:flex;justify-content:space-between;font-size:11px;"><span style="color:#64748b;">👥 Total Peserta</span><b style="color:#1d4ed8;">' + r.jumlah + '</b></div>' +
                    '<div style="display:flex;justify-content:space-between;font-size:11px;"><span style="color:#64748b;">🔬 Deteksi Dini</span><b style="color:#15803d;">' + r.deteksi + '</b></div>' +
                    '<div style="display:flex;justify-content:space-between;font-size:11px;"><span style="color:#64748b;">⚠️ Faktor Risiko</span><b style="color:#c2410c;">' + r.faktor + '</b></div>' +
                    '<div style="display:flex;justify-content:space-between;font-size:11px;"><span style="color:#64748b;">🚨 Risiko Tinggi</span><b style="color:#b91c1c;">' + r.risikoTinggi + '</b></div>' +
                    '</div></div>';
            };
            infoPanel.addTo(map);

            // Render choropleth kecamatan
            var geojsonLayer = L.geoJSON(kecamatanGeoJSON, {
                style: function(feature) {
                    var color = getKecColor(feature.properties.nama);
                    return {
                        fillColor: color,
                        weight: 2.5,
                        opacity: 1,
                        color: '#ffffff',
                        fillOpacity: 0.72
                    };
                },
                onEachFeature: function(feature, layer) {
                    var nama  = feature.properties.nama;
                    var r     = regionLookup[nama];
                    var color = getKecColor(nama);


                    layer.on('mouseover', function() {
                        layer.setStyle({ weight: 4, fillOpacity: 0.9, color: '#fff' });
                        if (r) infoPanel.update(r, color);
                    });
                    layer.on('mouseout', function() {
                        geojsonLayer.resetStyle(layer);
                        infoPanel.reset();
                    });
                }
            }).addTo(map);

            // ===== 1. BADGE LABEL ANGKA DI TENGAH TIAP KECAMATAN (Dinamis) =====
            var labelCenters = {
                'Banjarmasin Utara'   : [-3.288, 114.588],
                'Banjarmasin Timur'   : [-3.325, 114.618],
                'Banjarmasin Tengah'  : [-3.322, 114.572],
                'Banjarmasin Barat'   : [-3.318, 114.530],
                'Banjarmasin Selatan' : [-3.365, 114.567]
            };
            var labelMarkers = [];

            function renderLabels(mode) {
                // Hapus label lama jika ada
                labelMarkers.forEach(function(m) { map.removeLayer(m); });
                labelMarkers = [];

                mapRegions.forEach(function(r) {
                    var center = labelCenters[r.nama];
                    if (!center) return;

                    var text = '';
                    var color = '#10b981';
                    if (mode === 'peserta') {
                        text = r.jumlah + ' peserta';
                        color = r.color === 'red' ? '#ef4444' : r.color === 'orange' ? '#f59e0b' : '#10b981';
                    } else {
                        text = r.risikoTinggi + ' risiko';
                        // Mode risiko: Red > 8, Orange 3 - 8, Green < 3
                        color = r.risikoTinggi > 8 ? '#ef4444' : r.risikoTinggi >= 3 ? '#f59e0b' : '#10b981';
                    }

                    var icon = L.divIcon({
                        className: '',
                        html: '<div style="background:' + color + ';color:white;font-family:sans-serif;font-size:11px;font-weight:800;padding:3px 8px;border-radius:20px;box-shadow:0 2px 6px rgba(0,0,0,0.25);white-space:nowrap;text-align:center;line-height:1.4;">' +
                              text + '</div>',
                        iconAnchor: [40, 10]
                    });
                    var marker = L.marker(center, { icon: icon, interactive: false }).addTo(map);
                    labelMarkers.push(marker);
                });
            }

            // Inisialisasi awal label
            renderLabels('peserta');

            // ===== 2. LEGENDA PETA DINAMIS =====
            var legend = L.control({ position: 'bottomright' });
            legend.onAdd = function() {
                this._div = L.DomUtil.create('div');
                this._div.style.cssText = 'background:white;padding:10px 14px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.12);font-family:sans-serif;font-size:12px;line-height:2;';
                this.update('peserta');
                return this._div;
            };
            legend.update = function(mode) {
                if (mode === 'peserta') {
                    this._div.innerHTML =
                        '<b style="font-size:12px;color:#1e293b;display:block;margin-bottom:4px;">Zona Total Peserta</b>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#10b981;margin-right:6px;vertical-align:middle;"></span>Hijau &nbsp;: &lt; 10 peserta<br>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#f59e0b;margin-right:6px;vertical-align:middle;"></span>Oranye: 10 - 50 peserta<br>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#ef4444;margin-right:6px;vertical-align:middle;"></span>Merah &nbsp;: &gt; 50 peserta';
                } else {
                    this._div.innerHTML =
                        '<b style="font-size:12px;color:#1e293b;display:block;margin-bottom:4px;">Zona Kasus Risiko Tinggi</b>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#10b981;margin-right:6px;vertical-align:middle;"></span>Hijau &nbsp;: &lt; 3 kasus<br>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#f59e0b;margin-right:6px;vertical-align:middle;"></span>Oranye: 3 - 8 kasus<br>' +
                        '<span style="display:inline-block;width:13px;height:13px;border-radius:3px;background:#ef4444;margin-right:6px;vertical-align:middle;"></span>Merah &nbsp;: &gt; 8 kasus';
                }
            };
            legend.addTo(map);

            // ===== 3. TOGGLE LAYER CONTROL (Peserta vs Risiko Tinggi) =====
            var activeMode = 'peserta'; // mode default
            var toggleControl = L.control({ position: 'topright' });
            toggleControl.onAdd = function() {
                var div = L.DomUtil.create('div');
                div.style.cssText = 'background:white;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.13);font-family:sans-serif;font-size:11px;overflow:hidden;';
                div.innerHTML =
                    '<div style="padding:6px 10px;font-weight:700;color:#475569;font-size:10px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">TAMPILKAN ZONA</div>' +
                    '<div style="padding:4px 6px;display:flex;gap:4px;">' +
                    '<button id="btnPeserta" onclick="switchMode(\'peserta\')" style="padding:4px 10px;border-radius:6px;border:none;background:#1d4ed8;color:white;font-size:10px;font-weight:700;cursor:pointer;">👥 Peserta</button>' +
                    '<button id="btnRisiko" onclick="switchMode(\'risiko\')" style="padding:4px 10px;border-radius:6px;border:none;background:#f1f5f9;color:#64748b;font-size:10px;font-weight:700;cursor:pointer;">🚨 Risiko</button>' +
                    '</div>';
                L.DomEvent.disableClickPropagation(div);
                return div;
            };
            toggleControl.addTo(map);

            window.switchMode = function(mode) {
                activeMode = mode;
                document.getElementById('btnPeserta').style.background = mode === 'peserta' ? '#1d4ed8' : '#f1f5f9';
                document.getElementById('btnPeserta').style.color      = mode === 'peserta' ? 'white'   : '#64748b';
                document.getElementById('btnRisiko').style.background  = mode === 'risiko'  ? '#ef4444' : '#f1f5f9';
                document.getElementById('btnRisiko').style.color       = mode === 'risiko'  ? 'white'   : '#64748b';
                
                // Update text legenda dan label penanda
                legend.update(mode);
                renderLabels(mode);

                geojsonLayer.setStyle(function(feature) {
                    var nama = feature.properties.nama;
                    var r    = regionLookup[nama];
                    var fill = '#94a3b8';
                    if (r) {
                        if (mode === 'peserta') {
                            fill = r.color === 'red' ? '#ef4444' : r.color === 'orange' ? '#f59e0b' : '#10b981';
                        } else {
                            // Mode risiko: Red > 8, Orange 3 - 8, Green < 3
                            fill = r.risikoTinggi > 8 ? '#ef4444' : r.risikoTinggi >= 3 ? '#f59e0b' : '#10b981';
                        }
                    }
                    return { fillColor: fill, weight: 2.5, opacity: 1, color: '#fff', fillOpacity: 0.72 };
                });
            };

        });
    </script>
    <style>
        .kec-label {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-shadow: 0 1px 3px rgba(255,255,255,0.9), 0 0 6px rgba(255,255,255,0.8);
            pointer-events: none;
        }
        .kec-label::before { display: none !important; }
    </style>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush