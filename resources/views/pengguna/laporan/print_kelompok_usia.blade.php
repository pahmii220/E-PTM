<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan PTM Berdasarkan Kelompok Usia</title>

    <style>
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

        /* KOP */
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

        /* TABLE */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
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

        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* TTD */
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
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px;">Print</button>

            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; color:#000; text-decoration:none;">
                Kembali
            </a>
        </div>


        {{-- KOP SURAT --}}
        <div class="kop">

            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width:65px;">
            </div>

            <br>

            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">
                    Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116
                </div>
            </div>

            <div class="clear"></div>

        </div>

        <hr class="top">


        <div style="width:100%; text-align:center; margin-bottom:12px;">
            <h3 style="
                margin:0;
                font-size:15px;
                letter-spacing:0.6px;
                font-weight:700;
            ">
                LAPORAN PENYAKIT TIDAK MENULAR (PTM) <br>
                BERDASARKAN KELOMPOK USIA
            </h3>
        </div>


        <table class="grid">

            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Kelompok Usia</th>
                    <th>Rentang Usia</th>
                    <th>Jumlah Peserta</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td style="text-align:center">1</td>
                    <td>Remaja</td>
                    <td style="text-align:center">&lt; 18 Tahun</td>
                    <td style="text-align:center">{{ $data['remaja'] }}</td>
                </tr>

                <tr>
                    <td style="text-align:center">2</td>
                    <td>Dewasa</td>
                    <td style="text-align:center">18 – 44 Tahun</td>
                    <td style="text-align:center">{{ $data['dewasa'] }}</td>
                </tr>

                <tr>
                    <td style="text-align:center">3</td>
                    <td>Pra Lansia</td>
                    <td style="text-align:center">45 – 59 Tahun</td>
                    <td style="text-align:center">{{ $data['pra_lansia'] }}</td>
                </tr>

                <tr>
                    <td style="text-align:center">4</td>
                    <td>Lansia</td>
                    <td style="text-align:center">≥ 60 Tahun</td>
                    <td style="text-align:center">{{ $data['lansia'] }}</td>
                </tr>

            </tbody>

        </table>


        <div style="margin-top:8px;font-size:12px;font-weight:700;">
            Jumlah keseluruhan peserta sebanyak =
            {{ $data['remaja'] + $data['dewasa'] + $data['pra_lansia'] + $data['lansia'] }} orang
        </div>


        {{-- TTD --}}
        <div class="ttd">

            <div class="block">

                <br>

                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:10px">KEPALA DINAS</div>

                <div class="name">
                    {{ config('app.kepala_nama', 'dr. H. DIAUDDIN, M.Kes') }}
                </div>

                <div style="margin-top:4px;">
                    NIP. {{ config('app.kepala_nip', '197709232006041015') }}
                </div>

            </div>

        </div>

    </div>

</body>

</html>