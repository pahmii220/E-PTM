<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Peserta Pemeriksaan</title>
    <style>
        @page {
            margin: 15mm 12mm;
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 6px 8px;
            box-sizing: border-box;
        }

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

        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            margin-top: 10px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 4px 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            text-align: center;
        }

        /* Layout TTD & QR */
        .ttd-wrapper {
            width: 100%;
            margin-top: 20px;
            overflow: hidden;
        }

        .ttd-right {
            float: right;
            width: 40%;
            text-align: center;
            font-size: 12px;
        }

        .name {
            margin-top: 60px;
            font-weight: 700;
            text-decoration: underline;
        }

        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; text-decoration:none; color:#000;">Kembali</a>
        </div>

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

        <div style="text-align:center;margin-bottom:10px;">
            <h3 style="margin:0;font-size:15px;letter-spacing:0.6px;">LAPORAN PESERTA</h3>
        </div>

        {{-- INFO BULAN --}}
        <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">
            Bulan:
            {{ \Carbon\Carbon::create()->month((int) request('bulan'))->locale('id')->translatedFormat('F') }}
            {{ request('tahun', now()->year) }}
        </div>

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>No. RM</th>
                    <th>Tanggal Lahir</th>
                    <th>Nama Peserta</th>
                    <th>Puskesmas</th>
                    <th>Kontak</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $row)
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td>{{ $row->no_rekam_medis ?? '-' }}</td>
                        <td style="text-align:center">
                            {{ $row->tanggal_lahir ? \Carbon\Carbon::parse($row->tanggal_lahir)->format('d-m-Y') : '-' }}
                        </td>
                        <td>{{ $row->nama_lengkap ?? '-' }}</td>
                        <td>{{ $row->puskesmas->nama_puskesmas ?? ($row->nama_puskesmas ?? '-') }}</td>
                        <td>{{ $row->kontak ?? '-' }}</td>
                        <td>{{ $row->alamat ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:6px; font-size:12px; font-weight:700;">
            Jumlah keseluruhan peserta sebanyak = {{ $items->count() }} orang
        </div>

        {{-- BLOK TTD & QR --}}
<div class="ttd-wrapper">
    <div class="ttd-right">
        <div>Banjarmasin, {{ now()->format('d-m-Y') }}</div>
        <div style="font-weight:bold;">KEPALA BIDANG P2PTM</div>

        {{-- QR Code hanya muncul jika ada token --}}
        @if(!empty($qrToken))
            <div style="margin: 5px 0;">
                {!! QrCode::size(85)->generate($qrToken) !!}
            </div>
        @else
            <div style="height: 20px; margin: 5px 0;"></div>
        @endif

    <!-- Tambahkan style margin-top negatif di sini -->
    <div style="margin-top: -50px;">
        <div class="name" style="text-decoration: underline; font-weight: bold;">
            {{ $kepalaAktif->nama_kepala ?? 'dr. H. DIAUDDIN, M.Kes' }}
        </div>
        <div>NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}</div>
    </div>
    </div>
</div>

    </div>
</body>

</html>