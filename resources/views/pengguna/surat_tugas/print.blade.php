<?php \Carbon\Carbon::setLocale('id'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Tugas - {{ $surat->nomor_surat }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13px;
            line-height: 1.35;
            color: #000;
            margin: 10mm 15mm;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* ====== KOP SURAT ====== */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop-table td.logo-cell {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-table td.logo-cell img {
            width: 75px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .kop-table td.text-cell {
            text-align: center;
            vertical-align: middle;
        }

        .kop-table td.spacer-cell {
            width: 80px;
        }

        .pemprov {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }

        .dinas {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            margin-top: 1px;
            letter-spacing: 1px;
            color: #000;
        }

        .alamat {
            font-size: 11px;
            margin-top: 3px;
            line-height: 1.3;
            color: #111;
        }

        hr.line-double {
            border: 0;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 6px 0 14px 0;
        }

        /* ====== JUDUL SURAT ====== */
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .nomor {
            text-align: center;
            font-size: 13px;
            margin-bottom: 14px;
        }

        /* ====== ISI SURAT ====== */
        .section {
            margin-bottom: 8px;
            display: flex;
        }

        .section-label {
            width: 110px;
            font-weight: bold;
        }

        .section-content {
            flex: 1;
            text-align: justify;
        }

        .memerintahkan {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 12px 0 8px 0;
            letter-spacing: 2px;
        }

        /* ====== TABEL BIODATA ====== */
        table.biodata {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        table.biodata td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 13px;
        }

        table.biodata td:first-child {
            width: 25px;
            text-align: center;
        }

        table.biodata td:nth-child(2) {
            width: 110px;
        }

        table.biodata td:nth-child(3) {
            width: 15px;
            text-align: center;
        }

        /* ====== TABEL UNTUK ====== */
        table.untuk {
            width: 100%;
            border-collapse: collapse;
        }

        table.untuk td {
            vertical-align: top;
            padding: 2px 0;
            text-align: justify;
            font-size: 13px;
        }

        /* ====== TANDA TANGAN ====== */
        .ttd-wrapper {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .ttd-table {
            margin-left: auto;
            width: 280px;
            text-align: center;
            border-collapse: collapse;
            font-size: 13px;
        }

        .qr-container {
            margin: 6px 0;
            height: 85px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .no-print {
            text-align: right;
            margin-bottom: 10px;
        }

        @media print {
            @page {
                size: portrait;
                margin: 0mm;
            }
            body {
                margin: 10mm 15mm;
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 16px; background:#0d6efd; color:#fff; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">🖨️ Cetak Dokumen</button>
        </div>

        {{-- KOP SURAT --}}
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo Dinkes">
                </td>
                <td class="text-cell">
                    <div class="pemprov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div class="dinas">DINAS KESEHATAN</div>
                    <div class="alamat">
                        Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732<br>
                        (Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)
                    </div>
                </td>
                <td class="spacer-cell"></td>
            </tr>
        </table>
        <hr class="line-double">

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

        <div class="memerintahkan">M E M E R I N T A H K A N :</div> <br>

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
                        <tr><td colspan="4" style="height:3px;"></td></tr>
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
                        <td>
                            Melaksanakan tugas dengan rincian sebagai berikut:
                            <ul style="margin:3px 0 0 0; padding-left:15px;">
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
        <div class="ttd-wrapper">
            <table class="ttd-table">
                <tr>
                    <td>
                        Dikeluarkan di : Banjarmasin<br>
                        Pada Tanggal : {{ \Carbon\Carbon::parse($surat->tanggal_disetujui)->translatedFormat('d F Y') }}<br>
                        <strong>Kepala Bidang P2PTM</strong><br>
                        
                        <div class="qr-container">
                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Surat%20Perintah%20Tugas&periode=' . urlencode($surat->nomor_surat) . '&tanggal_sah=' . urlencode(\Carbon\Carbon::parse($surat->tanggal_disetujui)->format('d-m-Y H:i')) . '&nama_kepala=' . urlencode($kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah') . '&nip=' . urlencode($kepalaAktif->nip ?? '1973062022006041016'))) !!}
                        </div>
                        
                        <u><strong>{{ $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah' }}</strong></u><br>
                        NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>
</html>
