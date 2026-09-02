@php
    $currentBulan = $filterTrendBulan ?? request('trend_bulan', \Carbon\Carbon::now()->format('m'));
    $currentTahun = $filterTrendTahun ?? request('trend_tahun', \Carbon\Carbon::now()->format('Y'));
    
    if (!isset($mapAnalytics)) {
        $mapAnalytics = \App\Services\MapVisualizationService::getMapData($currentBulan, $currentTahun);
    }
    
    $pkmData = $mapAnalytics['puskesmas_data'] ?? [];
    $heatPoints = $mapAnalytics['heat_points'] ?? [];
    $choroplethData = $mapAnalytics['kecamatan_choropleth'] ?? [];
    $diseaseHotspots = $mapAnalytics['disease_hotspots'] ?? [];
    $topHotspots = $mapAnalytics['top_hotspots'] ?? [];
    $highestKec = $topHotspots['top_kecamatan'] ?? null;
    $topPkmList = $topHotspots['top_puskesmas'] ?? [];
@endphp

{{-- ================= CARD PETA SEBARAN, KEPADATAN & CLUSTERING PTM ================= --}}
<div class="card border-0 shadow-sm rounded-3xl overflow-hidden mb-4 ptm-gis-card">
    {{-- HEADER KONTROL GIS --}}
    <div class="card-header bg-white border-0 p-4 pb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-2xl text-white shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <i class="bi bi-fire fs-4"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-gray-900" style="letter-spacing: -0.02em;">Peta Kepadatan & Clustering Sebaran PTM</h4>
                        <span class="badge bg-danger-subtle text-danger fw-bold px-2.5 py-1 rounded-pill text-xs border border-danger-subtle">
                            <i class="bi bi-broadcast me-1"></i> Live Spatial Analytics
                        </span>
                        <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill text-xs border border-primary-subtle">
                            <i class="bi bi-geo-alt-fill me-1"></i> Prov. Kalimantan Selatan
                        </span>
                    </div>
                    <p class="text-muted small mb-0 mt-0.5">Visualisasi Heat Map densitas penderita, clustering wilayah puskesmas, dan zonasi risiko</p>
                </div>
            </div>

            {{-- SEARCH & FOKUS HOTSPOT --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" id="btnFocusHotspot" class="btn btn-danger btn-sm rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5 transition-all">
                    <i class="bi bi-crosshair fs-6"></i> Fokus Hotspot Terbesar
                </button>
                <div style="min-width: 240px;">
                    <select id="pilihPuskesmasMap" class="form-select form-select-sm rounded-pill border-gray-300 shadow-sm fw-medium">
                        <option value="">🎯 Semua Puskesmas (Overview)</option>
                        @foreach($pkmData as $pkm)
                            <option value="{{ $pkm['id'] }}" data-lat="{{ $pkm['lat'] }}" data-lng="{{ $pkm['lng'] }}">
                                {{ $pkm['nama_puskesmas'] }} ({{ $pkm['total_kasus_ptm'] }} Kasus)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- DYNAMIC HOTSPOT INSIGHT ALERT BANNER DENGAN TOMBOL TINDAKAN SEGERA --}}
        <div id="hotspotAlertBanner" class="alert alert-danger border-0 rounded-2xl p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm" style="background: linear-gradient(90deg, #fef2f2 0%, #fff1f2 100%); border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center gap-2.5 flex-wrap">
                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 fw-bold fs-7 animate-pulse">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="hotspotBadgeText">OUTBREAK HOTSPOT</span>
                </span>
                <span class="text-gray-800 fs-7" id="hotspotDescription">
                    @if($highestKec && ($highestKec['total_kasus_ptm'] ?? 0) > 0)
                        Konsentrasi kasus PTM tertinggi di <strong>{{ $highestKec['nama'] }}</strong> 
                        ({{ $highestKec['total_kasus_ptm'] }} Kasus, Prevalensi: <strong>{{ $highestKec['prevalensi_rate'] }}%</strong>). 
                        Penyakit dominan: <span class="badge bg-white text-danger border border-danger-subtle fw-semibold px-2 py-0.5">{{ $highestKec['dominant_disease'] }}</span>
                    @else
                        Memuat data sebaran dan konsentrasi kasus PTM Kota Banjarmasin...
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small text-danger fw-semibold me-1 d-none d-md-inline" id="hotspotZoneLabel">
                    {{ $highestKec['zone_label'] ?? 'Monitoring Aktif' }}
                </span>
                <a id="btnActionHotspot" href="{{ route('pengguna.verifikasi_laporan.index') }}" class="btn btn-danger btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5 text-white text-nowrap transition-all" style="font-size: 11px; text-decoration: none;">
                    <i class="bi bi-shield-fill-exclamation"></i> <span id="btnActionHotspotText">Tindakan Segera: Monitoring Laporan</span>
                </a>
            </div>
        </div>

        {{-- FILTER BAR: PENYAKIT & MODE TAMPILAN --}}
        <div class="row g-2 align-items-center pt-2 border-top border-gray-100">
            {{-- PILIH JENIS PENYAKIT (PREVALENSI) --}}
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="small fw-bold text-gray-700 me-1"><i class="bi bi-funnel-fill text-danger me-1"></i>Prevalensi:</span>
                    <button type="button" class="btn btn-sm btn-filter-disease active rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="all">
                        🔥 Semua PTM
                    </button>
                    <button type="button" class="btn btn-sm btn-filter-disease btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="hipertensi">
                        🩸 Hipertensi
                    </button>
                    <button type="button" class="btn btn-sm btn-filter-disease btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="diabetes">
                        🍬 Diabetes
                    </button>
                    <button type="button" class="btn btn-sm btn-filter-disease btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="kolesterol">
                        🧪 Kolesterol
                    </button>
                    <button type="button" class="btn btn-sm btn-filter-disease btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="obesitas">
                        ⚖️ Obesitas
                    </button>
                    <button type="button" class="btn btn-sm btn-filter-disease btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold text-xs" data-disease="risiko_tinggi">
                        ⚠️ Risiko Tinggi
                    </button>
                </div>
            </div>

            {{-- MODE TAMPILAN GIS (HEATMAP / CLUSTER / CHOROPLETH / HYBRID) --}}
            <div class="col-12 col-lg-5 text-lg-end">
                <div class="btn-group btn-group-sm shadow-sm rounded-pill p-0.5 bg-gray-100" role="group">
                    <button type="button" class="btn btn-mode active rounded-pill fw-semibold text-xs px-3" data-mode="hybrid" title="Tampilkan Heatmap dengan Marker Aktif & Batas Wilayah">
                        ✨ Hybrid
                    </button>
                    <button type="button" class="btn btn-mode rounded-pill fw-semibold text-xs px-3" data-mode="heatmap" title="Hanya Peta Kepadatan Heat Map">
                        🔥 Heat Map
                    </button>
                    <button type="button" class="btn btn-mode rounded-pill fw-semibold text-xs px-3" data-mode="cluster" title="Hanya Pengelompokan Marker Cluster">
                        📍 Clustering
                    </button>
                    <button type="button" class="btn btn-mode rounded-pill fw-semibold text-xs px-3" data-mode="choropleth" title="Hanya Pewarnaan Zonasi Wilayah Kecamatan">
                        🗺️ Choropleth
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MAP CONTAINER --}}
    <div class="card-body p-0 position-relative">
        <div id="puskesmasMap" style="height: 540px; width: 100%; z-index: 1;" class="gis-map-canvas"></div>
    </div>
</div>

@push('styles')
    <!-- Leaflet & MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <style>
        .ptm-gis-card {
            background: #ffffff;
            transition: all 0.3s ease;
        }
        .btn-filter-disease {
            transition: all 0.2s ease;
        }
        .btn-filter-disease.active {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
        }
        .btn-mode {
            color: #4b5563;
            border: none;
            background: transparent;
            transition: all 0.2s ease;
        }
        .btn-mode.active {
            background: #ffffff !important;
            color: #111827 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }
        .animate-pulse {
            animation: ptmPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes ptmPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(0.98); }
        }

        /* Custom Popup & Tooltip Leaflet */
        .leaflet-popup-content-wrapper {
            border-radius: 16px !important;
            padding: 0 !important;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,0.22) !important;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            line-height: 1.4 !important;
        }
        .pkm-gis-popup {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-width: 270px;
        }
        .pkm-gis-popup .popup-header {
            padding: 12px 14px;
            color: white;
        }
        .pkm-gis-popup .popup-body {
            padding: 12px 14px;
            background: white;
        }
        .kec-tooltip-modern {
            background: rgba(15, 23, 42, 0.94) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.3) !important;
        }
        .kec-tooltip-modern::before {
            border-top-color: rgba(15, 23, 42, 0.94) !important;
        }

        /* Custom Marker Cluster Styling */
        .marker-cluster-high {
            background-color: rgba(239, 68, 68, 0.4) !important;
        }
        .marker-cluster-high div {
            background-color: #ef4444 !important;
            color: white !important;
            font-weight: 800 !important;
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.6);
        }
        .marker-cluster-medium {
            background-color: rgba(249, 115, 22, 0.4) !important;
        }
        .marker-cluster-medium div {
            background-color: #f97316 !important;
            color: white !important;
            font-weight: 800 !important;
        }
        .marker-cluster-low {
            background-color: rgba(16, 185, 129, 0.4) !important;
        }
        .marker-cluster-low div {
            background-color: #10b981 !important;
            color: white !important;
            font-weight: 800 !important;
        }

        /* Pulsing ring for high outbreak pin */
        .pkm-pin-pulse {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pkm-pin-pulse::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #ef4444;
            animation: pkmRing 1.8s ease-out infinite;
        }
        @keyframes pkmRing {
            0% { transform: scale(0.9); opacity: 0.9; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        /* Floating GIS Legend */
        .gis-legend-container {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.14);
            font-size: 11px;
            line-height: 1.4;
            max-width: 220px;
            color: #1e293b;
        }
        .heatmap-gradient-bar {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(to right, #06b6d4, #10b981, #facc15, #f97316, #ef4444);
            margin: 4px 0 6px 0;
        }
    </style>
@endpush

@push('scripts')
    <!-- Leaflet, Heatmap & MarkerCluster Libraries -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('puskesmasMap');
            if (!mapEl) return;

            // Inisialisasi Data dari Backend
            const puskesmasData = {!! json_encode($pkmData) !!};
            const heatDataSets = {!! json_encode($heatPoints) !!};
            const choroplethData = {!! json_encode($choroplethData) !!};
            const diseaseHotspots = {!! json_encode($diseaseHotspots) !!};

            let currentDisease = 'all';
            let currentMode = 'hybrid'; // hybrid, heatmap, cluster, choropleth

            // 1. INTI PETA LEAFLET (DIKUNCI PERSIS PADA SUDUT PANDANG OPTIMAL KOTA BANJARMASIN)
            const banjarmasinSouthWest = L.latLng(-3.43, 114.49); // Batas Selatan (Tamban / Ciputra / Trisakti)
            const banjarmasinNorthEast = L.latLng(-3.21, 114.69); // Batas Utara & Timur (Sungai Lulut / Alalak)
            const banjarmasinBounds = L.latLngBounds(banjarmasinSouthWest, banjarmasinNorthEast);

            const map = L.map('puskesmasMap', {
                center: [-3.3198, 114.5901],
                zoom: 12,
                minZoom: 12,                  // Mencegah zoom out (terkunci persis di lebar optimal)
                maxZoom: 14,                  // Membatasi zoom in agar framing tetap rapi
                maxBounds: banjarmasinBounds, // Mengunci pergeseran peta hanya di area operasional
                maxBoundsViscosity: 1.0,      // Efek pegas elastis 100% mencegah panning keluar area
                scrollWheelZoom: false,       // Mencegah scroll mouse halaman web mengubah zoom secara tidak sengaja
                doubleClickZoom: false,       // Mencegah double click mengubah zoom
                zoomControl: true,
                dragging: true                // Geser peta tetap halus dan nyaman
            });

            // Tombol Reset Kamera ke Posisi Kunci Awal
            const resetViewControl = L.Control.extend({
                options: { position: 'topleft' },
                onAdd: function () {
                    const btn = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    btn.innerHTML = '<a href="javascript:void(0)" title="Reset ke Tampilan Optimal" style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; text-decoration:none; color:#1e293b;"><i class="bi bi-arrows-fullscreen"></i></a>';
                    btn.onclick = function (e) {
                        e.stopPropagation();
                        map.setView([-3.3198, 114.5901], 12);
                    };
                    return btn;
                }
            });
            map.addControl(new resetViewControl());

            // Buat dedicated pane untuk Heatmap agar blending sempurna di bawah marker
            map.createPane('heatmapPane');
            map.getPane('heatmapPane').style.zIndex = 420;

            // 2. BASE MAP TILES
            const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '© Google Maps'
            });

            const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '© Google Satellite'
            });

            const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles © Esri'
            });

            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            });

            // Aktifkan default Satelit Google
            googleSatellite.addTo(map);

            const baseMaps = {
                "🛰️ Satelit (Google)": googleSatellite,
                "🗺️ Peta Standar": googleRoadmap,
                "🌍 Satelit (Esri)": esriSatellite,
                "📍 OpenStreetMap": osmLayer
            };
            L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

            // 3. LAYER GROUPS
            let heatLayer = null;
            let markerClusterGroup = null;
            let geojsonLayer = null;
            let geojsonRawData = null;
            const markerReferences = {};

            // 4. FUNCTION: RENDER HEATMAP LAYER
            function renderHeatmap(diseaseKey) {
                if (heatLayer && map.hasLayer(heatLayer)) {
                    map.removeLayer(heatLayer);
                }

                const rawPoints = heatDataSets[diseaseKey] || heatDataSets['all'] || [];
                if (rawPoints.length === 0) return;

                // Konfigurasi Heatmap Gradien Kepadatan Kasus
                heatLayer = L.heatLayer(rawPoints, {
                    pane: 'heatmapPane',
                    radius: 38,
                    blur: 24,
                    maxZoom: 15,
                    max: 1.0,
                    minOpacity: 0.45,
                    gradient: {
                        0.15: '#06b6d4', // Cyan / Biru Muda (Densitas Ringan)
                        0.35: '#10b981', // Hijau Emerald (Sedang Terkendali)
                        0.60: '#facc15', // Kuning (Konsentrasi Menengah)
                        0.80: '#f97316', // Oranye (Konsentrasi Tinggi)
                        1.00: '#ef4444'  // Merah Membara (Zona Outbreak / Hotspot)
                    }
                });

                if (currentMode === 'heatmap' || currentMode === 'hybrid') {
                    heatLayer.addTo(map);
                }
            }

            // 5. FUNCTION: RENDER MARKER CLUSTERING LAYER
            function renderMarkerCluster(diseaseKey) {
                if (markerClusterGroup && map.hasLayer(markerClusterGroup)) {
                    map.removeLayer(markerClusterGroup);
                }

                // Custom cluster icon dengan kalkulasi dinamis
                markerClusterGroup = L.markerClusterGroup({
                    showCoverageOnHover: false,
                    maxClusterRadius: 40,
                    spiderfyOnMaxZoom: true,
                    iconCreateFunction: function (cluster) {
                        const markers = cluster.getAllChildMarkers();
                        let totalKasus = 0;
                        markers.forEach(m => {
                            totalKasus += (m.options.caseCount || 0);
                        });

                        let cClass = 'marker-cluster-low';
                        if (totalKasus >= 15) {
                            cClass = 'marker-cluster-high';
                        } else if (totalKasus >= 5) {
                            cClass = 'marker-cluster-medium';
                        }

                        return L.divIcon({
                            html: '<div><span>' + totalKasus + '</span></div>',
                            className: 'marker-cluster ' + cClass,
                            iconSize: L.point(40, 40)
                        });
                    }
                });

                puskesmasData.forEach(function (pkm) {
                    if (pkm.lat && pkm.lng) {
                        let displayCount = pkm.total_kasus_ptm;
                        let filterLabel = 'Total Kasus PTM';

                        if (diseaseKey === 'hipertensi') {
                            displayCount = pkm.hipertensi;
                            filterLabel = 'Kasus Hipertensi';
                        } else if (diseaseKey === 'diabetes') {
                            displayCount = pkm.diabetes;
                            filterLabel = 'Kasus Diabetes';
                        } else if (diseaseKey === 'kolesterol') {
                            displayCount = pkm.kolesterol;
                            filterLabel = 'Kasus Kolesterol';
                        } else if (diseaseKey === 'obesitas') {
                            displayCount = pkm.obesitas;
                            filterLabel = 'Kasus Obesitas';
                        } else if (diseaseKey === 'risiko_tinggi') {
                            displayCount = pkm.risiko_tinggi;
                            filterLabel = 'Risiko Tinggi';
                        }

                        let customPinIcon;

                        // JIKA KASUS = 0 PADA FILTER INI: Tampilkan pin netral halus agar tidak numpuk angka 0 merah
                        if (displayCount === 0) {
                            customPinIcon = L.divIcon({
                                className: 'custom-pkm-pin-zero',
                                html: `
                                    <div style="width:14px; height:14px; background:rgba(148, 163, 184, 0.8); border:2px solid white; border-radius:50%; box-shadow:0 1px 4px rgba(0,0,0,0.3);" title="${pkm.nama_puskesmas}: 0 Kasus">
                                    </div>
                                `,
                                iconSize: [14, 14],
                                iconAnchor: [7, 7]
                            });
                        } else {
                            // PIN AKTIF DENGAN KASUS > 0
                            let pinBg = '#10b981'; // Hijau
                            let isPulse = false;
                            if (displayCount >= 8) {
                                pinBg = '#ef4444'; // Merah
                                isPulse = true;
                            } else if (displayCount >= 3) {
                                pinBg = '#f97316'; // Oranye
                            }

                            customPinIcon = L.divIcon({
                                className: 'custom-pkm-pin',
                                html: `
                                    <div class="${isPulse ? 'pkm-pin-pulse' : ''}" style="width:30px; height:30px;">
                                        <div style="width:30px; height:30px; background:${pinBg}; border-radius:50%; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; color:white; font-size:11px; font-weight:800;">
                                            ${displayCount}
                                        </div>
                                    </div>
                                `,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                        }

                        // Breakdown Top Penyakit HTML
                        let breakdownHtml = '';
                        if (pkm.top_penyakit && Object.keys(pkm.top_penyakit).length > 0) {
                            breakdownHtml = '<div style="margin-top:8px; padding-top:8px; border-top:1px dashed #e2e8f0;">' +
                                '<div style="font-size:10px; font-weight:700; color:#64748b; margin-bottom:4px;">DIAGNOSA TERBANYAK:</div>';
                            for (let [peny, c] of Object.entries(pkm.top_penyakit)) {
                                breakdownHtml += `<div style="display:flex; justify-content:space-between; font-size:11px; color:#334155; margin-bottom:2px;">
                                    <span>• ${peny}</span>
                                    <strong style="color:#ef4444;">${c}</strong>
                                </div>`;
                            }
                            breakdownHtml += '</div>';
                        }

                        let headerBg = displayCount > 0 ? (displayCount >= 8 ? '#ef4444' : '#0284c7') : '#64748b';

                        const popupHtml = `
                            <div class="pkm-gis-popup">
                                <div class="popup-header" style="background:${headerBg};">
                                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85; font-weight:700;">FASILITAS KESEHATAN</div>
                                    <div style="font-size:13px; font-weight:800; line-height:1.2;">${pkm.nama_puskesmas}</div>
                                    <div style="font-size:11px; opacity:0.9; margin-top:2px;">Kec. ${pkm.kecamatan}</div>
                                </div>
                                <div class="popup-body">
                                    <div style="font-size:11px; color:#64748b; margin-bottom:10px; display:flex; align-items:start; gap:4px;">
                                        <i class="bi bi-geo-alt-fill text-danger" style="margin-top:1px;"></i>
                                        <span>${pkm.alamat}</span>
                                    </div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; margin-bottom:8px;">
                                        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:6px; border-radius:6px; text-align:center;">
                                            <div style="font-size:10px; color:#64748b; font-weight:600;">Total Skrining</div>
                                            <strong style="color:#0f172a; font-size:13px;">${pkm.total_skrining}</strong>
                                        </div>
                                        <div style="background:#fef2f2; border:1px solid #fecaca; padding:6px; border-radius:6px; text-align:center;">
                                            <div style="font-size:10px; color:#dc2626; font-weight:600;">${filterLabel}</div>
                                            <strong style="color:#dc2626; font-size:13px;">${displayCount}</strong>
                                        </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:4px; text-align:center; font-size:10px; margin-bottom:6px;">
                                        <div style="background:#eff6ff; padding:4px; border-radius:4px;">
                                            <div style="color:#2563eb; font-weight:700;">Hipertensi</div>
                                            <div style="font-weight:800; color:#1e3a8a;">${pkm.hipertensi}</div>
                                        </div>
                                        <div style="background:#faf5ff; padding:4px; border-radius:4px;">
                                            <div style="color:#9333ea; font-weight:700;">Diabetes</div>
                                            <div style="font-weight:800; color:#581c87;">${pkm.diabetes}</div>
                                        </div>
                                        <div style="background:#fff7ed; padding:4px; border-radius:4px;">
                                            <div style="color:#ea580c; font-weight:700;">Kolesterol</div>
                                            <div style="font-weight:800; color:#7c2d12;">${pkm.kolesterol}</div>
                                        </div>
                                    </div>

                                    ${breakdownHtml}

                                    {{-- TOMBOL TINDAKAN SEGERA MONITORING LAPORAN PUSKESMAS --}}
                                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #f1f5f9;">
                                        <a href="/pegawai/verifikasi-laporan/${pkm.id}" class="btn ${displayCount >= 3 ? 'btn-danger' : 'btn-primary'} btn-sm w-100 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-1.5 text-white" style="font-size: 11px; padding: 6px 12px; text-decoration: none; ${displayCount >= 3 ? 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);' : ''}">
                                            <i class="bi bi-shield-fill-exclamation"></i> Tindakan Segera: Monitoring Laporan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;

                        const marker = L.marker([pkm.lat, pkm.lng], {
                            icon: customPinIcon,
                            caseCount: displayCount
                        }).bindPopup(popupHtml);

                        markerClusterGroup.addLayer(marker);
                        markerReferences[pkm.id] = marker;
                    }
                });

                if (currentMode === 'cluster' || currentMode === 'hybrid') {
                    markerClusterGroup.addTo(map);
                }
            }

            // 6. FUNCTION: RENDER CHOROPLETH WILAYAH KECAMATAN (GEOJSON)
            function renderChoropleth(diseaseKey) {
                if (geojsonLayer && map.hasLayer(geojsonLayer)) {
                    map.removeLayer(geojsonLayer);
                }

                function applyGeoJson(data) {
                    geojsonLayer = L.geoJSON(data, {
                        style: function (feature) {
                            const namaKec = feature.properties.name || '';
                            const kStats = choroplethData[namaKec] || {};
                            
                            // Garis Pembatas Kecamatan Hijau Emerald yang jelas & kontras
                            let strokeColor = '#10b981'; // Hijau Emerald
                            let fillColor = '#10b981';
                            let fillOpacity = 0.04; // Sangat transparan halus agar tidak menutupi heatmap
                            let weight = 2.5;
                            let dashArray = '5, 5';

                            if (currentMode === 'choropleth') {
                                // Hanya di mode Choropleth murni poligon diwarnai sesuai zonasi
                                fillColor = kStats.zone_color || '#10b981';
                                fillOpacity = 0.5;
                                strokeColor = '#10b981';
                                weight = 2.5;
                                dashArray = null;
                            } else if (currentMode === 'heatmap') {
                                strokeColor = '#10b981'; // Garis pembatas hijau
                                fillOpacity = 0;
                                weight = 2;
                                dashArray = '4, 4';
                            } else if (currentMode === 'hybrid') {
                                strokeColor = '#10b981'; // Garis pembatas hijau
                                fillOpacity = 0.04;
                                weight = 2.5;
                                dashArray = '5, 5';
                            } else if (currentMode === 'cluster') {
                                strokeColor = '#10b981';
                                fillOpacity = 0.04;
                                weight = 2;
                                dashArray = '4, 4';
                            }

                            return {
                                color: strokeColor,
                                fillColor: fillColor,
                                fillOpacity: fillOpacity,
                                weight: weight,
                                dashArray: dashArray
                            };
                        },
                        onEachFeature: function (feature, layer) {
                            const namaKec = feature.properties.name || '';
                            const kStats = choroplethData[namaKec] || {
                                total_skrining: 0,
                                total_kasus_ptm: 0,
                                prevalensi_rate: 0,
                                dominant_disease: 'N/A',
                                zone_label: 'Normal'
                            };

                            let countForDisease = kStats.total_kasus_ptm;
                            let labelDisease = 'Kasus PTM';
                            if (diseaseKey === 'hipertensi') { countForDisease = kStats.hipertensi; labelDisease = 'Hipertensi'; }
                            else if (diseaseKey === 'diabetes') { countForDisease = kStats.diabetes; labelDisease = 'Diabetes'; }
                            else if (diseaseKey === 'kolesterol') { countForDisease = kStats.kolesterol; labelDisease = 'Kolesterol'; }
                            else if (diseaseKey === 'obesitas') { countForDisease = kStats.obesitas; labelDisease = 'Obesitas'; }
                            else if (diseaseKey === 'risiko_tinggi') { countForDisease = kStats.risiko_tinggi; labelDisease = 'Risiko Tinggi'; }

                            const tooltipContent = `
                                <div style="font-family:'Inter', sans-serif;">
                                    <div style="font-weight:800; font-size:12px; color:#f8fafc;">${namaKec}</div>
                                    <div style="font-size:10px; color:#cbd5e1; margin-top:2px;">Status: <strong>${kStats.zone_label}</strong></div>
                                    <div style="display:flex; justify-content:space-between; gap:12px; font-size:11px; margin-top:4px;">
                                        <span>${labelDisease}: <strong>${countForDisease}</strong></span>
                                        <span>Prevalensi: <strong>${kStats.prevalensi_rate}%</strong></span>
                                    </div>
                                </div>
                            `;

                            layer.bindTooltip(tooltipContent, {
                                sticky: true,
                                className: 'kec-tooltip-modern'
                            });

                            // BIND POPUP DENGAN TOMBOL TINDAKAN SEGERA MONITORING KECAMATAN
                            const polygonPopupContent = `
                                <div class="pkm-gis-popup">
                                    <div class="popup-header" style="background:#0f172a;">
                                        <div style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85; font-weight:700;">ZONASI WILAYAH</div>
                                        <div style="font-size:14px; font-weight:800; line-height:1.2;">${namaKec}</div>
                                        <div style="font-size:11px; color:#38bdf8; margin-top:2px;">Status: <strong>${kStats.zone_label}</strong></div>
                                    </div>
                                    <div class="popup-body">
                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; margin-bottom:8px;">
                                            <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:6px; border-radius:6px; text-align:center;">
                                                <div style="font-size:10px; color:#64748b; font-weight:600;">Total Skrining</div>
                                                <strong style="color:#0f172a; font-size:13px;">${kStats.total_skrining}</strong>
                                            </div>
                                            <div style="background:#fef2f2; border:1px solid #fecaca; padding:6px; border-radius:6px; text-align:center;">
                                                <div style="font-size:10px; color:#dc2626; font-weight:600;">${labelDisease}</div>
                                                <strong style="color:#dc2626; font-size:13px;">${countForDisease}</strong>
                                            </div>
                                        </div>
                                        <div style="font-size:11px; color:#475569; margin-bottom:8px;">
                                            • Prevalensi: <strong>${kStats.prevalensi_rate}%</strong><br>
                                            • Dominan: <strong>${kStats.dominant_disease}</strong><br>
                                            • Fasilitas: <strong>${kStats.puskesmas_count} Puskesmas</strong>
                                        </div>
                                        <a href="/pegawai/verifikasi-laporan?kecamatan=${encodeURIComponent(namaKec.replace('Banjarmasin ', ''))}" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill text-white shadow-sm d-flex align-items-center justify-content-center gap-1.5" style="font-size:11px; padding:6px 12px; text-decoration:none; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                            <i class="bi bi-clipboard2-pulse-fill"></i> Tindakan Segera: Monitoring Wilayah Ini
                                        </a>
                                    </div>
                                </div>
                            `;
                            layer.bindPopup(polygonPopupContent);

                            layer.on({
                                mouseover: function (e) {
                                    const l = e.target;
                                    l.setStyle({
                                        weight: 4,
                                        color: '#059669', // Hijau lebih tajam saat disentuh kursor
                                        fillOpacity: currentMode === 'choropleth' ? 0.7 : 0.15
                                    });
                                },
                                mouseout: function (e) {
                                    geojsonLayer.resetStyle(e.target);
                                }
                            });
                        }
                    });

                    geojsonLayer.addTo(map);
                }

                if (geojsonRawData) {
                    applyGeoJson(geojsonRawData);
                } else {
                    fetch("{{ asset('geojson_banjarmasin.json') }}")
                        .then(response => response.json())
                        .then(data => {
                            geojsonRawData = data;
                            applyGeoJson(data);
                        })
                        .catch(err => console.error("Gagal memuat batas kecamatan GeoJSON:", err));
                }
            }

            // 7. FUNCTION: UPDATE HOTSPOT BANNER SESUAI FILTER PENYAKIT
            function updateHotspotBanner(diseaseKey) {
                const targetHotspot = diseaseHotspots[diseaseKey] || diseaseHotspots['all'] || {};
                const topKec = targetHotspot.top_kecamatan;
                const topPkm = targetHotspot.top_puskesmas || [];

                const bannerEl = document.getElementById('hotspotAlertBanner');
                const badgeTextEl = document.getElementById('hotspotBadgeText');
                const descEl = document.getElementById('hotspotDescription');
                const labelEl = document.getElementById('hotspotZoneLabel');

                const diseaseLabels = {
                    'all': 'Penyakit Tidak Menular (PTM)',
                    'hipertensi': 'Hipertensi',
                    'diabetes': 'Diabetes Melitus',
                    'kolesterol': 'Kolesterol Tinggi',
                    'obesitas': 'Obesitas',
                    'risiko_tinggi': 'Skrining Risiko Tinggi'
                };
                const currentLabel = diseaseLabels[diseaseKey] || 'PTM';

                if (topKec) {
                    let caseCount = topKec.total_kasus_ptm;
                    if (diseaseKey === 'hipertensi') caseCount = topKec.hipertensi;
                    else if (diseaseKey === 'diabetes') caseCount = topKec.diabetes;
                    else if (diseaseKey === 'kolesterol') caseCount = topKec.kolesterol;
                    else if (diseaseKey === 'obesitas') caseCount = topKec.obesitas;
                    else if (diseaseKey === 'risiko_tinggi') caseCount = topKec.risiko_tinggi;

                    let pkmHighlight = '';
                    if (topPkm.length > 0) {
                        let topPkmCount = (diseaseKey === 'all') ? topPkm[0].total_kasus_ptm : (topPkm[0][diseaseKey] ?? 0);
                        pkmHighlight = ` | Puskesmas Utama: <strong>${topPkm[0].nama_puskesmas}</strong> (${topPkmCount} Kasus)`;
                    }

                    badgeTextEl.textContent = `HOTSPOT ${currentLabel.toUpperCase()}`;
                    descEl.innerHTML = `Konsentrasi kasus <strong>${currentLabel}</strong> tertinggi di <strong>${topKec.nama}</strong> (${caseCount} Kasus, Prevalensi: <strong>${topKec.prevalensi_rate}%</strong>)${pkmHighlight}`;
                    labelEl.textContent = topKec.zone_label || 'Monitoring Aktif';

                    // Update tombol Tindakan Segera di banner agar langsung membuka puskesmas/wilayah hotspot
                    const btnAction = document.getElementById('btnActionHotspot');
                    const btnActionText = document.getElementById('btnActionHotspotText');
                    if (btnAction && topPkm.length > 0) {
                        btnAction.href = `/pegawai/verifikasi-laporan/${topPkm[0].id}`;
                        if (btnActionText) {
                            btnActionText.textContent = `Tindakan Segera: Monitoring ${topPkm[0].nama_puskesmas}`;
                        }
                    } else if (btnAction) {
                        btnAction.href = `/pegawai/verifikasi-laporan?kecamatan=${encodeURIComponent(topKec.nama.replace('Banjarmasin ', ''))}`;
                        if (btnActionText) {
                            btnActionText.textContent = `Tindakan Segera: Monitoring ${topKec.nama}`;
                        }
                    }
                }

                // Update juga opsi di Select Dropdown Puskesmas agar relevan
                const selectPkm = document.getElementById('pilihPuskesmasMap');
                if (selectPkm) {
                    let currentVal = selectPkm.value;
                    let html = '<option value="">🎯 Semua Puskesmas (Overview)</option>';
                    puskesmasData.forEach(pkm => {
                        let c = (diseaseKey === 'all') ? pkm.total_kasus_ptm : (pkm[diseaseKey] ?? 0);
                        html += `<option value="${pkm.id}">${pkm.nama_puskesmas} (${c} Kasus ${currentLabel})</option>`;
                    });
                    selectPkm.innerHTML = html;
                    selectPkm.value = currentVal;
                }
            }

            // 8. FUNCTION: UPDATE ALL MAP LAYERS BERDASARKAN FILTER & MODE
            function updateMapLayers() {
                updateHotspotBanner(currentDisease);
                renderHeatmap(currentDisease);
                renderMarkerCluster(currentDisease);
                renderChoropleth(currentDisease);

                // Sinkronkan visibilitas layer berdasarkan currentMode
                if (currentMode === 'heatmap') {
                    if (markerClusterGroup && map.hasLayer(markerClusterGroup)) map.removeLayer(markerClusterGroup);
                    if (heatLayer && !map.hasLayer(heatLayer)) heatLayer.addTo(map);
                } else if (currentMode === 'cluster') {
                    if (heatLayer && map.hasLayer(heatLayer)) map.removeLayer(heatLayer);
                    if (markerClusterGroup && !map.hasLayer(markerClusterGroup)) markerClusterGroup.addTo(map);
                } else if (currentMode === 'choropleth') {
                    if (heatLayer && map.hasLayer(heatLayer)) map.removeLayer(heatLayer);
                    if (markerClusterGroup && map.hasLayer(markerClusterGroup)) map.removeLayer(markerClusterGroup);
                } else if (currentMode === 'hybrid') {
                    if (heatLayer && !map.hasLayer(heatLayer)) heatLayer.addTo(map);
                    if (markerClusterGroup && !map.hasLayer(markerClusterGroup)) markerClusterGroup.addTo(map);
                }
            }

            // Inisialisasi Pertama
            updateMapLayers();

            // 9. EVENT HANDLERS: FILTER PENYAKIT
            document.querySelectorAll('.btn-filter-disease').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-filter-disease').forEach(b => {
                        b.classList.remove('active');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.add('active');
                    this.classList.remove('btn-outline-secondary');

                    currentDisease = this.dataset.disease;
                    updateMapLayers();
                });
            });

            // 10. EVENT HANDLERS: MODE TAMPILAN
            document.querySelectorAll('.btn-mode').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-mode').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    currentMode = this.dataset.mode;
                    updateMapLayers();
                });
            });

            // 11. EVENT HANDLERS: PENCARIAN / ZOOM KE PUSKESMAS
            const selectPuskesmas = document.getElementById('pilihPuskesmasMap');
            if (selectPuskesmas) {
                selectPuskesmas.addEventListener('change', function () {
                    const selectedId = this.value;
                    if (!selectedId) {
                        map.setView([-3.3198, 114.5901], 12);
                    } else {
                        const targetMarker = markerReferences[selectedId];
                        if (targetMarker) {
                            if (markerClusterGroup && map.hasLayer(markerClusterGroup)) {
                                markerClusterGroup.zoomToShowLayer(targetMarker, function () {
                                    targetMarker.openPopup();
                                });
                            } else {
                                map.setView(targetMarker.getLatLng(), 13);
                                targetMarker.openPopup();
                            }
                        }
                    }
                });
            }

            // 12. EVENT HANDLERS: TOMBOL FOKUS HOTSPOT TERBESAR
            const btnFocusHotspot = document.getElementById('btnFocusHotspot');
            if (btnFocusHotspot) {
                btnFocusHotspot.addEventListener('click', function () {
                    const targetHotspot = diseaseHotspots[currentDisease] || diseaseHotspots['all'] || {};
                    const topPkmList = targetHotspot.top_puskesmas || [];

                    if (topPkmList.length > 0) {
                        const topPkm = topPkmList[0];
                        if (topPkm && topPkm.lat && topPkm.lng) {
                            map.flyTo([topPkm.lat, topPkm.lng], 13, {
                                animate: true,
                                duration: 1.2
                            });

                            setTimeout(() => {
                                const targetMarker = markerReferences[topPkm.id];
                                if (targetMarker) {
                                    if (markerClusterGroup && map.hasLayer(markerClusterGroup)) {
                                        markerClusterGroup.zoomToShowLayer(targetMarker, function () {
                                            targetMarker.openPopup();
                                        });
                                    } else {
                                        targetMarker.openPopup();
                                    }
                                }
                            }, 1300);
                        }
                    }
                });
            }

            // 13. FLOATING LEGENDA PETA
            const legendControl = L.control({ position: 'bottomright' });
            legendControl.onAdd = function () {
                const div = L.DomUtil.create('div', 'gis-legend-container');
                div.innerHTML = `
                    <div style="font-weight:800; font-size:12px; margin-bottom:4px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">
                        📊 Sebaran PTM
                    </div>
                    
                    <div style="margin-bottom:6px;">
                        <div style="font-size:10px; font-weight:700; color:#64748b;">Intensitas Panas (Heatmap)</div>
                        <div class="heatmap-gradient-bar"></div>
                        <div style="display:flex; justify-content:space-between; font-size:9px; color:#64748b;">
                            <span>Rendah</span>
                            <span>Sedang</span>
                            <span style="color:#ef4444; font-weight:bold;">Outbreak</span>
                        </div>
                    </div>

                    <div style="border-top:1px solid #f1f5f9; padding-top:4px;">
                        <div style="font-size:10px; font-weight:700; color:#64748b; margin-bottom:3px;">Marker Puskesmas</div>
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                            <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                            <span>Kasus Tinggi (≥8)</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                            <span style="width:10px; height:10px; border-radius:50%; background:#f97316; display:inline-block;"></span>
                            <span>Kasus Sedang (3–7)</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; margin-bottom:2px;">
                            <span style="width:10px; height:10px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                            <span>Kasus Rendah (1–2)</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="width:8px; height:8px; border-radius:50%; background:#94a3b8; display:inline-block;"></span>
                            <span>Nihil Kasus (0)</span>
                        </div>
                    </div>
                `;
                return div;
            };
            legendControl.addTo(map);
        });
    </script>
@endpush
