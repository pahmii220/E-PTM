<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Daftar Puskesmas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            margin: 15mm 12mm;
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            color: #111;
            -webkit-print-color-adjust: exact;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 8px 10px;
            box-sizing: border-box;
        }

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

        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        .no-print .btn {
            padding: 8px 12px;
            margin-left: 6px;
            text-decoration: none;
            border-radius: 6px;
            background: #eee;
            color: #000;
            font-weight: 600;
        }

        .no-print .btn-primary {
            background: #1f8a70;
            color: #fff;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            box-sizing: border-box;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 6px 8px;
            vertical-align: middle;
            word-wrap: break-word;
            box-sizing: border-box;
        }

        table.grid th {
            background: #f2f2f2;
            font-weight: 700;
            text-align: center;
            font-size: 13px;
        }

        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 12px;
            color: #555;
        }

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

        @media print {
            table.grid {
                font-size: 11.5px;
                -webkit-print-color-adjust: exact;
            }

            body {
                margin: 6mm;
            }
        }

        @media (max-width:720px) {

            table.grid th,
            table.grid td {
                font-size: 11px;
                padding: 6px;
            }

            .kop .center {
                width: calc(100% - 100px);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="no-print">
            <a href="javascript:window.print()" class="btn btn-primary">Cetak / Print</a>
            <a href="{{ url()->previous() }}" class="btn">Kembali</a>
        </div>

        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:65px; height:auto;">
            </div>

            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116</div>
            </div>

            <div class="clear"></div>
        </div>

        <hr class="top">

        <div style="text-align:center; margin-bottom:10px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px;">DAFTAR PUSKESMAS</h3>
            <p class="small" style="margin:4px 0 0 0;">Dicetak: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        </div>

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th style="width:12%">Kode</th>
                    <th>Nama Puskesmas</th>
                    <th style="width:18%">Kabupaten</th>
                    <th style="width:15%">Kecamatan</th>
                    <th style="width:18%">Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($puskesmas as $i => $p)
                    <tr>
                        <td class="text-center">
                            {{ (isset($puskesmas) && is_object($puskesmas) && method_exists($puskesmas, 'firstItem')) ? $puskesmas->firstItem() + $i : $i + 1 }}
                        </td>
                        <td class="text-center">{{ $p->kode_puskesmas ?? '-' }}</td>
                        <td>{{ $p->nama_puskesmas ?? '-' }}</td>
                        <td>{{ $p->nama_kabupaten ?? '-' }}</td>
                        <td>{{ $p->kecamatan ?? '-' }}</td>
                        <td>
                            {{ $p->telepon ?? '' }} @if(!empty($p->telepon) && !empty($p->email)) / @endif
                            {{ $p->email ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data puskesmas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd">
            <div class="block">
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:10px">KEPALA DINAS</div>
                <div class="name">{{ config('app.kepala_nama', 'dr. H. DIAUDDIN, M.Kes') }}</div>
                <div style="margin-top:4px;">NIP. {{ config('app.kepala_nip', '197709232006041015') }}</div>
            </div>
        </div>
    </div>
</body>

</html>