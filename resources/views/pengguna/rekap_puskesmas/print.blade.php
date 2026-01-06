<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Rekap PTM Per Puskesmas</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            margin: 15mm 12mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
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
            text-align: center;
        }

        .clear {
            clear: both;
        }

        hr.top {
            border: none;
            border-top: 2px solid #000;
            margin: 8px 0 12px 0;
        }

        /* ====== JUDUL ====== */
        .title {
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        /* ====== TABEL ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 12px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            text-align: center;
        }

        table.grid td {
            text-align: center;
        }

        table.grid td.left {
            text-align: left;
        }

        /* ====== TTD ====== */
        .ttd {
            width: 100%;
            margin-top: 30px;
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

        /* ====== PRINT ====== */
        @media print {
            table.grid {
                font-size: 11.5px;
            }

            table.grid tr>td:last-child,
            table.grid tr>th:last-child {
                border-right: 1px solid #000;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">

        {{-- KOP --}}
        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width:65px;">
            </div>

            <div class="center">
                <div style="font-size:16px; font-weight:700;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div style="font-size:18px; font-weight:900;">DINAS KESEHATAN</div>
                <div style="font-size:12px; margin-top:4px;">
                    Jalan Belitung Darat No.118 — Telp. (0511) 3355661 — Banjarmasin
                </div>
            </div>

            <div class="clear"></div>
        </div>

        <hr class="top">

        {{-- JUDUL --}}
        <div class="title">
            LAPORAN REKAP PENYAKIT TIDAK MENULAR (PTM)<br>
            PER PUSKESMAS
        </div>

        {{-- TABEL --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Nama Puskesmas</th>
                    <th style="width:110px;">Total Pasien</th>
                    <th style="width:110px;">Deteksi Dini</th>
                    <th style="width:110px;">Faktor Risiko</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekapPuskesmas as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="left">{{ $item->nama_puskesmas }}</td>
                        <td>{{ $item->total_pasien }}</td>
                        <td>{{ $item->total_deteksi }}</td>
                        <td>{{ $item->total_faktor }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TTD --}}
        <div class="ttd">
            <div class="block">
                <div>Dikeluarkan di Banjarmasin</div>
                <div>Tanggal: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:12px;">KEPALA DINAS KESEHATAN</div>

                <div class="name">
                    {{ config('app.kepala_nama', 'dr. H. DIAUDDIN, M.Kes') }}
                </div>
                <div>
                    NIP. {{ config('app.kepala_nip', '197709232006041015') }}
                </div>
            </div>
        </div>

    </div>
</body>

</html>