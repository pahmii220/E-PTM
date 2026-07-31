<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Evaluasi Sistem SUS</title>
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
            width: 90%;
            height: 340px;
            margin: 15px auto;
        }

        /* ====== TABLE ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            margin-top: 15px;
            margin-bottom: 15px;
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

        table.grid td {
            text-align: left;
        }

        table.grid tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* ====== REKAP BOX ====== */
        .rekap-box {
            font-size: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            background: #fafafa;
            padding: 10px 15px;
            border-radius: 4px;
        }

        .rekap-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .rekap-box td {
            padding: 3px 5px;
            vertical-align: top;
        }

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        .no-print button, .no-print a {
            padding: 8px 12px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            border: 1px solid #ccc;
            text-decoration: none;
            display: inline-block;
        }

        .no-print .btn-print {
            background: #198754;
            color: #fff;
            border-color: #198754;
            margin-right: 6px;
        }

        .no-print .btn-close {
            background: #eee;
            color: #000;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* ====== TTD & QR CODE ====== */
        .ttd {
            width: 100%;
            margin-top: 20px;
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
            height: 80px;
            margin: 6px 0;
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

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak Laporan</button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>

        {{-- ========================================================== --}}
        {{-- HALAMAN 1: KOP, JUDUL, DESKRIPSI & GRAFIK HORIZONTAL --}}
        {{-- ========================================================== --}}
        
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="left"><img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:65px;"></div>
            <br>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732 <br>
(Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
            </div>
            <div class="clear"></div>
        </div>
        <hr class="top">

        {{-- JUDUL --}}
        <div style="text-align:center; margin-bottom:10px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; text-transform: uppercase;">
                LAPORAN HASIL SURVEI KEPUASAN PETUGAS PUSKESMAS TERHADAP SISTEM
            </h3>
        </div>

        {{-- DESKRIPSI SINGKAT --}}
        <div style="text-align:center; margin-bottom:15px;">
            <p style="margin:0 auto; font-size:12px; line-height:1.5; max-width:850px; color:#333;">
                Berikut merupakan hasil rata-rata survei terhadap kepuasan pelayanan sistem E-PTM Dinas Kesehatan Provinsi Kalimantan Selatan, berdasarkan jawaban dari 10 pertanyaan inti System Usability Scale (SUS) yang diajukan kepada Petugas Puskesmas.
            </p>
        </div>

        {{-- KANVAS GRAFIK BATANG HORIZONTAL --}}
        <div style="text-align: center; font-weight: bold; font-size: 13px; margin-top: 5px;">Grafik Rata-rata Hasil Survei (Skala 1 - 5)</div>
        <div class="chart-container">
            <canvas id="susChartHorizontal"></canvas>
        </div>

        {{-- RINGKASAN HASIL EVALUASI (TOTAL KESELURUHAN) --}}
        @php
            $namaBulanIndoEval = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            if (request('filter_waktu') == 'tanggal' && request('tgl_awal') && request('tgl_akhir')) {
                $tA = \Carbon\Carbon::parse(request('tgl_awal'));
                $tB = \Carbon\Carbon::parse(request('tgl_akhir'));
                $strPeriodeEvaluasi = $tA->format('d') . ' ' . ($namaBulanIndoEval[(int)$tA->format('m')] ?? '') . ' ' . $tA->format('Y') . ' s/d ' . $tB->format('d') . ' ' . ($namaBulanIndoEval[(int)$tB->format('m')] ?? '') . ' ' . $tB->format('Y');
            } elseif (request('bulan')) {
                $bNum = (int) request('bulan');
                $strPeriodeEvaluasi = ($namaBulanIndoEval[$bNum] ?? '') . ' ' . request('tahun', date('Y'));
            } else {
                $nowEv = \Carbon\Carbon::now();
                $strPeriodeEvaluasi = ($namaBulanIndoEval[(int)$nowEv->format('m')] ?? '') . ' ' . $nowEv->format('Y');
            }

            $nowMakassar = \Carbon\Carbon::now()->setTimezone('Asia/Makassar');
            $strTglCetak = $nowMakassar->format('d') . ' ' . ($namaBulanIndoEval[(int)$nowMakassar->format('m')] ?? '') . ' ' . $nowMakassar->format('Y') . ' - ' . $nowMakassar->format('H:i');
        @endphp
        <div class="rekap-box">
            <table style="font-size: 12px; line-height: 1.4;">
                <tr>
                    <td style="width: 30%;"><strong>Periode Laporan</strong></td>
                    <td style="width: 2%;">:</td>
                    <td><strong>{{ $strPeriodeEvaluasi }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Cetak</strong></td>
                    <td>:</td>
                    <td>{{ $strTglCetak }} WITA</td>
                </tr>
                <tr>
                    <td><strong>Jumlah Responden</strong></td>
                    <td>:</td>
                    <td>{{ $totalResponden }} Orang Petugas</td>
                </tr>
                <tr>
                    <td><strong>Rata-rata Skor SUS Akhir</strong></td>
                    <td>:</td>
                    <td><strong>{{ $rataRataSkor }} / 100</strong> (Tingkat Penerimaan: <span style="color: #198754; font-weight: bold;">{{ $predikat }}</span>)</td>
                </tr>
                <tr>
                    <td><strong>Kesimpulan</strong></td>
                    <td>:</td>
                    <td><em>{{ $keterangan }}</em></td>
                </tr>
            </table>
        </div>

        {{-- ========================================================== --}}
        {{-- HALAMAN 2: TABEL RINCIAN PERTANYAAN & TTD --}}
        {{-- ========================================================== --}}
        <div class="page-break"></div>

        {{-- KOP HALAMAN 2 --}}
        <div class="kop">
            <div class="left"><img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:65px;"></div>
            <br>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
            </div>
            <div class="clear"></div>
        </div>
        <hr class="top">

        <div style="text-align:center; margin-bottom:15px; margin-top: 10px;">
            <h3 style="margin:0; font-size:14px; letter-spacing:0.6px; text-transform: uppercase;">
                Tabel Hasil Rata-Rata Jawaban per Pertanyaan Kuesioner
            </h3>
        </div>

        @php
            // Hitung rata-rata tiap pertanyaan dari data di DB
            $q1_avg = $totalResponden > 0 ? round($semuaData->avg('q1'), 2) : 0;
            $q2_avg = $totalResponden > 0 ? round($semuaData->avg('q2'), 2) : 0;
            $q3_avg = $totalResponden > 0 ? round($semuaData->avg('q3'), 2) : 0;
            $q4_avg = $totalResponden > 0 ? round($semuaData->avg('q4'), 2) : 0;
            $q5_avg = $totalResponden > 0 ? round($semuaData->avg('q5'), 2) : 0;
            $q6_avg = $totalResponden > 0 ? round($semuaData->avg('q6'), 2) : 0;
            $q7_avg = $totalResponden > 0 ? round($semuaData->avg('q7'), 2) : 0;
            $q8_avg = $totalResponden > 0 ? round($semuaData->avg('q8'), 2) : 0;
            $q9_avg = $totalResponden > 0 ? round($semuaData->avg('q9'), 2) : 0;
            $q10_avg = $totalResponden > 0 ? round($semuaData->avg('q10'), 2) : 0;

            // Daftar pertanyaan SUS standar
            $pertanyaanList = [
                1 => "Seberapa mudah Anda menemukan informasi yang dibutuhkan di aplikasi ini?",
                2 => "Bagaimana pendapat Anda tentang tampilan antarmuka (interface) aplikasi ini?",
                3 => "Seberapa cepat proses pengajuan layanan melalui aplikasi ini?",
                4 => "Apakah Anda merasa terbantu dengan fitur status dan history pengajuan?",
                5 => "Seberapa responsif sistem terhadap aksi yang Anda lakukan?",
                6 => "Apakah Anda merasa aman menggunakan aplikasi ini untuk pengajuan layanan?",
                7 => "Bagaimana kualitas informasi yang disajikan dalam aplikasi ini?",
                8 => "Apakah Anda akan merekomendasikan aplikasi ini kepada orang lain?",
                9 => "Bagaimana kemudahan aksesibilitas aplikasi ini (misalnya, di berbagai perangkat)?",
                10 => "Secara keseluruhan, seberapa puas Anda dengan pelayanan online melalui aplikasi ini?"
            ];

            $averages = [$q1_avg, $q2_avg, $q3_avg, $q4_avg, $q5_avg, $q6_avg, $q7_avg, $q8_avg, $q9_avg, $q10_avg];
        @endphp

        {{-- TABEL DETIL --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">No</th>
                    <th>Pertanyaan Kuesioner Kebergunaan Sistem</th>
                    <th style="width: 150px; text-align: center;">Rata-rata Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pertanyaanList as $no => $text)
                    <tr>
                        <td style="text-align: center;">{{ $no }}</td>
                        <td>{{ $text }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $averages[$no - 1] }} / 5</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- BLOK TTD & QR --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    {{-- TAMPILAN PEGAWAI --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container">
                        <div style="height: 80px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        Deny Haryuniansyah
                    </div>
                    <div style="margin-top:4px;">
                        NIP. 1973062022006041016
                    </div>

                @else
                    {{-- TAMPILAN DINAMIS ADMIN/KEPALA --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container">
                        @if(!empty($qrToken))
                            @php
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i'); 
                            @endphp
                            {!! QrCode::size(80)->generate(url('/verifikasi-laporan?judul=Laporan%20Evaluasi%20Sistem&tanggal_sah=' . urlencode($tanggalSah))) !!}
                        @else
                            <div style="height: 80px;"></div>
                        @endif
                    </div>

                    <div class="name" style="margin-top: 0;">
                        {{ $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah' }}
                    </div>
                    <div style="margin-top:4px;">
                        NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Render Horizontal Bar Chart --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('susChartHorizontal').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7', 'Q8', 'Q9', 'Q10'],
                    datasets: [{
                        data: {!! json_encode($averages) !!},
                        backgroundColor: 'rgba(251, 191, 36, 0.85)', // Warna emas kuning (amber)
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 1.5,
                        borderRadius: 3,
                        barPercentage: 0.6,
                        maxBarThickness: 18
                    }]
                },
                options: {
                    indexAxis: 'y', // Mengubah menjadi horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 5, // Nilai skala maksimal kuesioner adalah 5
                            ticks: { font: { family: 'Times New Roman', size: 12 } }
                        },
                        y: {
                            ticks: { font: { family: 'Times New Roman', size: 12, weight: 'bold' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>
