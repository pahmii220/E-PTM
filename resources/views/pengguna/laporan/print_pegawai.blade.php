<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Data Pegawai Dinkes P2PTM</title>

    <style>
        /* ====== SETTING CETAK LANDSCAPE ====== */
        @page {
            size: landscape;
            margin: 10mm 10mm;
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
            margin: 0 auto;
            padding: 4px 6px;
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
            font-size: 10.5px;
            table-layout: fixed;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 5px 6px;
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
            width: 30%;
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
                    Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732 <br>
(Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        {{-- JUDUL --}}
        <div style="width:100%; text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; font-weight:700;">
                LAPORAN DATA PEGAWAI SUB BAGIAN P2PTM
            </h3>
        </div>

        {{-- PENJELASAN SINGKAT --}}
        <div style="font-size: 11px; color: #444; margin-bottom: 12px; line-height: 1.4; text-align: left;">
            Laporan ini menyajikan daftar pegawai Dinas Kesehatan beserta rincian identitas lengkap (NIP, Tanggal Lahir, Telepon, Jabatan, Golongan, Wilayah/Provinsi, dan Alamat) yang bertugas dalam pencegahan Penyakit Tidak Menular (P2PTM).
        </div>

        {{-- TABEL --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:3%">No</th>
                    <th style="width:11%">NIP</th>
                    <th style="width:15%">Nama Pegawai</th>
                    <th style="width:7.5%">Tgl Lahir</th>
                    <th style="width:8.5%">Telepon/HP</th>
                    <th style="width:14%">Jabatan</th>
                    <th style="width:8%">Golongan</th>
                    <th style="width:6%">Bidang</th>
                    <th style="width:10%">Provinsi & Wilayah</th>
                    <th style="width:17%">Alamat Lengkap</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataPegawai ?? [] as $index => $row)
                    <tr>
                        <td style="text-align:center">{{ $index + 1 }}</td>
                        <td style="text-align:center">{{ $row->nip ?? '-' }}</td>
                        <td>{{ $row->nama_pegawai ?? '-' }}</td>
                        <td style="text-align:center">{{ $row->tgl_lahir ? \Carbon\Carbon::parse($row->tgl_lahir)->format('d-m-Y') : '-' }}</td>
                        <td style="text-align:center">{{ $row->telepon ?? '-' }}</td>
                        <td>{{ $row->jabatan ?? '-' }}</td>
                        <td style="text-align:center">{{ $row->golongan ?? '-' }}</td>
                        <td style="text-align:center">{{ $row->bidang ?? '-' }}</td>
                        <td style="text-align:center">
                            {{ $row->provinsi ?? 'Kalimantan Selatan' }}
                            @if($row->kabupaten_kota)
                                <br><small style="color:#555;">({{ $row->kabupaten_kota }})</small>
                            @endif
                        </td>
                        <td>{{ $row->alamat ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:20px;">Belum ada data pegawai dinkes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TTD & QR CODE --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container" style="margin: 10px 0;">
                        <div style="height: 85px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        Deny Haryuniansyah
                    </div>
                    <div style="margin-top:4px;">
                        NIP. 1973062022006041016
                    </div>
                @else
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container" style="margin: 10px 0;">
                        @if(isset($qrToken))
                            @php
                                $periode = 'Data Pegawai Dinkes P2PTM';
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');
                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                            @endphp

                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Data%20Pegawai%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
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
