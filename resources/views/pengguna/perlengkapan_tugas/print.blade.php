<?php \Carbon\Carbon::setLocale('id'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Logistik Kegiatan - {{ $perlengkapan->laporanMonitoring->judul_laporan ?? ($perlengkapan->suratTugas->nomor_surat ?? 'PTM') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 13px;
            color: #000;
            margin: 15mm 15mm;
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
            margin-bottom: 5px;
        }

        .kop-table td.logo-cell {
            width: 85px;
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
            width: 85px;
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
            margin-top: 2px;
            letter-spacing: 1px;
            color: #000;
        }

        .alamat {
            font-size: 11px;
            margin-top: 4px;
            line-height: 1.35;
            color: #111;
        }

        hr.top {
            border: none;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin: 8px 0 16px 0;
        }

        /* ====== JUDUL DOKUMEN ====== */
        .title-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-text {
            font-weight: bold;
            font-size: 15px;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subtitle-text {
            font-size: 13px;
            margin-top: 5px;
            font-weight: bold;
            color: #222;
        }

        /* ====== METADATA ====== */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 190px;
            color: #111;
        }

        .meta-sep {
            width: 15px;
            text-align: center;
        }

        .meta-val {
            color: #000;
        }

        /* ====== TABEL ITEM LOGISTIK ====== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            font-size: 13px;
        }

        .items-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        .items-table td.center {
            text-align: center;
        }

        /* ====== CATATAN ====== */
        .catatan-box {
            margin-bottom: 25px;
            font-size: 13px;
            line-height: 1.4;
        }

        /* ====== TANDA TANGAN ====== */
        .ttd-wrapper {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .ttd-table {
            margin-left: auto;
            width: 290px;
            text-align: center;
            border-collapse: collapse;
            font-size: 13px;
        }

        .disclaimer-text {
            font-size: 8px;
            margin-top: 4px;
            color: #333;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90px;
            margin: 8px 0;
        }

        .no-print {
            text-align: right;
            margin-bottom: 15px;
        }

        @media print {
            @page {
                size: portrait;
                margin: 0mm;
            }
            body {
                margin: 12mm 15mm;
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
            <button onclick="window.print()" style="padding:8px 16px; background:#198754; color:#fff; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">🖨️ Cetak Dokumen</button>
        </div>

        <!-- KOP SURAT -->
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

        <hr class="top">

        <!-- JUDUL LAPORAN -->
        <div class="title-container">
            <div class="title-text">LAPORAN PERLENGKAPAN LOGISTIK KEGIATAN PTM</div>
            <div class="subtitle-text">
                Periode: {{ \Carbon\Carbon::parse($perlengkapan->created_at)->setTimezone('Asia/Makassar')->translatedFormat('F Y') }}
            </div>
        </div>

        <!-- METADATA LAPORAN -->
        <table class="meta-table">
            @if($perlengkapan->laporanMonitoring)
                <tr>
                    <td class="meta-label">Judul Laporan Monitoring</td>
                    <td class="meta-sep">:</td>
                    <td class="meta-val"><strong>{{ $perlengkapan->laporanMonitoring->judul_laporan }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Puskesmas Tujuan</td>
                    <td class="meta-sep">:</td>
                    <td class="meta-val">{{ Str::startsWith($perlengkapan->laporanMonitoring->puskesmas->nama_puskesmas ?? '', 'Puskesmas') ? $perlengkapan->laporanMonitoring->puskesmas->nama_puskesmas : 'Puskesmas ' . ($perlengkapan->laporanMonitoring->puskesmas->nama_puskesmas ?? '-') }} (Kec. {{ $perlengkapan->laporanMonitoring->puskesmas->kecamatan ?? '-' }})</td>
                </tr>
                <tr>
                    <td class="meta-label">Rekomendasi Logistik</td>
                    <td class="meta-sep">:</td>
                    <td class="meta-val">{{ $perlengkapan->laporanMonitoring->rekomendasi_tindakan }}</td>
                </tr>
            @else
                <tr>
                    <td class="meta-label">Nomor STL</td>
                    <td class="meta-sep">:</td>
                    <td class="meta-val"><strong>{{ $perlengkapan->suratTugas->nomor_surat ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Agenda Kunjungan</td>
                    <td class="meta-sep">:</td>
                    <td class="meta-val">{{ $perlengkapan->suratTugas->maksud_tujuan ?? '-' }}</td>
                </tr>
            @endif
        </table>

        <!-- TABEL BARANG LOGISTIK -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama Alat / Barang Logistik</th>
                    <th style="width: 160px;">Jumlah / Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perlengkapan->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="center"><strong>{{ $item->jumlah }} {{ $item->satuan ?? 'Pcs' }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="center">Tidak ada data barang logistik</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($perlengkapan->catatan)
            <div class="catatan-box">
                <strong>Catatan Tambahan:</strong><br>
                {{ $perlengkapan->catatan }}
            </div>
        @endif

        <!-- BLOK TANDA TANGAN -->
        <div class="ttd-wrapper">
            <table class="ttd-table">
                <tr>
                    <td>
                        Banjarmasin, {{ \Carbon\Carbon::parse($perlengkapan->updated_at)->translatedFormat('d F Y') }}<br>
                        Mengetahui/Menyetujui,<br>
                        <strong>{{ isset($kepalaAktif) ? 'KEPALA BIDANG P2P' : 'KEPALA DINAS' }}</strong>
                        
                        <div class="disclaimer-text">
                            Dokumen ini telah disahkan secara elektronik
                        </div>

                        <div class="qr-container">
                            @php
                                $periode = \Carbon\Carbon::parse($perlengkapan->created_at)->translatedFormat('F Y');
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'dr. H. DIAUDDIN, M.Kes';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                            @endphp

                            {!! QrCode::size(85)->generate(route('verifikasi.laporan', [
                                'judul' => 'Laporan Perlengkapan Tugas Luar PTM',
                                'periode' => $periode,
                                'tanggal_sah' => $tanggalSah,
                                'nama_kepala' => $namaPejabat,
                                'nip' => $nipPejabat
                            ])) !!}
                        </div>

                        <u><strong>{{ $kepalaAktif->nama_kepala ?? 'dr. H. DIAUDDIN, M.Kes' }}</strong></u><br>
                        NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
