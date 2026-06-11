<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Deteksi Dini PTM</title>
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
            max-width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            box-sizing: border-box;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 4px 6px;
            vertical-align: middle;
            word-wrap: break-word;
            box-sizing: border-box;
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

        .ttd .block .name {
            margin-top: 5px;
            font-weight: 700;
            text-decoration: underline;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 10px 0;
        }

        /* ====== PRINT FALLBACK ====== */
        @media print {
            table.grid {
                -webkit-print-color-adjust: exact;
                box-shadow: inset -1px 0 0 #111;
                font-size: 11.5px;
            }

            table.grid tr>td:last-child,
            table.grid tr>th:last-child {
                border-right: 1px solid #111;
                box-shadow: inset -1px 0 0 #111;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; color:#000; text-decoration:none;">Kembali</a>
        </div>

        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" alt="logo" style="width:65px; height:auto;">
            </div>
            <br>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        {{-- JUDUL --}}
        <div style="text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px;">LAPORAN DETEKSI DINI PENYAKIT TIDAK MENULAR(PTM)
            </h3>
        </div>

        {{-- INFO BULAN --}}
        @if(isset($bulan) && isset($tahun))
            <div style="text-align: left; margin-bottom: 8px; font-size: 13px; font-weight: bold;">
                Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->locale('id')->translatedFormat('F') }} {{ $tahun }}
            </div>
        @endif

        {{-- TABEL DATA --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Peserta</th>
                    <th style="width:120px">Tanggal Pemeriksaan</th>
                    <th style="width:90px">Tekanan</th>
                    <th style="width:80px">Gula</th>
                    <th style="width:140px">Jenis Tindak Lanjut</th>
                    <th>Puskesmas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $row)
                    <tr>
                        <td style="text-align:center;">
                            {{ (isset($items) && is_object($items) && method_exists($items, 'firstItem')) ? $items->firstItem() + $i : $i + 1 }}
                        </td>
                        <td>
                            {{ optional($row->pasien)->nama_lengkap ?? ($row->nama_pasien ?? '-') }}
                        </td>
                        <td style="text-align:center;">
                            {{ $row->tanggal_pemeriksaan ? \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d-m-Y') : '-' }}
                        </td>
                        <td style="text-align:center;">
                            {{ $row->tekanan_darah ?? '-' }}
                        </td>
                        <td style="text-align:center;">
                            {{ $row->gula_darah ?? '-' }}
                        </td>
                        <td>
                            {{ optional($row->tindakLanjut)->jenis_tindak_lanjut ?? '-' }}
                        </td>
                        <td>
                            {{ optional($row->puskesmas)->nama_puskesmas ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 10px;">
                            Tidak ada data deteksi dini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- BAGIAN TANDA TANGAN & QR CODE --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    {{-- 1. TAMPILAN KHUSUS PEGAWAI (HARDCODE & TANPA QR CODE) --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container">
                        <div style="height: 85px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        Deny Haryuniansyah
                    </div>
                    <div style="margin-top:4px;">
                        NIP. 1973062022006041016
                    </div>

                @else
                    {{-- 2. TAMPILAN DINAMIS ADMIN/KEPALA DENGAN QR CODE VERIFIKASI --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    {{-- INI BAGIAN YANG DIUBAH --}}
                    <div class="qr-container">
                        @if(!empty($qrToken))
                            @php
                                $bulanAngka = (int) request('bulan', now()->month);
                                $tahun = request('tahun', now()->year);
                                $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                // Ambil nama dan NIP untuk dikirim ke QR Code
                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                            @endphp

                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Deteksi%20Dini%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
                        @else
                            <div style="height: 85px;"></div>
                        @endif
                    </div>
                    {{-- AKHIR BAGIAN YANG DIUBAH --}}

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