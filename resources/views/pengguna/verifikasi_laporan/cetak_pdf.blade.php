<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan PDF Statistik PTM</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            size: landscape;
            margin: 0mm; /* Menghilangkan header & footer bawaan browser (URL/Titel) */
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 10mm; /* Mengembalikan margin untuk konten */
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
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

        /* ====== TABLE ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            /* Font sangat kecil agar muat banyak kolom */
            table-layout: auto;
            margin-top: 15px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 3px 2px;
            vertical-align: middle;
            text-align: center;
        }

        table.grid th {
            font-weight: 700;
        }

        table.grid th.bg-blue {
            background-color: #d9edf7;
        }

        table.grid th.bg-green {
            background-color: #dcf8c6;
        }

        table.grid th.bg-yellow {
            background-color: #fcf8e3;
        }

        table.grid td.bg-gray {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table.grid td.bg-dark-gray {
            background-color: #e2e3e5;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .font-bold {
            font-weight: bold;
        }

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 15px;
            text-align: right;
            padding: 10px;
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

        /* ====== TTD ====== */
        .ttd {
            width: 100%;
            margin-top: 25px;
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
</head>

<body>

    @php
$bulanIndo = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
$namaBulan = $bulanIndo[\Carbon\Carbon::parse($startDate)->format('m')] . ' ' . \Carbon\Carbon::parse($startDate)->format('Y');

$totalSemuaPasien = 0;
$totalSemuaRemaja = 0;
$totalSemuaDewasa = 0;
$totalSemuaPraLansia = 0;
$totalSemuaLansia = 0;
$totalPenyakitGlobal = array_fill_keys($penyakitList, 0);
    @endphp

    <div class="container">

        {{-- TOMBOL AKSES --}}
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak PDF Sekarang</button>
            <button class="btn-close" onclick="window.close()">Tutup Tab</button>
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
        <p style="text-align: center; font-size:12px; margin:0 0 15px;">Periode: {{ $namaBulan }}</p>

        {{-- TABEL RINCIAN LENGKAP --}}
        <table class="grid">
            <thead>
                <tr>
                    <th rowspan="2" class="bg-blue" style="width: 2%;">No</th>
                    <th rowspan="2" class="bg-blue" style="width: 10%;">Wilayah Puskesmas</th>
                    <th colspan="{{ count($penyakitList) }}" class="bg-yellow">Berdasarkan Jenis Penyakit Terdeteksi</th>
                    <th rowspan="2" class="bg-blue" style="width: 3%;">Total Pasien</th>
                </tr>
                <tr>
                    @foreach($penyakitList as $penyakit)
                        <th class="bg-yellow" style="font-size: 7px; max-width: 35px; word-wrap: break-word;">
                            {{ $penyakit }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if($matriksLaporan->count() == 0)
                    <tr>
                        <td colspan="{{ count($penyakitList) + 3 }}" style="padding: 20px;">Tidak ada data pada periode ini</td>
                    </tr>
                @else
                    @foreach($matriksLaporan as $no => $pkm)
                        @php
                            $totalSemuaPasien += $pkm['total_pasien'];
                            foreach ($penyakitList as $p) {
                                $totalPenyakitGlobal[$p] += $pkm['ptm'][$p];
                            }
                        @endphp
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td class="text-left font-bold">{{ $pkm['puskesmas'] }}</td>
                            @foreach($penyakitList as $penyakit)
                                <td style="color: {{ $pkm['ptm'][$penyakit] > 0 ? '#000' : '#ccc' }}">{{ $pkm['ptm'][$penyakit] }}</td>
                            @endforeach
                            <td class="bg-gray">{{ $pkm['total_pasien'] }}</td>
                        </tr>
                    @endforeach

                    <!-- BARIS TOTAL KESELURUHAN -->
                    <tr>
                        <td colspan="2" class="text-right font-bold" style="padding-right: 5px;">TOTAL KESELURUHAN :</td>
                        @foreach($penyakitList as $penyakit)
                            <td class="font-bold">{{ $totalPenyakitGlobal[$penyakit] }}</td>
                        @endforeach
                        <td class="bg-dark-gray">{{ $totalSemuaPasien }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div class="block">
                <div>Banjarmasin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div>Kepala Bidang P2TPM</div>
                <div class="name">Deny Haryuniansyah</div>
                <div>NIP. 1973062022006041016</div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Auto open print dialog setelah load selesai
            setTimeout(() => { window.print(); }, 500);
        });
    </script>
</body>

</html>