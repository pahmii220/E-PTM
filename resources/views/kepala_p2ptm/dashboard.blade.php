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
    $totalPetugas = User::where('role_name', 'petugas')->count();
    $totalPegawai = \App\Models\PegawaiDinkes::count();

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
    @endphp

    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">

        {{-- ================= SPANDUK SAMBUTAN ================= --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-4 p-3.5 shadow-lg mb-4 relative overflow-hidden text-white d-flex justify-content-between align-items-center">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 opacity-20">
                <i class="bi bi-hexagon-fill" style="font-size: 15rem;"></i>
            </div>

            <div class="relative z-10">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="bi {{ $ikonUcapan }} fs-2"></i>
                    <h2 class="fw-bold mb-0 tracking-wide">{{ $ucapan }}, Kepala P2PTM!</h2>
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
                    ['title' => 'Total Pasien', 'value' => $data['totalPeserta'], 'icon' => 'bi-people', 'color' => 'primary'],
                    ['title' => 'Deteksi Dini', 'value' => $data['totalDeteksi'], 'icon' => 'bi-activity', 'color' => 'success'],
                    ['title' => 'Risiko Tinggi', 'value' => $data['totalRisiko'], 'icon' => 'bi-exclamation-triangle', 'color' => 'danger'],
                    ['title' => 'Petugas Puskesmas', 'value' => $totalPetugas, 'icon' => 'bi-person-badge-fill', 'color' => 'info'],
                    ['title' => 'Pegawai Dinkes', 'value' => $totalPegawai, 'icon' => 'bi-person-vcard-fill', 'color' => 'warning']
                ];
            @endphp

            @foreach($cards as $card)
            <div class="col-xl col-md-4 col-sm-6">
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
                            <h4 class="fw-bold mb-0 text-dark">Sebaran Pasien per Puskesmas</h4>
                        </div>
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
        {{-- BARIS 3: PETA SEBARAN LOKASI PUSKESMAS --}}
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden transition-hover">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="bg-blue-50 text-blue-600 p-2 rounded-3 me-3"><i class="bi bi-geo-alt-fill fs-4"></i></div>
                            <h4 class="fw-bold mb-0 text-dark">Peta Sebaran Lokasi Wilayah Puskesmas</h4>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <select id="pilihPuskesmas" class="form-select form-select-sm shadow-sm" style="min-width: 250px; border-radius: 8px;">
                                <option value="">Pilih Puskesmas...</option>
                                @if(isset($mapPuskesmasData))
                                    @foreach($mapPuskesmasData as $pkm)
                                        <option value="{{ $pkm->id }}">{{ $pkm->nama_puskesmas }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div id="puskesmasMap" style="height: 450px; width: 100%; z-index: 1;"></div>
                    </div>
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
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // INISIALISASI PETA SEBARAN PUSKESMAS KEPALA P2PTM
            var mapElement = document.getElementById('puskesmasMap');
            if (mapElement) {
                var map = L.map('puskesmasMap', {
                    minZoom: 12
                }).setView([-3.316694, 114.590111], 12);

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

                // Default Active Layer (Langsung Peta Satelit)
                googleSatellite.addTo(map);

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
                                let color = '#3388ff';
                                let fillColor = '#3388ff';
                                const nama = feature.properties.name || '';
                                if (nama.includes('Utara')) { fillColor = '#facc15'; color = '#ca8a04'; }
                                else if (nama.includes('Barat')) { fillColor = '#38bdf8'; color = '#0284c7'; }
                                else if (nama.includes('Tengah')) { fillColor = '#c084fc'; color = '#9333ea'; }
                                else if (nama.includes('Timur')) { fillColor = '#f87171'; color = '#dc2626'; }
                                else if (nama.includes('Selatan')) { fillColor = '#fbcfe8'; color = '#db2777'; }

                                return { color: color, fillColor: fillColor, fillOpacity: 0.35, weight: 2, dashArray: '3' };
                            },
                            onEachFeature: function (feature, layer) {
                                if (feature.properties && feature.properties.name) {
                                    layer.bindTooltip("<strong>" + feature.properties.name + "</strong>", { permanent: false, direction: "center" });
                                }
                            }
                        }).addTo(map);
                    })
                    .catch(err => console.error("GeoJSON error:", err));

                var mapMarkers = {};
                var bounds = [];
                var puskesmasList = {!! json_encode($mapPuskesmasData ?? []) !!};

                if (puskesmasList && puskesmasList.length > 0) {
                    puskesmasList.forEach(function(pkm) {
                        if (pkm.latitude && pkm.longitude) {
                            var lat = parseFloat(pkm.latitude);
                            var lng = parseFloat(pkm.longitude);

                            if (!isNaN(lat) && !isNaN(lng)) {
                                var alamatLengkap = pkm.alamat || 'Alamat tidak tersedia';
                                var statusBadge = pkm.deteksi_dini_count > 0 
                                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Melaporkan (' + pkm.deteksi_dini_count + ' Data)</span>'
                                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Belum Ada Laporan</span>';

                                var popupContent = '<div class="p-2" style="min-width: 220px;">' +
                                    '<h6 class="fw-bold mb-1 text-primary"><i class="bi bi-hospital me-1"></i>' + pkm.nama_puskesmas + '</h6>' +
                                    '<p class="small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>' + alamatLengkap + '</p>' +
                                    '<div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">' +
                                        '<span class="small text-secondary">Status PTM:</span>' + statusBadge +
                                    '</div></div>';

                                var marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                                mapMarkers[pkm.id] = marker;
                                bounds.push([lat, lng]);
                            }
                        }
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [40, 40] });
                    }
                }

                var selectPuskesmas = document.getElementById('pilihPuskesmas');
                if (selectPuskesmas) {
                    selectPuskesmas.addEventListener('change', function () {
                        var pkmId = this.value;
                        if (pkmId && mapMarkers[pkmId]) {
                            var targetMarker = mapMarkers[pkmId];
                            map.setView(targetMarker.getLatLng(), 16, { animate: true });
                            targetMarker.openPopup();
                        } else if (bounds.length > 0) {
                            map.fitBounds(bounds, { padding: [40, 40] });
                        }
                    });
                }
            }

            Chart.defaults.font.family = "'Segoe UI', sans-serif";
            
            // Fungsi buat gradient vertikal
            function buatGradient(ctx, r, g, b) {
                const grad = ctx.createLinearGradient(0, 0, 0, 320);
                grad.addColorStop(0,   `rgba(${r},${g},${b},0.95)`);
                grad.addColorStop(1,   `rgba(${r},${g},${b},0.35)`);
                return grad;
            }

            const ctxPuskesmas = document.getElementById('puskesmasChart').getContext('2d');
            const labelsPuskesmas = {!! json_encode($puskesmasLabels) !!};

            // Bar Chart: PUSKESMAS
            new Chart(ctxPuskesmas, {
                type: 'bar',
                data: {
                    labels: labelsPuskesmas,
                    datasets: [{
                        label: 'Pasien Terdaftar',
                        data: {!! json_encode($puskesmasData) !!},
                        backgroundColor: buatGradient(ctxPuskesmas, 59, 130, 246),
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 0,
                        borderRadius: { topLeft: 6, topRight: 6 },
                        borderSkipped: false,
                        barThickness: 18,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
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
                                    const val = ctx.raw.toLocaleString('id-ID');
                                    return `  👥 ${ctx.dataset.label}: ${val} data`;
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

            // Doughnut Chart: SKRINING
            @if($totalSkrining > 0)
            new Chart(document.getElementById('skriningChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'Dicurigai', 'Risiko Tinggi'],
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
        });
    </script>
@endpush