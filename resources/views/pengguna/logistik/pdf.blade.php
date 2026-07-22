<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima Logistik</title>
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
            margin-top: 10px;
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
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* ====== TTD ====== */
        .ttd {
            width: 100%;
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .ttd .block {
            width: 40%;
            text-align: center;
            font-size: 13px;
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

<body>
    <div class="container">

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; text-decoration:none; color:#000;">Kembali</a>
        </div>

        @php
            // Teknik Base64 agar DomPDF 100% bisa memuat gambar lokal di Laragon/Windows
            $logoPath = public_path('images/dinkes.png');
            $logoBase64 = '';
            if(file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
        @endphp

        {{-- KOP SURAT --}}
        <table style="width: 100%; border: none; margin-bottom: 6px; padding: 0; border-collapse: collapse;">
            <tr>
                <td style="width: 90px; text-align: center; border: none; vertical-align: middle; padding: 0;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="logo" style="width:70px;">
                    @endif
                </td>
                <td style="text-align: center; border: none; vertical-align: middle; padding: 0; padding-right: 90px;">
                    <div style="font-size:17px;font-weight:700; line-height:1.2;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div style="font-size:19px;font-weight:900; line-height:1.2; letter-spacing: 1px;">DINAS KESEHATAN</div>
                    <div style="font-size:12px; margin-top: 4px;">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116</div>
                </td>
            </tr>
        </table>
        <hr class="top">

        {{-- JUDUL --}}
        <div style="text-align:center;margin-top:20px;margin-bottom:20px;">
            <h3 style="margin:0;font-size:15px;letter-spacing:0.6px;text-decoration:underline;">SERAH TERIMA LOGISTIK ALAT KESEHATAN PTM</h3>
        </div>

        {{-- KETERANGAN BAST --}}
        <div style="font-size:13px; margin-bottom:15px; line-height: 1.6; text-align: justify;">
            Pada hari ini, tanggal <strong>{{ $tanggal }}</strong>, telah dilakukan serah terima logistik dan bahan habis pakai untuk pelayanan Penyakit Tidak Menular (PTM) dari <strong>Dinas Kesehatan</strong> kepada <strong>{{ $logistik->puskesmas->nama_puskesmas }}</strong>. Adapun rincian barang yang diserahkan adalah sebagai berikut:
        </div>

        {{-- TABEL DATA --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th style="text-align: left;">Nama Barang / Alat Kesehatan</th>
                    <th style="width:150px">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center">1</td>
                    <td>Strip Test Gula Darah</td>
                    <td style="text-align:center">{{ $logistik->strip_gula }}</td>
                </tr>
                <tr>
                    <td style="text-align:center">2</td>
                    <td>Strip Test Kolesterol</td>
                    <td style="text-align:center">{{ $logistik->strip_kolesterol }}</td>
                </tr>
                <tr>
                    <td style="text-align:center">3</td>
                    <td>Strip Test Asam Urat</td>
                    <td style="text-align:center">{{ $logistik->strip_asam_urat }}</td>
                </tr>
                <tr>
                    <td style="text-align:center">4</td>
                    <td>Blood Lancet</td>
                    <td style="text-align:center">{{ $logistik->lancet }}</td>
                </tr>
                <tr>
                    <td style="text-align:center">5</td>
                    <td>Kapas Alkohol (Alcohol Swab)</td>
                    <td style="text-align:center">{{ $logistik->kapas_alkohol }}</td>
                </tr>
            </tbody>
        </table>

        @if($logistik->keterangan)
        <div style="font-size:13px; margin-top:15px;">
            <strong>Keterangan Tambahan:</strong><br>
            {{ $logistik->keterangan }}
        </div>
        @endif

        <div style="font-size:13px; margin-top:15px; line-height: 1.6; text-align: justify;">
            Barang tersebut telah diterima dalam keadaan baik dan cukup, untuk dipergunakan sebagaimana mestinya dalam mendukung pelayanan skrining Penyakit Tidak Menular di wilayah kerja Puskesmas terkait. Berita Acara ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        {{-- BLOK TTD KANAN (1 PIHAK) --}}
        <table style="width: 100%; margin-top: 40px; border: none; padding: 0;">
            <tr>
                <td style="width: 60%; border: none;"></td>
                <td style="width: 40%; border: none; text-align: center; font-size: 13px; vertical-align: top;">
                    <div>DIKELUARKAN DI BANJARMASIN</div>
                    <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div style="height: 85px;"></div>

                    <div style="margin-top: 0; font-weight: 700; text-decoration: underline;">
                        {{ $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah, SKM' }}
                    </div>
                    <div style="margin-top:4px;">
                        NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
