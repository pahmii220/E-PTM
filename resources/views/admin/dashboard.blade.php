@extends('layouts.master')

@section('title', 'Dashboard Admin — PTM Monitor')

@section('content')
            @php 
                        use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;
    use App\Models\Peserta;
    use App\Models\DeteksiDiniPTM;
    use App\Models\User;
    use App\Models\Puskesmas;

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

    // Logika Tanggal Indonesia Manual
    $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    $namaBulan = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
    $tanggalIndo = $namaHari[$waktuSekarang->format('l')] . ', ' . $waktuSekarang->format('d') . ' ' . $namaBulan[$waktuSekarang->format('F')] . ' ' . $waktuSekarang->format('Y');

    // =======================================================================
    // 2. DATA KPI (Modified)
    // =======================================================================
    $realTotalPeserta = Peserta::count();
    $realTotalDeteksi = DeteksiDiniPTM::count();
    $realTotalPegawai = User::where('role_name', 'pegawai')->count();
    $realTotalPetugas = User::where('role_name', 'petugas')->count();

    // Data Grafik Puskesmas
    $puskesmasList = Puskesmas::all();
    $puskesmasLabels = $puskesmasList->pluck('nama_puskesmas')->toArray();
    $puskesmasData = $puskesmasList->map(function ($p)
     {
        return Peserta::where('puskesmas_id', $p->id)->count();
    })->toArray();

    // Data Deteksi Dini per Puskesmas
    $deteksiData = $puskesmasList->map(function ($p) {
        return \App\Models\DeteksiDiniPTM::where('puskesmas_id', $p->id)->count();
    })->toArray();

    // Data Faktor Risiko per Puskesmas
    $faktorData = $puskesmasList->map(function ($p) {
        return \App\Models\FaktorResikoPTM::where('puskesmas_id', $p->id)->count();
    })->toArray();

    // Data Skrining
    $skNormal = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Normal%')->count();
    $skDicurigai = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Dicurigai%')->count();
    $skRisiko = DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Risiko%')->count() + DeteksiDiniPTM::where('hasil_skrining', 'LIKE', '%Resiko%')->count();
    $totalSkrining = $skNormal + $skDicurigai + $skRisiko;
            @endphp
            <div class="container py-2">
                    {{-- ================= SPANDUK SELAMAT DATANG ================= --}}
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-6 shadow-lg mb-6 relative overflow-hidden text-white flex justify-between items-center">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20"><i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i></div>
                    <div class="relative z-10">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi {{ $ikonUcapan }} fs-4"></i>
                            <h1 class="h4 fw-bold mb-0 tracking-wide">{{ $ucapan }}, Administrator!</h1>
                        </div>

                                                           <p class="text-blue-100 text-sm mb-0 mt-2">Berikut adalah ringkasan data PTM, dan statistik puskesmas.</p>
                    </div>
                    <div class="relative z-10 hidden md:block text-right">
                        <div class="text-blue-100 text-xs fw-semibold up
                                   percase tracking-wider mb-1">Tanggal Hari Ini</div>
                        <div class="fs-5 fw-bold">{{ $tanggalIndo }}</div>
                </div>



                                        </div>
                {{-- ================= KARTU INDIKATOR (KPI) ================= --}}
                <div class="row g-3 mb-4">
                    {{-- Card 1: Peserta --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-people-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Total Peserta</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPeserta) }}</h3></div></div></div>
                    </div>
                    {{-- Card 2: Deteksi Dini --}}
                        <div class="col-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-indigo-50 text-indigo-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-file-medical-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Deteksi Dini</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalDeteksi) }}</h3></div></div></div>
                        </div>
                    {{-- Card 3: Pegawai --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-green-50 text-green-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-person-badge-fill"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Pegawai Dinkes</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPegawai) }}</h3></div></div></div>
                    </div>
                    {{-- Card 4: Petugas --}}
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-2xl h-100"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-500 d-flex align-items-center justify-content-center text-3xl"><i class="bi bi-person-workspace"></i></div><div><p class="text-gray-500 text-xs mb-1 font-semibold uppercase tracking-wider">Petugas</p><h3 class="text-gray-800 font-extrabold mb-0 text-2xl">{{ number_format($realTotalPetugas) }}</h3></div></div></div>
                    </div>
                </div>
                {{-- ================= PUSAT STATISTIK ================= --}}
                <div class="row g-4">
                    {{-- Chart Puskesmas --}}
                    <div class="col-lg-6">
                        <div class="card shadow-sm h-100 border-0 rounded-2xl">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 border-b pb-3 border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg"><i class="bi bi-hospital"></i></div>
                                        <h5 class="mb-0 fw-bold text-gray-800">Pasien Terdaftar per Puskesmas</h5>
                                    </div>
                                    {{-- <a href="{{ route('admin.dashboard.print') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm flex items-center gap-1.5 font-semibold text-xs border-gray-200 text-gray-600 hover:bg-gray-50">
                                        <i class="bi bi-printer"></i> Cetak
                                    </a> --}}
                                </div>
        {{-- Chart Vertical Grouped --}}
    <div style="height: 320px; width: 100%;">
        <canvas id="puskesmasChart"></canvas>
    </div>                   
                            </div>
                        </div>
                    </div>
                    {{-- Chart Skrining --}}
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
                                            <div class="text-center py-5"><i class="bi bi-pie-chart text-gray-300 d-block mb-2" style="font-size: 3rem;"></i><p class="text-gray-400 text-sm mb-0">Data belum tersedia</p></div>
                                        @else
                                            <canvas id="skriningChart"></canvas>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- ================= PETA SEBARAN LOKASI PUSKESMAS ================= --}}
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-2xl overflow-hidden">
                            <div class="card-header bg-white border-b border-gray-100 p-4 d-flex justify-content-between align-items-center">
                                <div class="flex items-center gap-2">
                                    <div class="bg-red-100 text-red-600 p-2 rounded-lg"><i class="bi bi-geo-alt-fill"></i></div>
                                    <h5 class="mb-0 fw-bold text-gray-800">Peta Sebaran Lokasi Wilayah Puskesmas</h5>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <select id="pilihPuskesmas" class="form-select form-select-sm shadow-sm" style="min-width: 250px; border-radius: 8px;">
                                        <option value="">Pilih Puskesmas...</option>
                                        @foreach($mapPuskesmasData as $pkm)
                                            <option value="{{ $pkm->id }}">{{ $pkm->nama_puskesmas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-body p-0 position-relative">
                                <!-- Peta Leaflet -->
                                <div id="puskesmasMap" style="height: 450px; width: 100%; z-index: 1;"></div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection

@push('scripts')
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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

                var mapMarkers = {};
                var bounds = [];
                var puskesmasList = {!! json_encode($mapPuskesmasData) !!};

                if (puskesmasList && puskesmasList.length > 0) {
                    puskesmasList.forEach(function(pkm) {
                        if (pkm.latitude && pkm.longitude) {
                            var lat = parseFloat(pkm.latitude);
                            var lng = parseFloat(pkm.longitude);

                            if (!isNaN(lat) && !isNaN(lng)) {
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
                                                <div style="font-size: 10px; color: #64748b; margin-bottom: 2px; font-weight:600;">Total Pasien </div>
                                                <strong style="color: #0f172a; font-size: 14px;">${pkm.peserta_count || 0}</strong>
                                            </div>
                                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; border-radius: 6px; text-align: center;">
                                                <i class="bi bi-clipboard2-pulse-fill" style="color: #10b981; font-size: 16px; margin-bottom: 2px; display: block;"></i>
                                                <div style="font-size: 10px; color: #64748b; margin-bottom: 2px; font-weight:600;">Total Deteksi Dini</div>
                                                <strong style="color: #0f172a; font-size: 14px;">${pkm.deteksi_dini_count || 0}</strong>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                var marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                                mapMarkers[pkm.id] = marker;
                                bounds.push([lat, lng]);
                            }
                        }
                    });

                    // Sesuaikan view peta agar semua marker terlihat
                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                }

                // Fitur Filter/Pilih Puskesmas
                var selectPuskesmas = document.getElementById('pilihPuskesmas');
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
                                map.setView(targetMarker.getLatLng(), 16); // 16 adalah level zoom yg cukup dekat
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
        document.addEventListener('DOMContentLoaded', function () {
            // 1. GRAFIK PUSKESMAS VERTIKAL (GROUPED - 3 DATASET)
            const kanvasPuskesmas = document.getElementById('puskesmasChart');
            if (kanvasPuskesmas) {
                const ctx = kanvasPuskesmas.getContext('2d');
                const labelsPuskesmas = {!! json_encode($puskesmasLabels) !!};
                const dataPeserta     = {!! json_encode($puskesmasData) !!};
                const dataDeteksi     = {!! json_encode($deteksiData) !!};
                const dataFaktor      = {!! json_encode($faktorData) !!};

                // Fungsi buat gradient vertikal
                function buatGradient(ctx, r, g, b) {
                    const grad = ctx.createLinearGradient(0, 0, 0, 320);
                    grad.addColorStop(0,   `rgba(${r},${g},${b},0.95)`);
                    grad.addColorStop(1,   `rgba(${r},${g},${b},0.35)`);
                    return grad;
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labelsPuskesmas,
                                                    datasets: [
                                {
                                    label: 'Peserta Terdaftar',
                                    data: dataPeserta,
                                    backgroundColor: buatGradient(ctx, 59, 130, 246),
                                    borderColor: 'rgba(59,130,246,1)',
                                    borderWidth: 0,
                                    borderRadius: { topLeft: 6, topRight: 6 },
                                    borderSkipped: false,
                                    barThickness: 18,
                                }
                            ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',        // Tooltip muncul untuk semua dataset sekaligus
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    borderRadius: 3,
                                    useBorderRadius: true,
                                    font: { size: 11, weight: '600' },
                                    padding: 14,
                                    color: '#374151'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.92)',
                                titleColor: '#f9fafb',
                                bodyColor: '#d1d5db',
                                padding: 14,
                                cornerRadius: 12,
                                titleFont: { size: 13, weight: '700' },
                                bodyFont: { size: 12 },
                                bodySpacing: 7,
                                boxPadding: 5,
                                callbacks: {
                                    title: function(ctx) {
                                        return '🏥 ' + ctx[0].label;
                                    },
                                    label: function(ctx) {
                                        const ikon = ctx.datasetIndex === 0 ? '👥'
                                                   : ctx.datasetIndex === 1 ? '🩺'
                                                   : '⚠️';
                                        const val = ctx.raw.toLocaleString('id-ID');
                                        return `  ${ikon} ${ctx.dataset.label}: ${val} data`;
                                    },
                                    afterBody: function(ctx) {
                                        const total = ctx.reduce((s, c) => s + c.raw, 0);
                                        return [`  ────────────────`, `  📊 Total: ${total.toLocaleString('id-ID')} data`];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10, weight: '600' },
                                    color: '#6b7280',
                                    maxRotation: 30,
                                    callback: function(val, index) {
                                        const nama = labelsPuskesmas[index];
                                        // Potong nama jika terlalu panjang
                                        return nama.length > 14 ? nama.substring(0, 13) + '…' : nama;
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(243,244,246,1)',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    precision: 0,
                                    font: { size: 11 },
                                    color: '#9ca3af',
                                    callback: val => val.toLocaleString('id-ID')
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah Data',
                                    font: { size: 11, style: 'italic' },
                                    color: '#9ca3af'
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutBounce'
                        }
                    }
                });
            }

                    // 2. GRAFIK DONAT (STATISTIK PTM)
                    const kanvasDonat = document.getElementById('skriningChart');
                    if (kanvasDonat) {
                        new Chart(kanvasDonat.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: ['Normal', 'Dicurigai PTM', 'Risiko Tinggi'],
                                datasets: [{
                                    data: [{{ $skNormal }}, {{ $skDicurigai }}, {{ $skRisiko }}],
                                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                    borderWidth: 2,
                                    borderColor: '#ffffff'
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                });
            </script>
@endpush