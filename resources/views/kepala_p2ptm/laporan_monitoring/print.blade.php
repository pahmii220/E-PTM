<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Monitoring PTM</title>

    <style>
        @page {
            size: auto;
            margin: 0mm; /* Menghilangkan header/footer URL bawaan browser */
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 15mm 12mm; /* Memindahkan margin ke body agar konten tidak mepet */
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

        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 8px;
            vertical-align: top;
            text-align: left;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            width: 30%;
        }

        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }

            table.grid {
                font-size: 12px;
            }
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

        /* Kotak untuk QR Code */
        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px;">Print</button>
            <a href="javascript:history.back()"
                style="padding:8px 12px; background:#eee; text-decoration:none;">Kembali</a>
        </div>

        <div class="kop">
            <div class="left">
                <img src="{{ asset('images/dinkes.png') }}" style="width:65px;">
            </div>
            <br>
            <div class="center">
                <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                <div class="dinas">DINAS KESEHATAN</div>
                <div class="addr">Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732
                    <br>
                (Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        <div style="text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px;">LAPORAN HASIL MONITORING PTM</h3>
        </div>

        {{-- PENJELASAN SINGKAT --}} 
        <div style="font-size: 11px; color: #444; margin-bottom: 12px; line-height: 1.4; text-align: left;"> 
            Laporan ini berisi rincian hasil monitoring pelaksanaan program Penyakit Tidak Menular (PTM) di tingkat Puskesmas. Data yang dilaporkan digunakan sebagai bahan evaluasi dan tindak lanjut di Dinas Kesehatan.
        </div>

        <table class="grid">
            <tr>
                <th>Puskesmas Tujuan</th>
                <td>{{ $laporan->puskesmas->nama_puskesmas ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Dibuat</th>
                <td>{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <th>Diajukan Oleh</th>
                <td>{{ $laporan->pegawai->nama_pegawai ?? ($laporan->pegawai->user->Nama_Lengkap ?? '-') }}</td>
            </tr>
            <tr>
                <th>Status Laporan</th>
                <td>
                    <strong>{{ strtoupper($laporan->status_laporan) }}</strong>
                    @if($laporan->status_laporan === 'disetujui' && $laporan->tanggal_disetujui)
                        <br><i>(Disetujui pada: {{ \Carbon\Carbon::parse($laporan->tanggal_disetujui)->translatedFormat('d F Y') }})</i>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Judul Temuan</th>
                <td><strong>{{ $laporan->judul_laporan ?? '-' }}</strong></td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{!! nl2br(e($laporan->deskripsi_temuan ?? '-')) !!}</td>
            </tr>
            <tr>
                <th>Rekomendasi / Kesimpulan</th>
                <td>{!! nl2br(e($laporan->rekomendasi_tindakan ?? '-')) !!}</td>
            </tr>
            @if($laporan->catatan_kepala)
            <tr>
                <th>Catatan Kepala P2PTM</th>
                <td>{!! nl2br(e($laporan->catatan_kepala)) !!}</td>
            </tr>
            @endif
        </table>

        <div class="ttd">
            <div class="block">
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:10px; font-weight:bold;">
                    {{ isset($kepalaAktif) ? 'KEPALA BIDANG P2P' : 'KEPALA DINAS' }}
                </div>

                <div style="font-size:7px;">
                    Dokumen ini telah disahkan secara elektronik
                </div>

                <div class="qr-container">
                    @if($laporan->status_laporan === 'disetujui')
                        @php
    $periode = \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('F Y');
    $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

    $namaPejabat = $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS';
    $nipPejabat = $kepalaAktif->nip ?? '197008081990031003';
                        @endphp

                        {!! QrCode::size(85)->generate(\App\Helpers\DocumentSigner::url([
                            'judul' => 'Laporan Hasil Monitoring PTM',
                            'periode' => $periode,
                            'tanggal_sah' => $tanggalSah,
                            'nama_kepala' => $namaPejabat,
                            'nip' => $nipPejabat,
                            'jabatan' => $kepalaAktif->jabatan ?? 'Kepala Bidang P2PTM',
                            'catatan' => $laporan->catatan_kepala ?? (request('catatan_pengesahan') ?? 'Laporan hasil monitoring kegiatan P2PTM telah diverifikasi dan disahkan.')
                        ])) !!}
                    @else
                        <div style="height: 85px;"></div>
                    @endif
                </div>

                <div class="name">
                    {{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                </div>
                <div>
                    NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}
                </div>
            </div>
        </div>

    </div>
</body>

</html>
