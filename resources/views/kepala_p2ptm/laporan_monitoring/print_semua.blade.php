<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Rekap Laporan Hasil Monitoring PTM</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body {
                margin: 0 !important;
                padding: 12mm 15mm !important;
            }
            a[href]:after {
                content: none !important;
            }
            a {
                text-decoration: none !important;
                color: inherit !important;
            }
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 12mm 15mm;
            background: #fff;
            color: #000;
            -webkit-print-color-adjust: exact;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* KOP */
        .kop {
            text-align: center;
            margin-bottom: 10px;
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
            font-size: 16px;
            font-weight: 700;
        }

        .kop .dinas {
            font-size: 20px;
            font-weight: 900;
            margin-top: 2px;
        }

        .kop .addr {
            font-size: 12px;
            margin-top: 4px;
        }

        hr.top {
            border: none;
            border-top: 2px solid #000;
            margin: 8px 0 16px 0;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 15px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 6px 8px;
            vertical-align: top;
            text-align: left;
        }

        table.grid th {
            background: #f0f0f0;
            font-weight: 700;
            text-align: center;
        }

        .no-print {
            margin-bottom: 15px;
            text-align: right;
        }

        .btn-print {
            padding: 8px 16px;
            background: #0d9488;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-back {
            padding: 8px 16px;
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 8px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* Footer TTD */
        .ttd {
            width: 100%;
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .ttd .block {
            width: 300px;
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
            height: 80px;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="no-print">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
            <a href="javascript:history.back()" class="btn-back">Tutup / Kembali</a>
        </div>

        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width: 75px; height: auto;">
            </div>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732
                    <br>(Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        <div style="text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:16px; text-transform:uppercase;">LAPORAN HASIL MONITORING Penyakit Tidak Menular (PTM)</h3>
            <div style="font-size: 12px; margin-top: 4px; color: #333;">
                Periode: 
                @php
                    $bulanIndo = [
                        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                @if(!empty($bulan) && isset($bulanIndo[$bulan]))
                    <strong>Bulan {{ $bulanIndo[$bulan] }} {{ \Carbon\Carbon::now()->year }}</strong>
                @elseif(!empty($startDate) && !empty($endDate))
                    <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
                @elseif(!empty($startDate))
                    <strong>Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}</strong>
                @elseif(!empty($endDate))
                    <strong>Sampai {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</strong>
                @else
                    <strong>Semua Periode Transaksi</strong>
                @endif
            </div>
        </div>

        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 150px;">Pegawai Pengaju</th>
                    <th style="width: 160px;">Puskesmas Tujuan</th>
                    <th>Judul & Temuan Monitoring</th>
                    <th>Rekomendasi / Tindakan</th>
                    <th style="width: 90px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}</td>
                        <td><strong>{{ $row->pegawai->nama_pegawai ?? ($row->pegawai->user->Nama_Lengkap ?? '-') }}</strong></td>
                        <td><strong>{{ $row->puskesmas->nama_puskesmas ?? '-' }}</strong><br><small style="color:#666;">Kec. {{ $row->puskesmas->kecamatan ?? '-' }}</small></td>
                        <td>
                            <strong>{{ $row->judul_laporan }}</strong>
                            <p style="margin: 4px 0 0 0; color: #444; font-size: 10.5px;">{{ $row->deskripsi_temuan }}</p>
                        </td>
                        <td>
                            <span style="color: #000;">{{ $row->rekomendasi_tindakan }}</span>
                            @if($row->catatan_kepala)
                                <div style="margin-top: 4px; font-size: 10px; color: #b91c1c;"><em>Catatan Kepala: {{ $row->catatan_kepala }}</em></div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($row->status_laporan === 'disetujui')
                                <strong style="color: #16a34a;">DISETUJUI</strong>
                            @elseif($row->status_laporan === 'ditolak')
                                <strong style="color: #dc2626;">DITOLAK</strong>
                            @else
                                <strong style="color: #d97706;">PENDING</strong>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                            Tidak ada data laporan hasil monitoring pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd">
            <div class="block">
                <div>BANJARMASIN, {{ \Carbon\Carbon::now()->setTimezone('Asia/Makassar')->translatedFormat('d F Y') }}</div>
                <div style="margin-top:5px; font-weight:bold;">
                    {{ isset($kepalaAktif) ? 'KEPALA BIDANG P2P' : 'KEPALA DINAS' }}
                </div>

                <div style="font-size:7.5px; margin-top: 4px; color: #555;">
                    Dokumen ini telah disahkan secara elektronik
                </div>

                <div class="qr-container">
                    @php
                        $periode = \Carbon\Carbon::now()->translatedFormat('F Y');
                        $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');
                        $namaPejabat = $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS';
                        $nipPejabat = $kepalaAktif->nip ?? '197008081990031003';
                    @endphp

                    {!! QrCode::size(80)->generate(\App\Helpers\DocumentSigner::url([
                        'judul' => 'Rekapitulasi Laporan Hasil Monitoring PTM',
                        'periode' => $periode,
                        'tanggal_sah' => $tanggalSah,
                        'nama_kepala' => $namaPejabat,
                        'nip' => $nipPejabat,
                        'jabatan' => $kepalaAktif->jabatan ?? 'Kepala Bidang P2PTM',
                        'catatan' => request('catatan_pengesahan') ?? 'Rekapitulasi laporan hasil monitoring terverifikasi telah disahkan.'
                    ])) !!}
                </div>

                <div class="name">
                    {{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                </div>
                <div style="margin-top: 3px;">
                    NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}
                </div>
            </div>
        </div>

    </div>
</body>
</html>
