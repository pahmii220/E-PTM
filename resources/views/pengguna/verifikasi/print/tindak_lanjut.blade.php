<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Tindak Lanjut PTM</title>

    <style>
        @page { margin: 15mm 12mm; }

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

        /* KOP */
        .kop { text-align: center; margin-bottom: 6px; position: relative; }
        .kop .left { float: left; width: 80px; }
        .kop .center { display: inline-block; width: calc(100% - 160px); text-align: center; }
        .clear { clear: both; }

        .kop .prov { font-size: 14px; font-weight: 700; }
        .kop .dinas { font-size: 18px; font-weight: 900; margin-top: 2px; }
        .kop .addr { font-size: 12px; margin-top: 6px; }

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
        }

        table.grid th, table.grid td {
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

        .no-print { margin-bottom: 10px; text-align: right; }

        @media print {
            .no-print { display: none; }
            table.grid { font-size: 11.5px; }
        }

        /* Footer TTD */
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

        .ttd .block .name {
            margin-top: 70px;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="no-print">
        <button onclick="window.print()" style="padding:8px 12px;">Print</button>
        <a href="javascript:history.back()" style="padding:8px 12px; background:#eee; text-decoration:none;">Kembali</a>
    </div>

    <!-- KOP -->
    <div class="kop">
        <div class="left">
            <img src="{{ asset('images/dinkes.png') }}" style="width:65px;">
        </div>
        <br>
        <div class="center">
            <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
            <div class="dinas">DINAS KESEHATAN</div>
            <div class="addr">Jalan Belitung Darat No.118 — Banjarmasin 70116</div>
        </div>
        <div class="clear"></div>
    </div>

    <hr class="top">

    <div style="text-align:center; margin-bottom:10px;">
        <h3 style="margin:0; font-size:15px;">LAPORAN TINDAK LANJUT PENYAKIT TIDAK MENULAR</h3>
    </div>

    <!-- TABEL -->
    <table class="grid">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Peserta</th>
                <th style="width:120px">Tanggal</th>
                <th style="width:180px">Jenis Tindak Lanjut</th>
                <th>Keterangan</th>
                <th style="width:120px">Puskesmas</th>
            </tr>
        </thead>
                <tbody>
                    @forelse($items as $i => $row)
                                    <tr>
                                        <td style="text-align:center">{{ $i + 1 }}</td>
                                        <td>{{ optional($row->pasien)->nama_lengkap ?? '-' }}</td>
                                        <td style="text-align:center">
                                            {{ $row->tanggal_tindak_lanjut
                        ? \Carbon\Carbon::parse($row->tanggal_tindak_lanjut)->format('d-m-Y')
                        : '-' }}
                                        </td>
                                        <td>{{ $row->jenis_tindak_lanjut ?? '-' }}</td>
                                        <td>{{ $row->catatan_petugas ?? '-' }}</td>
                                        <td>{{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}</td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">
                                Tidak ada data tindak lanjut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
                
                <!-- TTD -->
                <div class="ttd">
                    <div class="block">
                        <div>Banjarmasin, {{ now()->format('d-m-Y') }}</div>
                        <div style="margin-top:10px">KEPALA DINAS</div>
                        <div class="name">dr. H. DIAUDDIN, M.Kes</div>
                        <div>NIP. 197709232006041015</div>
                    </div>
                </div>
                
                </div>
                </body>
                
                </html>