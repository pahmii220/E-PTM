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
        }

        .kop .left {
            float: left;
            width: 80px;
        }

        .kop .center {
            display: inline-block;
            width: calc(100% - 160px);
        }

        .clear {
            clear: both;
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
            font-size: 13px;
            table-layout: fixed;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 8px 10px;
            /* Padding disesuaikan agar tidak terlalu rapat */
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #e2e2e2;
            /* Warna header disamakan dengan referensi gambar */
            font-weight: 700;
            text-align: left;
            /* Rata kiri sesuai gambar */
        }

        table.grid td {
            text-align: left;
            /* Rata kiri sesuai gambar */
        }

        /* Memberikan warna belang-belang pada baris tabel (opsional tapi bagus) */
        table.grid tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* ====== REKAP BOX ====== */
        .rekap-box {
            font-size: 12px;
            margin-bottom: 15px;
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

        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* ====== TTD & QR CODE ====== */
        .ttd {
            width: 100%;
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd .block {
            width: 40%;
            text-align: center;
            font-size: 12px;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 10px 0;
        }

        .ttd .block .name {
            margin-top: 5px;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>

<body onload="window.print();">
    <div class="container">

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button onclick="window.print()"
                style="padding:8px 12px; margin-right:6px; background:#198754; color:#fff; border:none; border-radius:4px; cursor:pointer;">Cetak
                Laporan</button>
            <a href="{{ route('pengguna.evaluasi.report') }}"
                style="padding:8px 12px; background:#eee; text-decoration:none; color:#000; border-radius:4px;">Kembali</a>
        </div>

        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="left"><img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:65px;"></div>
            <br><br>
            <div class="center">
                <div style="font-size:17px;font-weight:700;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div style="font-size:18px;font-weight:900;">DINAS KESEHATAN</div>
                <div style="font-size:12px;">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <hr class="top">

        {{-- JUDUL --}}
        <div style="text-align:center;margin-bottom:20px;">
            <h3 style="margin:0;font-size:15px;letter-spacing:0.6px;">LAPORAN HASIL EVALUASI SISTEM</h3>
        </div>

        {{-- RINGKASAN HASIL EVALUASI (TOTAL KESELURUHAN) --}}
        <div class="rekap-box">
            <table>
                <tr>
                    <td style="width: 25%;"><strong>Tanggal Cetak</strong></td>
                    <td style="width: 2%;">:</td>
                    <td>{{ now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y - H:i') }} WITA</td>
                </tr>
                <tr>
                    <td><strong>Total Pegawai (Keseluruhan)</strong></td>
                    <td>:</td>
                    <td>{{ $totalResponden }} Orang</td>
                </tr>
                <tr>
                    <td><strong>Rata-rata Skor SUS (Keseluruhan)</strong></td>
                    <td>:</td>
                    <td><strong>{{ $rataRataSkor }} / 100</strong></td>
                </tr>
                <tr>
                    <td><strong>Tingkat Penerimaan Sistem</strong></td>
                    <td>:</td>
                    <td><strong>{{ $predikat }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Kesimpulan</strong></td>
                    <td>:</td>
                    <td><em>{{ $keterangan }}</em></td>
                </tr>
            </table>
        </div>

        {{-- PENGELOMPOKKAN DATA PER BULAN (OTOMATIS DARI BLADE) --}}
        @php
            // Mengelompokkan data berdasarkan Bulan dan Tahun dari kolom created_at
            $rekapPerBulan = $semuaData->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('F Y');
            });
        @endphp

        {{-- TABEL REKAPITULASI BULANAN --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 10%;">Bulan</th>
                    <th style="width: 15%;">Jumlah Responden</th>
                    <th style="width: 15%;">Rata-rata nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapPerBulan as $bulan => $dataBulan)
                    <tr>
                        <td style="font-weight: 500;">{{ $bulan }}</td>
                        <td>{{ $dataBulan->count() }}</td>
                        {{-- Mengambil rata-rata skor SUS di bulan tersebut, dibulatkan 2 desimal --}}
                        <td>{{ round($dataBulan->avg('skor_sus'), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align:center; padding: 15px;">Belum ada data tanggapan hasil evaluasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- BLOK TTD & QR --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    {{-- 1. TAMPILAN KHUSUS PEGAWAI --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container">
                        <div style="height: 85px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        Deny Haryuniansyah
                    </div>
                    <div style="margin-top:4px;">
                        NIP. 1973062022006041016
                    </div>

                @else
                    {{-- 2. TAMPILAN DINAMIS ADMIN/KEPALA --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container">
                        @if(!empty($qrToken))
                            @php
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i'); 
                            @endphp
                            {!! QrCode::size(100)->generate(url('/verifikasi-laporan?judul=Laporan%20Evaluasi%20Sistem&tanggal_sah=' . urlencode($tanggalSah))) !!}
                        @else
                            <div style="height: 85px;"></div>
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
</body>

</html>