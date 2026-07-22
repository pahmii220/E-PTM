<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Statistik Pemeriksaan PTM</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            margin: 15mm 12mm;
            size: landscape;
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 6px 8px;
            box-sizing: border-box;
        }

        /* ====== KOP ====== */
        .kop {
            text-align: center;
            margin-bottom: 6px;
            position: relative;
        }

        .kop .left {
            float: left;
            width: 80px;
        }

        .kop .center {
            display: inline-block;
            width: calc(100% - 160px);
            text-align: center;
        }

        .clear {
            clear: both;
        }

        .kop .prov {
            font-size: 16px;
            font-weight: 700;
        }

        .kop .dinas {
            font-size: 22px;
            font-weight: 900;
            margin-top: 2px;
        }

        .kop .addr {
            font-size: 13px;
            margin-top: 6px;
        }

        hr.top {
            border: none;
            border-top: 2px solid #000;
            margin: 8px 0 12px 0;
        }

        /* ====== PAGE BREAK & CHART ====== */
        .page-break {
            page-break-before: always;
        }

        .chart-container {
            width: 100%;
            height: 250px;
            margin: 15px auto;
        }

        /* ====== TABLE ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            table-layout: fixed;
            margin-top: 10px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 4px;
            vertical-align: middle;
            word-wrap: break-word;
            text-align: center;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
        }

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        .no-print button {
            padding: 8px 14px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .no-print .btn-print {
            background: #dc3545;
            color: #fff;
            border-color: #dc3545;
            margin-right: 6px;
        }

        .no-print .btn-close {
            background: #eee;
            color: #000;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* ====== TTD & QR CODE ====== */
        .ttd {
            width: 100%;
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd .block {
            width: 250px;
            text-align: center;
            font-size: 12px;
        }

        .ttd .block .name {
            margin-top: 50px;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    @php
        $bulanIndo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
        $namaBulan = $bulanIndo[\Carbon\Carbon::parse($startDate)->format('m')] . ' ' . \Carbon\Carbon::parse($startDate)->format('Y');

        // Ambil puskesmas yang memiliki data PTM > 0
        $validPuskesmas = [];
        foreach($matriksLaporan as $row) {
            $totalPenyakitPkm = array_sum($row['ptm']);
            if($totalPenyakitPkm > 0) {
                $validPuskesmas[] = $row;
            }
        }

        $pkmLabels = [];
        $datasetData = [];

        // Buat warna unik per penyakit (opsional: 10 warna utama)
        $colors = [
            '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', 
            '#0dcaf0', '#0d6efd', '#6610f2', '#d63384', '#6c757d',
            '#000000', '#795548', '#9c27b0', '#673ab7', '#3f51b5',
            '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#cddc39'
        ];

        // Cari penyakit yang "ada kasusnya" di antara semua validPuskesmas agar legend tidak penuh
        $activePenyakit = [];
        foreach($penyakitList as $p) {
            $hasCase = false;
            foreach($validPuskesmas as $pkm) {
                if($pkm['ptm'][$p] > 0) {
                    $hasCase = true;
                    break;
                }
            }
            if($hasCase) {
                $activePenyakit[] = $p;
            }
        }

        // Siapkan dataset chart.js
        $colorIndex = 0;
        foreach($activePenyakit as $penyakit) {
            $dataPenyakit = [];
            foreach($validPuskesmas as $pkm) {
                $dataPenyakit[] = $pkm['ptm'][$penyakit];
            }
            
            $datasetData[] = [
                'label' => $penyakit,
                'data' => $dataPenyakit,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'maxBarThickness' => 45, // LIMIT LEBAR BATANG
            ];
            $colorIndex++;
        }

        foreach($validPuskesmas as $pkm) {
            $pkmLabels[] = str_replace('Puskesmas ', '', $pkm['puskesmas']);
        }
    @endphp

    <div class="container">

        {{-- TOMBOL AKSES --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak PDF</button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
        
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width:70px;">
            </div>
            <br>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">
                    Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116
                </div>
            </div>
            <div class="clear"></div>
        </div>
        
        <hr class="top">

        <h3 style="text-align: center; font-size:15px; margin:10px 0 5px;">
            LAPORAN STATISTIK PEMERIKSAAN PTM<br>
            @if($kecFilter)
                KECAMATAN {{ strtoupper($kecFilter) }}
            @elseif($kotaFilter)
                KOTA/KABUPATEN {{ strtoupper($kotaFilter) }}
            @else
                PROVINSI KALIMANTAN SELATAN
            @endif
        </h3>
        <p style="text-align: center; font-size:13px; margin:0 0 15px;">Periode: {{ $namaBulan }}</p>

        @if(count($validPuskesmas) == 0)
            <h4 style="text-align: center; color: #666; margin-top: 50px;">Belum ada data pemeriksaan PTM pada periode ini.</h4>
        @else
            {{-- GRAFIK --}}
            <div class="chart-container">
                <canvas id="ptmChart"></canvas>
            </div>
            <p style="text-align: center; font-size: 11px; color: #555; margin-bottom: 10px; margin-top: -5px;">
                * Grafik di atas menampilkan sebaran jenis Penyakit Tidak Menular (PTM) di masing-masing puskesmas.
            </p>

            {{-- TABEL RINCIAN --}}
            <h4 style="text-align: center; margin-bottom: 5px; font-size: 14px;">TABEL RINCIAN KASUS PTM PER PUSKESMAS</h4>
            <table class="grid">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 150px; text-align: left;">Puskesmas</th>
                        <th style="width: 50px;">Total</th>
                        @foreach($activePenyakit as $penyakit)
                            <th>{{ $penyakit }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($validPuskesmas as $no => $pkm)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td style="text-align: left; font-weight: bold;">{{ $pkm['puskesmas'] }}</td>
                        <td style="font-weight: bold; background: #f8f9fa;">{{ array_sum($pkm['ptm']) }}</td>
                        @foreach($activePenyakit as $penyakit)
                            <td>{{ $pkm['ptm'][$penyakit] > 0 ? $pkm['ptm'][$penyakit] : '-' }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div class="block">
                <div>Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div>Pegawai Dinas Kesehatan</div>
                <div class="name">______________________________</div>
                <div>NIP. ........................................</div>
            </div>
        </div>

    </div>

    @if(count($validPuskesmas) > 0)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('ptmChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($pkmLabels) !!},
                    datasets: {!! json_encode($datasetData) !!}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 10, family: '"Times New Roman", serif' }, boxWidth: 12 }
                        }
                    },
                    scales: {
                        x: { stacked: true, ticks: { font: { family: '"Times New Roman", serif', size: 11 } } },
                        y: { stacked: true, beginAtZero: true, ticks: { font: { family: '"Times New Roman", serif' }, stepSize: 1 } }
                    },
                    animation: {
                        onComplete: function() {
                            // Jeda sebentar agar canvas selesai dirender
                            if(!window.hasPrinted) {
                                window.hasPrinted = true;
                                setTimeout(() => { window.print(); }, 500);
                            }
                        }
                    }
                }
            });
        });
    </script>
    @else
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
    @endif
</body>
</html>
