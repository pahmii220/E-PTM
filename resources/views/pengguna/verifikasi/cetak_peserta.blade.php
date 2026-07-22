<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Pasien Pemeriksaan</title>
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

        /* ====== TABLE ====== */
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

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; text-decoration:none; color:#000;">Kembali</a>
        </div>

        {{-- KOP SURAT --}}
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

        {{-- JUDUL --}}
        <div style="text-align:center;margin-bottom:10px;">
            <h3 style="margin:0;font-size:15px;letter-spacing:0.6px;">LAPORAN PASIEN</h3>
        </div>

    {{-- INFO BULAN / PERIODE --}}
    <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">
        @if(request('filter_type') === 'tanggal' && request('tgl_awal') && request('tgl_akhir'))
            Periode: {{ \Carbon\Carbon::parse(request('tgl_awal'))->format('d/m/Y') }} s/d
            {{ \Carbon\Carbon::parse(request('tgl_akhir'))->format('d/m/Y') }}
        @else
            Bulan:
            {{ \Carbon\Carbon::create()->month((int) request('bulan', now()->month))->locale('id')->translatedFormat('F') }}
            {{ request('tahun', now()->year) }}
        @endif
    </div>
    
    {{-- KETERANGAN LAPORAN --}}
    <div style="font-size:12px; margin-bottom:10px;">
        Laporan ini berisi daftar pasien berdasarkan periode yang dipilih. Informasi yang
        disajikan digunakan sebagai bahan pendataan, monitoring, dan penyusunan laporan pasien pada Dinas Kesehatan Provinsi
        Kalimantan Selatan.
    </div>
    
    {{-- TABEL DATA --}}
    <table class="grid">

        {{-- TABEL DATA --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>No. RM</th>
                    <th>Tanggal Lahir</th>
                    <th>Nama Pasien</th>
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

        {{-- TOTAL --}}
        <div style="margin-top:6px; font-size:12px; font-weight:700;">
            Jumlah keseluruhan pasien sebanyak = {{ $items->count() }} orang
        </div>

        {{-- BLOK TTD & QR (Telah disinkronkan) --}}
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

                    <div class="qr-container">
                        {{-- Ruang kosong pengganti QR Code agar tata letak tetap presisi --}}
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

                                <div style="font-size:7px;">
                                    Dokumen ini telah disahkan secara elektronik
                                </div>

                                <div class="qr-container">
                                    @if(!empty($qrToken))
                                        @php
        if (request('filter_type') === 'tanggal' && request('tgl_awal') && request('tgl_akhir')) {
            $periode = \Carbon\Carbon::parse(request('tgl_awal'))->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse(request('tgl_akhir'))->format('d/m/Y');
        } else {
            $bulanAngka = (int) request('bulan', now()->month);
            $tahun = request('tahun', now()->year);
            $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;
        }

        // Tambahkan ->setTimezone('Asia/Makassar') untuk WITA
        $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i'); 
                                        @endphp

                                        {!! QrCode::size(100)->generate(url('/verifikasi-laporan?judul=Laporan%20Pasien%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah))) !!}
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