<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Logistik Kegiatan - {{ $perlengkapan->laporanMonitoring->judul_laporan ?? ($perlengkapan->suratTugas->nomor_surat ?? 'PTM') }}</title>
    <style>
        @page { margin: 20mm 15mm; size: A4; }
        body { font-family: "Arial", sans-serif; font-size: 13px; color: #000; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        
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
        
        .title { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 30px; text-decoration: underline; text-transform: uppercase; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .items-table th { background-color: #f0f0f0; text-align: center; }
        .items-table td.center { text-align: center; }
        
        .ttd-box { width: 100%; margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-item { width: 300px; text-align: center; }
        .ttd-space { height: 70px; }

        .kop { text-align: center; margin-bottom: 20px; }
        .kop h2, .kop h3, .kop h4 { margin: 0; padding: 2px; font-weight: normal; font-size: 16px; }
        .kop h3 { font-size: 15px; }

        .no-print { text-align: right; margin-bottom: 15px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 15px; background:#198754; color:#fff; border:none; border-radius:4px; cursor:pointer;">🖨️ Cetak Dokumen</button>
        </div>

        <div class="kop">
            <div class="left"><img src="{{ asset('images/dinkes.png') }}" style="width:65px;"></div>
            <div class="center">
                <br>
                <div style="font-size:17px;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div style="font-size:18px;">DINAS KESEHATAN</div>
                <div style="font-size:12px;">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        <div class="title" style="margin-bottom: 25px;">
            <span style="text-decoration: underline; font-weight: bold; font-size: 16px;">LAPORAN PERLENGKAPAN LOGISTIK KEGIATAN PTM</span>
        </div>

        <table style="width: 100%; margin-bottom: 15px; font-size: 13px; text-align: left;">
            @if($perlengkapan->laporanMonitoring)
                <tr>
                    <td style="width: 140px; vertical-align: top;">Judul Laporan Monitoring</td>
                    <td style="width: 10px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;"><strong>{{ $perlengkapan->laporanMonitoring->judul_laporan }}</strong></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Puskesmas Tujuan</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">Puskesmas {{ $perlengkapan->laporanMonitoring->puskesmas->nama_puskesmas ?? '-' }} (Kec. {{ $perlengkapan->laporanMonitoring->puskesmas->kecamatan ?? '-' }})</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Rekomendasi Logistik</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $perlengkapan->laporanMonitoring->rekomendasi_tindakan }}</td>
                </tr>
            @else
                <tr>
                    <td style="width: 140px; vertical-align: top;">Nomor STL</td>
                    <td style="width: 10px; vertical-align: top;">:</td>
                    <td style="vertical-align: top;"><strong>{{ $perlengkapan->suratTugas->nomor_surat ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Agenda Kunjungan</td>
                    <td style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $perlengkapan->suratTugas->maksud_tujuan ?? '-' }}</td>
                </tr>
            @endif
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama Alat / Barang Logistik</th>
                    <th style="width: 150px;">Jumlah / Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perlengkapan->items as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="center"><strong>{{ $item->jumlah }} {{ $item->satuan ?? 'Unit' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($perlengkapan->catatan)
            <p><strong>Catatan Tambahan:</strong><br> {{ $perlengkapan->catatan }}</p>
        @endif

        <div class="ttd-box" style="justify-content: flex-end;">
            <div class="ttd-item" style="width: 300px; text-align: center;">
                Banjarmasin, {{ \Carbon\Carbon::parse($perlengkapan->updated_at)->translatedFormat('d F Y') }}<br>
                Mengetahui/Menyetujui,<br>
                <strong>{{ isset($kepalaAktif) ? 'KEPALA BIDANG P2P' : 'KEPALA DINAS' }}</strong>
                
                <div style="font-size:7px; margin-top: 5px;">
                    Dokumen ini telah disahkan secara elektronik
                </div>

                <div class="qr-container" style="display: flex; justify-content: center; align-items: center; height: 85px; margin: 10px 0;">
                    @php
$periode = \Carbon\Carbon::parse($perlengkapan->created_at)->translatedFormat('F Y');
$tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

$namaPejabat = $kepalaAktif->nama_kepala ?? 'dr. H. DIAUDDIN, M.Kes';
$nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                    @endphp

                    {!! QrCode::size(85)->generate(route('verifikasi.laporan', [
    'judul' => 'Laporan Perlengkapan Tugas Luar PTM',
    'periode' => $periode,
    'tanggal_sah' => $tanggalSah,
    'nama_kepala' => $namaPejabat,
    'nip' => $nipPejabat
])) !!}
                </div>

                <u><strong>{{ $kepalaAktif->nama_kepala ?? 'dr. H. DIAUDDIN, M.Kes' }}</strong></u><br>
                NIP. {{ $kepalaAktif->nip ?? '1973062022006041016' }}
            </div>
        </div>
    </div>
</body>
</html>
