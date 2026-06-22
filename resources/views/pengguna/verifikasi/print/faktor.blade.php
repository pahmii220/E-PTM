<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Faktor Risiko PTM</title>
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
            margin-top: 5px;
            font-weight: 700;
            text-decoration: underline;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            /* Ruang untuk QR Code */
            margin: 10px 0;
        }

        @media print {
            .no-print {
                display: none;
            }

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

        <div style="text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px;">LAPORAN FAKTOR RESIKO PENYAKIT TIDAK
                MENULAR(PTM)
            </h3>
        </div>

        {{-- Menampilkan bulan di sebelah kiri --}}
        @if(isset($bulan) && isset($tahun))
            <div style="text-align: left; margin-bottom: 8px; font-size: 13px; font-weight: bold;">
                Bulan: {{ \Carbon\Carbon::create()->month((int) $bulan)->locale('id')->translatedFormat('F') }} {{ $tahun }}
            </div>
        @endif

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Peserta</th>
                    <th style="width:110px">Tanggal Pemeriksaan</th>
                    <th style="width:80px">Merokok</th>
                    <th style="width:80px">Alkohol</th>
                    <th style="width:110px">Kurang Aktivitas</th>
                    <th>Puskesmas</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items ?? [] as $item)
                    <tr>
                        <td style="text-align:center">{{ $loop->iteration }}</td>
                        <td>{{ optional($item->pasien)->nama_lengkap ?? '-' }}</td>
                        <td style="text-align:center">
                            {{ $item->tanggal_pemeriksaan ? \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->format('d-m-Y') : ($item->dibuat_pada ? $item->dibuat_pada->format('d-m-Y') : '-') }}
                        </td>
                        <td style="text-align:center">{{ $item->merokok ?? '-' }}</td>
                        <td style="text-align:center">{{ $item->alkohol ?? '-' }}</td>
                        <td style="text-align:center">{{ $item->kurang_aktivitas_fisik ?? '-' }}</td>
                        <td>{{ optional($item->puskesmas)->nama_puskesmas ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-3" style="text-align: center;">
                            Tidak ada data faktor risiko untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- BAGIAN TANDA TANGAN & QR CODE (Telah disinkronkan) --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                {{-- Cek URL: Jika diakses dari rute yang diawali 'pengguna/' maka ini adalah role Pegawai --}}
                @if(request()->is('pengguna/*'))
                    {{-- ======================================================= --}}
                    {{-- 1. TAMPILAN KHUSUS PEGAWAI (HARDCODE & TANPA QR CODE) --}}
                    {{-- ======================================================= --}}
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        KEPALA BIDANG P2PTM
                    </div>

                    <div class="qr-container">
                        @if(!empty($qrToken))
                            @php
        $bulanAngka = (int) request('bulan', now()->month);
        $tahun = request('tahun', now()->year);
        $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;

        $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i'); 
                            @endphp

                            {{-- KUNCI PERBAIKANNYA ADA DI BARIS INI (Memakai url(), bukan $qrToken) --}}
                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Faktor%20Risiko%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah))) !!}
                        @else
                            <div style="height: 85px;"></div>
                        @endif
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
                    $bulanAngka = (int) request('bulan', now()->month);
                    $tahun = request('tahun', now()->year);
                    $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;

                    $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i'); 
                                        @endphp

                                        {{-- INI YANG PENTING: Gunakan URL, bukan $qrToken --}}
                                        {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Faktor%20Risiko%20PTM&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah))) !!}
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