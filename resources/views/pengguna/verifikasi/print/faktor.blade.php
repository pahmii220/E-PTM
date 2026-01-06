<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Faktor Risiko PTM</title>
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
            margin-bottom: 10px;
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

        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
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
            text-align: center;
            font-weight: 700;
        }

        .no-print {
            text-align: right;
            margin-bottom: 10px;
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
                style="padding:8px 12px; background:#eee; color:#000; text-decoration:none;">Kembali</a>
        </div>

        <div class="kop">
            <div class="left"><img src="{{ asset('images/dinkes.png') }}" style="width:65px;"></div>
            <div class="center">
                <br>
                <div style="font-size:17px; font-weight:700;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div style="font-size:18px; font-weight:900;">DINAS KESEHATAN</div>
                <div style="font-size:12px;">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        <h3 style="text-align:center; margin:0 0 10px 0;">LAPORAN FAKTOR RISIKO PTM</h3>

        {{-- Guard: pastikan $items adalah collection --}}
        @php
$items = $items ?? collect();
        @endphp

        {{-- Debug block (akan tampil jika kosong atau jika ada variabel debug) --}}
        @if(($items->count() === 0) && (isset($debug) || true))
            <div style="margin-bottom:8px; color:#333; font-size:12px;">
                <strong>DEBUG (lihat ini sementara):</strong>
                <div>Jumlah item yang diterima view: {{ $items->count() }}</div>
                <div>Jika JSON debug sebelumnya menunjukkan data, berarti controller benar — sekarang periksa variabel yang
                    dikirim ke view.</div>
            </div>
        @endif

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Peserta</th>
                    <th style="width:110px">Tanggal</th>
                    <th style="width:80px">Merokok</th>
                    <th style="width:80px">Alkohol</th>
                    <th>Puskesmas</th>
                </tr>
            </thead>

            <tbody>
                @if($items->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data faktor risiko.
                        </td>
                    </tr>
                @else
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pasien->nama_lengkap ?? '-' }}</td>
                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>{{ $item->merokok ?? '-' }}</td>
                            <td>{{ $item->alkohol ?? '-' }}</td>
                            <td>{{ $item->pasien->puskesmas->nama_puskesmas ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>

        </table>

        <div style="width:100%; margin-top:40px; display:flex; justify-content:flex-end;">
            <div style="width:320px; text-align:center;">
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>
                <div style="margin-top:30px">KEPALA DINAS</div>
                <div style="margin-top:60px; font-weight:700; text-decoration:underline">
                    {{ config('app.kepala_nama', 'dr. H. DIAUDDIN, M.Kes') }}</div>
                <div>NIP. {{ config('app.kepala_nip', '197709232006041015') }}</div>
            </div>
        </div>

    </div>
</body>

</html>