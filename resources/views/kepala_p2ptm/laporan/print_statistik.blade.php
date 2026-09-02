<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Statistik Pemeriksaan PTM Tahunan</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            margin: 15mm 12mm;
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
            max-width: 1000px;
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
            font-size: 14px;
            font-weight: 700;
        }

        .kop .dinas {
            font-size: 18px;
            font-weight: 900;
            margin-top: 2px;
        }

        .kop .addr {
            font-size: 12px;
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
            width: 95%;
            height: 380px;
            margin: 20px auto;
        }

        /* ====== TABLE ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            margin-top: 15px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 6px 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            text-align: center;
        }

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 15px;
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
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd .block {
            width: 42%;
            text-align: center;
            font-size: 12px;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 8px 0;
        }

        .ttd .block .name {
            margin-top: 4px;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container">

        {{-- TOMBOL AKSES --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak PDF</button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>

        {{-- ========================================================== --}}
        {{-- HALAMAN 1: KOP, JUDUL & GRAFIK SEBARAN BULANAN (COMPARE) --}}
        {{-- ========================================================== --}}
        
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width:65px;">
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

        {{-- JUDUL LAPORAN --}}
        <div style="width:100%; text-align:center; margin-bottom:10px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; font-weight:700; text-transform: uppercase;">
                Laporan Statistik Pemeriksaan PTM (Tahun {{ $tahun }})
            </h3>
        </div>
        <div style="width:100%; text-align:center; margin-bottom:20px;">
            <p style="margin:0 auto; font-size:12px; line-height:1.5; max-width:800px; color:#333;">
                Laporan ini menampilkan statistik jumlah pemeriksaan Penyakit Tidak Menular (PTM) yang meliputi pendaftaran Peserta Baru, pendataan Faktor Risiko, dan pemeriksaan klinis Deteksi Dini di wilayah kerja Provinsi Kalimantan Selatan selama tahun berjalan.
            </p>
        </div>


        {{-- KANVAS GRAFIK BULANAN --}}
        <div class="chart-container">
            <canvas id="printPuskesmasChart"></canvas>
        </div>

        <div style="font-size: 11px; text-align: center; color: #555; margin-top: 10px;">
            * Grafik di atas menampilkan komparasi volume data pendaftaran Peserta Baru, Pemeriksaan Deteksi Dini, dan Faktor Risiko setiap bulan.
        </div>

        {{-- ========================================================== --}}
        {{-- HALAMAN 2: TABEL DETIL DATA & PENGESAHAN --}}
        {{-- ========================================================== --}}
        <div class="page-break"></div>

        <div style="width:100%; text-align:center; margin-bottom:10px; margin-top: 10px;">
            <h3 style="margin:0; font-size:14px; letter-spacing:0.6px; font-weight:700; text-transform: uppercase;">
                Tabel Rincian Jumlah Pemeriksaan PTM Per Bulan
            </h3>
        </div>


        <table class="grid">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Bulan</th>
                    <th>Jumlah Peserta Baru</th>
                    <th>Jumlah Faktor Risiko</th>
                    <th>Jumlah Deteksi Dini</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPeserta = 0;
                    $totalFaktor = 0;
                    $totalDeteksi = 0;
                @endphp
                @foreach($statistikBulanan as $i => $row)
                    @php
                        $totalPeserta += $row['total_peserta'];
                        $totalFaktor += $row['total_faktor'];
                        $totalDeteksi += $row['total_deteksi'];
                    @endphp
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td style="font-weight: bold; text-align: left;">{{ $row['nama_bulan'] }}</td>
                        <td style="text-align:center">{{ $row['total_peserta'] }}</td>
                        <td style="text-align:center">{{ $row['total_faktor'] }}</td>
                        <td style="text-align:center">{{ $row['total_deteksi'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="2" style="text-align: center;">TOTAL KESELURUHAN</td>
                    <td style="text-align: center;">{{ $totalPeserta }}</td>
                    <td style="text-align: center;">{{ $totalFaktor }}</td>
                    <td style="text-align: center;">{{ $totalDeteksi }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- TTD KEPALA BIDANG P2PTM & QR --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:8px; font-weight: bold; text-transform: uppercase;">
                    {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                </div>

                <div class="qr-container">
                    @if(isset($qrToken))
                        @php
    $judul = 'Laporan Statistik Pemeriksaan PTM Tahunan';
    $periode = 'Tahun ' . $tahun;
    $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');
    $namaPejabat = $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS';
    $nipPejabat = $kepalaAktif->nip ?? '197008081990031003';
                        @endphp

                        {!! QrCode::size(80)->generate(\App\Helpers\DocumentSigner::url([
                            'judul' => $judul,
                            'periode' => $periode,
                            'tanggal_sah' => $tanggalSah,
                            'nama_kepala' => $namaPejabat,
                            'nip' => $nipPejabat,
                            'jabatan' => $kepalaAktif->jabatan ?? 'Kepala Bidang P2PTM',
                            'catatan' => request('catatan_pengesahan') ?? 'Laporan statistik agregat capaian pemeriksaan PTM tahunan telah diverifikasi dan disahkan.'
                        ])) !!}
                    @else
                        <div style="height: 80px;"></div>
                    @endif
                </div>

                <div class="name" style="margin-top: 0;">
                    {{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                </div>
                <div style="margin-top:3px; font-size: 11px;">
                    NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}
                </div>
            </div>
        </div>

    </div>

    {{-- Render Grouped Bar Chart Bulanan (Batang Lebih Kecil & Tipis) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('printPuskesmasChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($bulanLabels) !!},
                    datasets: [
                        {
                            label: 'Peserta',
                            data: {!! json_encode($pesertaBulanan) !!},
                            backgroundColor: '#3b82f6', // Biru
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 2,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6,
                            maxBarThickness: 15
                        },
                        {
                            label: 'Faktor Risiko',
                            data: {!! json_encode($faktorBulanan) !!},
                            backgroundColor: '#ef4444', // Merah
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            borderRadius: 2,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6,
                            maxBarThickness: 15
                        },
                        {
                            label: 'Deteksi Dini',
                            data: {!! json_encode($deteksiBulanan) !!},
                            backgroundColor: '#10b981', // Hijau
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 2,
                            barPercentage: 0.7,
                            categoryPercentage: 0.6,
                            maxBarThickness: 15
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { 
                            position: 'right',
                            labels: {
                                font: { family: 'Times New Roman', size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { family: 'Times New Roman', size: 12 } },
                            grid: { borderDash: [3, 3] }
                        },
                        x: {
                            ticks: { font: { family: 'Times New Roman', size: 11, weight: 'bold' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
