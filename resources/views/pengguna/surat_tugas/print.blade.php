<?php \Carbon\Carbon::setLocale('id'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Tugas - {{ $surat->nomor_surat }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
            size: A4;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }

        /* KOP SURAT */
        .kop {
            text-align: center;
            margin-bottom: 5px;
            position: relative;
        }
        .kop .left {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }
        .kop .center {
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        .kop .prov {
            font-size: 16px;
            font-weight: bold;
        }
        .kop .dinas {
            font-size: 22px;
            font-weight: 900;
            margin-top: 2px;
        }
        .kop .addr {
            font-size: 12px;
            margin-top: 5px;
        }
        hr.top-1 {
            border: none;
            border-top: 3px solid #000;
            margin: 8px 0 2px 0;
        }
        hr.top-2 {
            border: none;
            border-top: 1px solid #000;
            margin: 0 0 20px 0;
        }

        /* JUDUL */
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
        }
        .nomor {
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* ISI SURAT */
        .section {
            margin-bottom: 15px;
            display: flex;
        }
        .section-label {
            width: 120px;
            font-weight: bold;
        }
        .section-content {
            flex: 1;
            text-align: justify;
        }

        .memerintahkan {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin: 25px 0;
            letter-spacing: 2px;
        }

        /* TABEL BIODATA */
        table.biodata {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.biodata td {
            vertical-align: top;
            padding: 4px 0;
        }
        table.biodata td:first-child {
            width: 30px;
            text-align: center;
        }
        table.biodata td:nth-child(2) {
            width: 130px;
        }
        table.biodata td:nth-child(3) {
            width: 15px;
        }

        /* TABEL UNTUK */
        table.untuk {
            width: 100%;
            border-collapse: collapse;
        }
        table.untuk td {
            vertical-align: top;
            padding: 4px 0;
            text-align: justify;
        }
        table.untuk td:first-child {
            width: 30px;
            text-align: center;
        }

        /* TANDA TANGAN */
        .ttd {
            width: 100%;
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 300px;
            text-align: center;
        }
        .qr-container {
            margin: 15px 0;
            height: 90px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* TOMBOL CETAK */
        .no-print {
            margin-bottom: 15px;
            text-align: right;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 15px; background:#0d6efd; color:#fff; border:none; border-radius:4px; cursor:pointer;">Cetak Dokumen</button>
        </div>

        {{-- KOP SURAT --}}
        <table width="100%" style="margin-bottom: 5px;">
            <tr>
                <td width="15%" align="center" valign="middle">
                    <img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:80px; height:auto;">
                </td>
                <td width="85%" align="center" valign="middle">
                    <div class="prov" style="font-size: 18px; font-weight: bold; letter-spacing: 1px;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div class="dinas" style="font-size: 24px; font-weight: 900; margin-top: 2px; letter-spacing: 2px;">DINAS KESEHATAN</div>
                    <div class="addr" style="font-size: 12px; margin-top: 5px;">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116</div>
                </td>
            </tr>
        </table>
        <hr class="top-1">
        <hr class="top-2">

        {{-- JUDUL --}}
        <div class="title">SURAT PERINTAH TUGAS</div>
        <div class="nomor">Nomor: {{ $surat->nomor_surat }}</div>

        {{-- DASAR HUKUM --}}
        <div class="section">
            <div class="section-label">Menimbang :</div>
            <div class="section-content">
                Bahwa dalam rangka peningkatan upaya Pencegahan dan Pengendalian Penyakit Tidak Menular (P2PTM) di wilayah Provinsi Kalimantan Selatan, dipandang perlu untuk menugaskan staf/pegawai Dinas Kesehatan turun ke lapangan guna melaksanakan pembinaan dan evaluasi program terkait.
            </div>
        </div>
        <div class="section">
            <div class="section-label">Dasar :</div>
            <div class="section-content">
                <ol style="margin:0; padding-left:15px;">
                    <li>Dokumen Pelaksanaan Anggaran (DPA) Dinas Kesehatan Provinsi Kalimantan Selatan Tahun Anggaran {{ \Carbon\Carbon::now()->year }}.</li>
                    <li>Program Kerja Seksi Pencegahan dan Pengendalian Penyakit Tidak Menular.</li>
                </ol>
            </div>
        </div>

        <div class="memerintahkan">M E M E R I N T A H K A N :</div>

        {{-- KEPADA --}}
        <div class="section">
            <div class="section-label">Kepada :</div>
            <div class="section-content">
                <table class="biodata">
                    <tr>
                        <td>1.</td>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $surat->pegawai->nama_pegawai ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>NIP</td>
                        <td>:</td>
                        <td>{{ $surat->pegawai->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $surat->pegawai->jabatan ?? '-' }}</td>
                    </tr>

                    @php $no = 2; @endphp
                    @foreach($surat->pengikut as $pengikut)
                        <tr><td colspan="4" style="height:5px;"></td></tr>
                        <tr>
                            <td>{{ $no++ }}.</td>
                            <td>Nama</td>
                            <td>:</td>
                            <td><strong>{{ $pengikut->nama_pegawai ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>NIP</td>
                            <td>:</td>
                            <td>{{ $pengikut->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Jabatan</td>
                            <td>:</td>
                            <td>{{ $pengikut->jabatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        {{-- UNTUK --}}
        <div class="section">
            <div class="section-label">Untuk :</div>
            <div class="section-content">
                <table class="untuk">
                    <tr>
                        <td></td>
                        <td>
                            Melaksanakan tugas dengan rincian sebagai berikut:
                            <ul style="margin:5px 0 0 0; padding-left:15px;">
                                <li><strong>Maksud dan Tujuan:</strong> {{ $surat->maksud_tujuan }}</li>
                                <li><strong>Lokasi / Tujuan:</strong> 
                                    @if($surat->puskesmas_id)
                                        {{ $surat->puskesmas->nama_puskesmas }} ({{ $surat->puskesmas->nama_kabupaten }})
                                    @else
                                        {{ $surat->lokasi_tujuan }}
                                    @endif
                                </li>
                                <li><strong>Tanggal Pelaksanaan:</strong> 
                                    @if($surat->tanggal_mulai == $surat->tanggal_selesai)
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y') }}
                                    @endif
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div class="ttd-box">
                Dikeluarkan di : Banjarmasin<br>
                Pada Tanggal : {{ \Carbon\Carbon::parse($surat->tanggal_disetujui)->translatedFormat('d F Y') }}<br>
                <strong>Kepala Bidang P2PTM</strong><br>
                
                <div class="qr-container">
                    {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Surat%20Perintah%20Tugas&periode=' . urlencode($surat->nomor_surat) . '&tanggal_sah=' . urlencode(\Carbon\Carbon::parse($surat->tanggal_disetujui)->format('d-m-Y H:i')) . '&nama_kepala=' . urlencode($kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah') . '&nip=' . urlencode($kepalaAktif->nip ?? '1973062022006041016'))) !!}
                </div>
                
                <u><strong>{{ $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah' }}</strong></u><br>
                NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
            </div>
        </div>

    </div>
</body>
</html>
