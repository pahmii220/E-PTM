<?php \Carbon\Carbon::setLocale('id'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Monitoring - {{ $laporan->puskesmas->nama_puskesmas ?? 'Puskesmas' }}</title>
    <style>
        @page {
            margin: 18mm 15mm;
            size: A4;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* KOP SURAT */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
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
        .pemkot {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dinas {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .bidang {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .alamat {
            font-size: 10px;
            font-style: italic;
            margin-top: 3px;
        }
        hr.line-double {
            border: 0;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 8px 0 15px 0;
        }

        .judul-dokumen {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .nomor-dokumen {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }

        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.meta-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        table.meta-table td.label {
            width: 28%;
            font-weight: bold;
        }
        table.meta-table td.colon {
            width: 2%;
        }

        .section-header {
            font-weight: bold;
            font-size: 13px;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        .box-content {
            border: 1px solid #ccc;
            background-color: #fafafa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: justify;
        }

        /* TANDA TANGAN BERSAMA */
        .ttd-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
            margin-bottom: 0;
        }

        @media print {
            @page {
                size: portrait;
                margin: 0mm;
            }
            body {
                margin: 15mm 12mm;
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="background: #333; color: #fff; padding: 10px; text-align: center;">
        <button onclick="window.print();" style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖵 Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <div class="container">
        {{-- KOP SURAT --}}
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo">
                </td>
                <td class="text-cell">
                    <div class="pemkot">Pemerintah provinsi Kalimantan Selatan</div>
                    <div class="dinas">Dinas Kesehatan</div>
                    <div class="alamat"> Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732<br>
                    (Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
                </td>
                <td class="spacer-cell"></td>
            </tr>
        </table>
        <hr class="line-double">

        {{-- JUDUL DOKUMEN --}}
        <div class="judul-dokumen">Laporan Hasil Monitoring Program P2PTM</div>
        <div class="nomor-dokumen">Kategori: {{ $laporan->kategori_temuan ?? 'Pemantauan Wilayah Puskesmas' }}</div>

        {{-- IDENTITAS LAPORAN --}}
        <table class="meta-table">
            <tr>
                <td class="label">Puskesmas Sasaran</td>
                <td class="colon">:</td>
                <td><strong>{{ Str::startsWith($laporan->puskesmas->nama_puskesmas ?? '', 'Puskesmas') ? $laporan->puskesmas->nama_puskesmas : 'Puskesmas ' . ($laporan->puskesmas->nama_puskesmas ?? '-') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Tanggal Kunjungan</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_kunjungan ?? $laporan->created_at)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Pengesahan (LHP)</td>
                <td class="colon">:</td>
                <td>{{ $laporan->nomor_spt ?? ('094/LHM-P2PTM/' . \Carbon\Carbon::parse($laporan->tanggal_disetujui ?? $laporan->created_at)->format('m/Y')) }}</td>
            </tr>
            @php
                $namaPelapor = $laporan->pegawai->nama_pegawai ?? ($laporan->pegawai->user->Nama_Lengkap ?? 'Akhmad Haris, SKM');
            @endphp
            <tr>
                <td class="label">Petugas Pelapor</td>
                <td class="colon">:</td>
                <td>{{ $namaPelapor }} (NIP. {{ $laporan->pegawai->nip ?? '-' }})</td>
            </tr>
            <tr>
                <td class="label">Status Pengesahan</td>
                <td class="colon">:</td>
                <td><strong style="color: green;">Disetujui Kepala P2PTM (ACC)</strong></td>
            </tr>
        </table>

        {{-- JUDUL TEMUAN --}}
        <div class="section-header">I. JUDUL TEMUAN MONITORING</div>
        <div class="box-content">
            <strong>{{ $laporan->judul_laporan }}</strong>
        </div>

        {{-- DESKRIPSI TEMUAN --}}
        <div class="section-header">II. TEMUAN MASALAH & ANALISIS LAPANGAN</div>
        <div class="box-content">
            {!! nl2br(e($laporan->deskripsi_temuan)) !!}
        </div>

        {{-- REKOMENDASI TINDAK LANJUT --}}
        <div class="section-header">III. REKOMENDASI & RENCANA TINDAK LANJUT (RTL)</div>
        <div class="box-content">
            {!! nl2br(e($laporan->rekomendasi_tindakan)) !!}
        </div>

        {{-- CATATAN KEPALA --}}
        @if($laporan->catatan_kepala)
        <div class="section-header">IV. CATATAN / PETUNJUK KEPALA P2PTM</div>
        <div class="box-content" style="background-color: #fffbeb; border-color: #fef08a;">
            <em>"{{ $laporan->catatan_kepala }}"</em>
        </div>
        @endif

        {{-- SEKSI TANDA TANGAN & QR CODE --}}
        <div class="ttd-section">
            <table class="ttd-table">
                <tr>
                    <td>
                        <div>Pegawai Dinkes,</div>
                        <div style="height: 65px;"></div>
                        <p class="ttd-nama">{{ $namaPelapor }}</p>
                        <div>NIP. {{ $laporan->pegawai->nip ?? '___________________' }}</div>
                    </td>
                    <td>
                        <div>Banjarmasin, {{ \Carbon\Carbon::parse($laporan->tanggal_disetujui ?? now())->translatedFormat('d F Y') }}</div>
                        <div>Disetujui & Disahkan Oleh,</div>
                        <div style="margin-top: 5px; margin-bottom: 5px;">
                            {!! QrCode::size(80)->generate(\App\Helpers\DocumentSigner::url([
                                'judul' => $laporan->judul_laporan ?? 'Laporan Hasil Monitoring',
                                'periode' => $laporan->puskesmas->nama_puskesmas ?? '-',
                                'tanggal_sah' => \Carbon\Carbon::parse($laporan->tanggal_disetujui ?? now())->format('d-m-Y H:i'),
                                'nama_kepala' => $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS',
                                'nip' => $kepalaAktif->nip ?? '197008081990031003',
                                'jabatan' => $kepalaAktif->jabatan ?? 'Kepala Bidang P2PTM'
                            ])) !!}
                        </div>
                        <p class="ttd-nama">{{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}</p>
                        <div>NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
