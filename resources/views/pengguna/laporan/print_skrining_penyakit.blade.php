<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Skrining & Jenis Penyakit</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            size: portrait;
            margin: 15mm 12mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            max-width: 800px;
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

        /* ====== TITLE ====== */
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        /* ====== TABLE ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            table-layout: fixed;
            margin-bottom: 20px;
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

        table.grid td.left {
            text-align: left;
        }

        table.grid td.center {
            text-align: center;
        }

        .table-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 15px;
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
        {{-- TOMBOL PRINT --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px; cursor:pointer;">Print</button>
            <button onclick="window.close()"
                style="padding:8px 12px; background:#eee; color:#000; border:1px solid #ccc; cursor:pointer;">Tutup</button>
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
        <div class="title">
            LAPORAN REKAPITULASI SKRINING & JENIS PENYAKIT (PTM)
        </div>

        {{-- NARASI EKSEKUTIF --}}
        <div style="background-color: #f8f9fa; border-left: 4px solid #198754; padding: 12px 15px; margin-bottom: 15px; font-size: 13px; line-height: 1.5; text-align: justify;">
            {!! $narasiEksekutif ?? 'Menampilkan laporan agregat hasil skrining dan pemetaan penyakit.' !!}
        </div>

        {{-- TABEL HASIL SKRINING --}}
        <div class="table-title">1. HASIL SKRINING PTM</div>
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Kategori Hasil Skrining</th>
                    <th style="width: 120px;">Total Individu</th>
                    <th style="width: 100px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSkrining = collect($dataSkrining)->sum('jumlah'); @endphp
                @forelse($dataSkrining as $index => $row)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="left"><strong>{{ $row->hasil_skrining }}</strong></td>
                        <td class="center">{{ number_format($row->jumlah, 0, ',', '.') }} orang</td>
                        <td class="center">
                            <strong>{{ $totalSkrining > 0 ? round(($row->jumlah / $totalSkrining) * 100, 1) : 0 }}%</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center">Tidak ada data hasil skrining.</td>
                    </tr>
                @endforelse
                @if(count($dataSkrining) > 0)
                    <tr style="background-color: #f1f1f1; font-weight: bold;">
                        <td colspan="2" class="center">TOTAL</td>
                        <td class="center">{{ number_format($totalSkrining, 0, ',', '.') }} orang</td>
                        <td class="center">100%</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- TABEL PEMETAAN JENIS PENYAKIT --}}
        <div class="table-title">2. PEMETAAN JENIS PENYAKIT PADA PASIEN BERISIKO</div>
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Diagnosa Penyakit</th>
                    <th style="width: 120px;">Jumlah Kasus</th>
                    <th style="width: 100px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPenyakit = collect($dataPenyakit)->sum('jumlah'); @endphp
                @forelse($dataPenyakit as $index => $row)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="left"><strong>{{ $row->diagnosa_penyakit }}</strong></td>
                        <td class="center">{{ number_format($row->jumlah, 0, ',', '.') }} orang</td>
                        <td class="center">
                            <strong>{{ $totalPenyakit > 0 ? round(($row->jumlah / $totalPenyakit) * 100, 1) : 0 }}%</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="center">Tidak ada data penyakit PTM terkait.</td>
                    </tr>
                @endforelse
                @if(count($dataPenyakit) > 0)
                    <tr style="background-color: #f1f1f1; font-weight: bold;">
                        <td colspan="2" class="center">TOTAL KASUS PENYAKIT</td>
                        <td class="center">{{ number_format($totalPenyakit, 0, ',', '.') }} orang</td>
                        <td class="center">100%</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- KETERANGAN DI BAWAH TABEL PENYAKIT --}}
        @php
            $jumlahNormalCetak      = collect($dataSkrining)->where('hasil_skrining', 'Normal')->sum('jumlah');
            $jumlahTerindikasi      = $totalSkrining - $jumlahNormalCetak;
            $pctTerindikasi         = $totalSkrining > 0 ? round(($jumlahTerindikasi / $totalSkrining) * 100, 1) : 0;
            $pctNormalCetak         = $totalSkrining > 0 ? round(($jumlahNormalCetak / $totalSkrining) * 100, 1) : 0;
        @endphp
        @if($totalSkrining > 0)
        <div style="margin-top: 10px; padding: 10px 14px; background: #f0fdf4; border-left: 4px solid #16a34a; font-size: 12px; line-height: 1.6; text-align: justify;">
            <strong>Keterangan:</strong>
            Dari total <strong>{{ number_format($totalSkrining, 0, ',', '.') }} pasien</strong> yang mengikuti skrining,
            sebanyak <strong>{{ number_format($jumlahTerindikasi, 0, ',', '.') }} orang ({{ $pctTerindikasi }}%)</strong>
            terindikasi memiliki diagnosa penyakit tidak menular (PTM) dan masuk dalam kategori berisiko,
            sedangkan <strong>{{ number_format($jumlahNormalCetak, 0, ',', '.') }} orang ({{ $pctNormalCetak }}%)</strong>
            dinyatakan <strong>Normal</strong> (tidak terindikasi PTM).
            Persentase pada kolom "Pemetaan Jenis Penyakit" dihitung dari total kasus berisiko,
            bukan dari total seluruh peserta skrining.
        </div>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="ttd">
            <div class="block">
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL, {{ date('d-m-Y') }}</div>
                <div style="font-weight: 700; margin-top:10px;">KEPALA BIDANG P2PTM</div>

                <div class="qr-container">
                    @if(isset($qrToken))
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($qrToken) !!}
                    @else
                        <div style="height:80px; width:80px; border:1px solid #ccc; display:inline-block;"></div>
                    @endif
                </div>

                @if(isset($kepalaAktif))
                    <div class="name">{{ $kepalaAktif->user->nama ?? 'Nama Kepala P2PTM' }}</div>
                    <div style="margin-top:2px;">NIP. {{ $kepalaAktif->nip ?? '-' }}</div>
                @else
                    <div class="name">Nama Kepala P2PTM</div>
                    <div style="margin-top:2px;">NIP. -</div>
                @endif
            </div>
        </div>

    </div>

</body>
</html>
