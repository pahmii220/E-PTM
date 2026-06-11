<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan PTM</title>

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
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">

        {{-- BUTTON PRINT --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px;margin-right:6px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px;background:#eee;color:#000;text-decoration:none;">
                Kembali
            </a>
        </div>

        {{-- KOP DINAS --}}
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
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; font-weight:700;">
                LAPORAN KEGIATAN PENCEGAHAN<br>
                PENYAKIT TIDAK MENULAR (PTM)
            </h3>
        </div>

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Nama Kegiatan</th>
                    <th>Jenis Kegiatan</th>
                    <th style="width:110px">Tanggal</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th> {{-- Posisi Baru: Keterangan Bergeser ke Kiri --}}
                    <th style="width:100px">Jumlah Peserta</th> {{-- Posisi Baru: Jumlah Peserta di Pojok Kanan --}}
                </tr>
            </thead>

            <tbody>
                @forelse($items as $i => $row)
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td>{{ $row->nama_kegiatan }}</td>
                        <td style="text-align:center">{{ $row->jenis_kegiatan }}</td>
                        <td style="text-align:center">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}
                        </td>
                        <td>{{ $row->lokasi }}</td>
                        <td>{{ $row->keterangan ?? '-' }}</td> {{-- Data Keterangan --}}
                        <td style="text-align:center">
                            {{ $row->jumlah_peserta ?? '-' }}
                        </td> {{-- Data Jumlah Peserta --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">
                            Tidak ada data kegiatan PTM
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- FOOTER TABEL: Baris Total Jumlah Peserta otomatis --}}
            @if($items->count() > 0)
                <tfoot>
                    <tr style="font-weight: bold; background-color: #eee;">
                        {{-- 1. Kosongkan satu sel di bawah kolom 'No' agar teks bergeser --}}
                        <td></td>

                        {{-- 2. Teks TOTAL PESERTA diletakkan mulai dari bawah 'Nama Kegiatan' (colspan dikurangi jadi 5) --}}
                        <td colspan="5" style="text-align: left; padding: 6px;">TOTAL</td>

                        {{-- 3. Angka Total --}}
                        <td style="text-align: center; padding: 6px;">{{ $items->sum('jumlah_peserta') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- TEKS REKAPITULASI DI BAWAH TABEL --}}
        <div style="margin-top:10px; font-size:12px; font-weight:700; line-height: 1.6;">
            <div>Jumlah kegiatan PTM sebanyak = {{ $items->count() }} kegiatan</div>

        </div>

{{-- TANDA TANGAN --}}
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
                        // Judul Laporan
                        $judul = 'Laporan Kegiatan PTM';

                        // Menentukan periode (opsional: bisa ambil dari request jika ada filter bulan/tahun)
                        $periode = (request('bulan') && request('tahun'))
                            ? \Carbon\Carbon::create()->month((int) request('bulan'))->format('F') . ' ' . request('tahun')
                            : 'Data Kegiatan ' . now()->year;

                        $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                        // Identitas Kepala
                        $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                        $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                    @endphp

                    {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=' . urlencode($judul) . '&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
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