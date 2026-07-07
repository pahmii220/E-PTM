<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan PTM Berdasarkan Kelompok Usia</title>

    <style>
        /* ====== SETTING CETAK ====== */
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

        /* ====== KOP ====== */
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

        /* ====== TABLE ====== */
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

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* ====== TTD & QR CODE ====== */
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

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 10px 0;
        }

        .ttd .block .name {
            margin-top: 5px;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">

        {{-- TOMBOL PRINT (Sembunyi saat dicetak) --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px; cursor:pointer;">Print</button>
            <button onclick="window.close()"
                style="padding:8px 12px; background:#eee; color:#000; border:1px solid #ccc; cursor:pointer;">Tutup</button>
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

        {{-- JUDUL --}}
        <div style="width:100%; text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; font-weight:700;">
                LAPORAN PENYAKIT TIDAK MENULAR (PTM) <br>
                BERDASARKAN KELOMPOK USIA
            </h3>
        </div>

        {{-- PENJELASAN SINGKAT --}}
        <div style="font-size: 11px; color: #444; margin-bottom: 12px; line-height: 1.4; text-align: left;">
            Laporan ini menyajikan ringkasan data peserta pemeriksaan Penyakit Tidak Menular (PTM) yang dikelompokkan berdasarkan kategori usia (Remaja, Dewasa, Pra Lansia, dan Lansia) pada Dinas Kesehatan Provinsi Kalimantan Selatan.
        </div>

        @php
            $totalRemaja = $dataUsia['remaja'] ?? 0;
            $totalDewasa = $dataUsia['dewasa'] ?? 0;
            $totalPraLansia = $dataUsia['pra_lansia'] ?? 0;
            $totalLansia = $dataUsia['lansia'] ?? 0;
            $grandTotal = $totalRemaja + $totalDewasa + $totalPraLansia + $totalLansia;
        @endphp

        {{-- TABEL --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Kelompok Usia</th>
                    <th>Rentang Usia</th>
                    <th style="width:120px">Jumlah Peserta</th>
                    <th style="width:90px">Persentase</th>
                    <th style="width:150px">Proporsi (Visual)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $persenRemaja = $grandTotal > 0 ? round(($totalRemaja / $grandTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="text-align:center">1</td>
                    <td>Remaja</td>
                    <td style="text-align:center">&lt; 18 Tahun</td>
                    <td style="text-align:center">{{ $totalRemaja }} Orang</td>
                    <td style="text-align:center">{{ $persenRemaja }}%</td>
                    <td style="text-align:center">
                        <div style="background-color: #e5e7eb; border-radius: 3px; width: 100%; height: 10px; display: inline-block; overflow: hidden; vertical-align: middle;">
                            <div style="background-color: #4b5563; height: 100%; width: {{ $persenRemaja }}%;"></div>
                        </div>
                    </td>
                </tr>
                @php
                    $persenDewasa = $grandTotal > 0 ? round(($totalDewasa / $grandTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="text-align:center">2</td>
                    <td>Dewasa</td>
                    <td style="text-align:center">18 – 44 Tahun</td>
                    <td style="text-align:center">{{ $totalDewasa }} Orang</td>
                    <td style="text-align:center">{{ $persenDewasa }}%</td>
                    <td style="text-align:center">
                        <div style="background-color: #e5e7eb; border-radius: 3px; width: 100%; height: 10px; display: inline-block; overflow: hidden; vertical-align: middle;">
                            <div style="background-color: #4b5563; height: 100%; width: {{ $persenDewasa }}%;"></div>
                        </div>
                    </td>
                </tr>
                @php
                    $persenPraLansia = $grandTotal > 0 ? round(($totalPraLansia / $grandTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="text-align:center">3</td>
                    <td>Pra Lansia</td>
                    <td style="text-align:center">45 – 59 Tahun</td>
                    <td style="text-align:center">{{ $totalPraLansia }} Orang</td>
                    <td style="text-align:center">{{ $persenPraLansia }}%</td>
                    <td style="text-align:center">
                        <div style="background-color: #e5e7eb; border-radius: 3px; width: 100%; height: 10px; display: inline-block; overflow: hidden; vertical-align: middle;">
                            <div style="background-color: #4b5563; height: 100%; width: {{ $persenPraLansia }}%;"></div>
                        </div>
                    </td>
                </tr>
                @php
                    $persenLansia = $grandTotal > 0 ? round(($totalLansia / $grandTotal) * 100, 1) : 0;
                @endphp
                <tr>
                    <td style="text-align:center">4</td>
                    <td>Lansia</td>
                    <td style="text-align:center">≥ 60 Tahun</td>
                    <td style="text-align:center">{{ $totalLansia }} Orang</td>
                    <td style="text-align:center">{{ $persenLansia }}%</td>
                    <td style="text-align:center">
                        <div style="background-color: #e5e7eb; border-radius: 3px; width: 100%; height: 10px; display: inline-block; overflow: hidden; vertical-align: middle;">
                            <div style="background-color: #4b5563; height: 100%; width: {{ $persenLansia }}%;"></div>
                        </div>
                    </td>
                </tr>
                {{-- BARIS TOTAL --}}
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td colspan="3" style="text-align:center; padding-right: 10px;">Total Keseluruhan</td>
                    <td style="text-align:center;">{{ $grandTotal }} Orang</td>
                    <td style="text-align:center;">100%</td>
                    <td style="text-align:center;">
                        <div style="background-color: #e5e7eb; border-radius: 3px; width: 100%; height: 10px; display: inline-block; overflow: hidden; vertical-align: middle;">
                            <div style="background-color: #4b5563; height: 100%; width: 100%;"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- TTD & QR CODE --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    {{-- ======================================================= --}}
                    {{-- 1. TAMPILAN KHUSUS PEGAWAI (HARDCODE & TANPA QR CODE) --}}
                    {{-- ======================================================= --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container" style="margin: 10px 0;">
                        {{-- Ruang kosong pengganti QR Code agar posisi seimbang --}}
                        <div style="height: 85px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        Deny Haryuniansyah
                    </div>
                    <div style="margin-top:4px;">
                        NIP. 1973062022006041016
                    </div>

                @else
                    {{-- ======================================================= --}}
                    {{-- 2. TAMPILAN DINAMIS ADMIN/KEPALA (DARI DB & ADA QR CODE)--}}
                    {{-- ======================================================= --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container" style="margin: 10px 0;">
                        @if(isset($qrToken))
                            @php
                                // Karena ini rekap keseluruhan, periode bisa kita buat "Data Keseluruhan" atau "Hingga saat ini"
                                // atau jika controller mengirim bulan/tahun, kita bisa tangkap juga.
                                $periode = request('bulan') && request('tahun')
                                    ? \Carbon\Carbon::create()->month((int) request('bulan'))->format('F') . ' ' . request('tahun')
                                    : 'Data Keseluruhan PTM';

                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                            @endphp

                            {{-- Judul disesuaikan menjadi: Laporan Kelompok Usia --}}
                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Kelompok%20Usia%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
                        @else
                            <div style="height: 85px;"></div>
                        @endif
                    </div>

                    <div class="name" style="margin-top: 0;">
                        {{ $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah' }}
                    </div>
                    <div style="margin-top:4px;">
                        NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
                    </div>
                @endif

            </div>
        </div>

    </div>

</body>

</html>