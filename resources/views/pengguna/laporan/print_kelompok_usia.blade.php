<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan PTM Berdasarkan Kelompok Usia</title>

    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            size: portrait;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
            color: #111;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 6px 8px;
            box-sizing: border-box;
        }

        /* ====== KOP ====== */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .kop-table td.logo-cell {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-table td.logo-cell img {
            width: 70px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .kop-table td.text-cell {
            text-align: center;
            vertical-align: middle;
        }
        .kop-table td.spacer-cell {
            width: 80px;
        }

        .prov {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dinas {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .addr {
            font-size: 11px;
            margin-top: 4px;
            font-style: italic;
            line-height: 1.3;
        }

        hr.top {
            border: none;
            border-top: 2px solid #000;
            margin: 6px 0 12px 0;
        }

        /* ====== CHART SECTION ====== */
        .chart-box {
            background-color: #fcfcfc;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }

        .bar-item {
            margin-bottom: 8px;
        }

        .bar-item:last-child {
            margin-bottom: 0;
        }

        .bar-label-group {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            margin-bottom: 3px;
        }

        .bar-track {
            background-color: #e5e7eb;
            border-radius: 4px;
            height: 16px;
            width: 100%;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .bar-fill {
            height: 100%;
            background-color: #198754;
            border-radius: 4px;
            transition: width 0.3s ease;
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
            padding: 5px 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #f3f4f6;
            font-weight: 700;
            text-align: center;
        }

        /* ====== NO PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 12mm 15mm;
            }
            .no-print {
                display: none;
            }
        }

        /* ====== TTD & QR CODE ====== */
        .ttd {
            width: 100%;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd .block {
            width: 42%;
            text-align: center;
            font-size: 12px;
        }

        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 85px;
            margin: 8px 0;
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
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ asset('images/dinkes.png') }}" alt="logo">
                </td>
                <td class="text-cell">
                    <div class="prov">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div class="dinas">DINAS KESEHATAN</div>
                    <div class="addr">
                        Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732<br>
                        (Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)
                    </div>
                </td>
                <td class="spacer-cell"></td>
            </tr>
        </table>

        <hr class="top">

        {{-- JUDUL --}}
        <div style="width:100%; text-align:center; margin-bottom:12px;">
            <h3 style="margin:0; font-size:14px; letter-spacing:0.5px; font-weight:700; color:#000; text-transform:uppercase;">
                LAPORAN REKAPITULASI PENYAKIT TIDAK MENULAR (PTM)<br>
                BERDASARKAN KELOMPOK USIA
            </h3>
        </div>

        {{-- PENJELASAN SINGKAT --}}
        <div style="font-size: 11px; color: #333; margin-bottom: 10px; line-height: 1.4; text-align: justify;">
            Laporan ini menyajikan distribusi dan persentase sebaran peserta pemeriksaan Penyakit Tidak Menular (PTM) yang dikelompokkan berdasarkan kategori usia (Remaja, Dewasa, Pra Lansia, dan Lansia) pada Dinas Kesehatan Provinsi Kalimantan Selatan.
        </div>

        {{-- INFO FILTER AKTIF --}}
        @if(request('kota') || request('kecamatan') || request('puskesmas_id') || request('filter_waktu'))
        <div style="font-size: 11px; margin-bottom: 12px; border: 1px dashed #999; padding: 6px 10px; background-color: #f9f9f9;">
            @if(request('kota')) <strong>Kota/Kab:</strong> {{ request('kota') }} &nbsp;|&nbsp; @endif
            @if(request('kecamatan')) <strong>Kecamatan:</strong> {{ request('kecamatan') }} &nbsp;|&nbsp; @endif
            @if(request('puskesmas_id'))
                @php
                    $pusk = \App\Models\Puskesmas::find(request('puskesmas_id'));
                @endphp
                <strong>Puskesmas:</strong> {{ $pusk ? $pusk->nama_puskesmas : request('puskesmas_id') }} &nbsp;|&nbsp;
            @endif
            @php
                $namaBulanIndoUsia = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            @endphp
            @if(request('filter_waktu') == 'bulan' && request('bulan')) 
                @php
                    $bNum = (int) request('bulan');
                    $namaB = $namaBulanIndoUsia[$bNum] ?? 'Juli';
                @endphp
                <strong>Periode:</strong> {{ $namaB }} {{ request('tahun', date('Y')) }}
            @elseif(request('filter_waktu') == 'tanggal' && request('tgl_awal') && request('tgl_akhir')) 
                @php
                    $t1 = \Carbon\Carbon::parse(request('tgl_awal'));
                    $t2 = \Carbon\Carbon::parse(request('tgl_akhir'));
                    $pStr = $t1->format('d') . ' ' . ($namaBulanIndoUsia[(int)$t1->format('m')] ?? '') . ' ' . $t1->format('Y') . ' s/d ' . $t2->format('d') . ' ' . ($namaBulanIndoUsia[(int)$t2->format('m')] ?? '') . ' ' . $t2->format('Y');
                @endphp
                <strong>Periode:</strong> {{ $pStr }}
            @endif
        </div>
        @endif

        @php
            $totalRemaja = $dataUsia['remaja'] ?? 0;
            $totalDewasa = $dataUsia['dewasa'] ?? 0;
            $totalPraLansia = $dataUsia['pra_lansia'] ?? 0;
            $totalLansia = $dataUsia['lansia'] ?? 0;
            $grandTotal = $totalRemaja + $totalDewasa + $totalPraLansia + $totalLansia;

            $persenRemaja = $grandTotal > 0 ? round(($totalRemaja / $grandTotal) * 100, 1) : 0;
            $persenDewasa = $grandTotal > 0 ? round(($totalDewasa / $grandTotal) * 100, 1) : 0;
            $persenPraLansia = $grandTotal > 0 ? round(($totalPraLansia / $grandTotal) * 100, 1) : 0;
            $persenLansia = $grandTotal > 0 ? round(($totalLansia / $grandTotal) * 100, 1) : 0;

            // Cari kelompok dominan
            $highestCategory = 'Pra Lansia';
            $highestPersen = $persenPraLansia;
            if ($persenRemaja >= max($persenDewasa, $persenPraLansia, $persenLansia)) {
                $highestCategory = 'Remaja (< 18 Tahun)'; $highestPersen = $persenRemaja;
            } elseif ($persenDewasa >= max($persenRemaja, $persenPraLansia, $persenLansia)) {
                $highestCategory = 'Dewasa (18-44 Tahun)'; $highestPersen = $persenDewasa;
            } elseif ($persenPraLansia >= max($persenRemaja, $persenDewasa, $persenLansia)) {
                $highestCategory = 'Pra Lansia (45-59 Tahun)'; $highestPersen = $persenPraLansia;
            } elseif ($persenLansia >= max($persenRemaja, $persenDewasa, $persenPraLansia)) {
                $highestCategory = 'Lansia (≥ 60 Tahun)'; $highestPersen = $persenLansia;
            }
        @endphp

        {{-- VISUAL GRAFIK BATANG HORIZONTAL (PRINT-FRIENDLY CSS) --}}
        <div class="chart-box">
            <div class="chart-title">📊 Visualisasi Grafik Sebaran Kelompok Usia</div>
            
            <div class="bar-item">
                <div class="bar-label-group">
                    <span><strong>1. Remaja</strong> (< 18 Tahun)</span>
                    <span><strong>{{ $persenRemaja }}%</strong> ({{ $totalRemaja }} Orang)</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ $persenRemaja }}%;"></div>
                </div>
            </div>

            <div class="bar-item">
                <div class="bar-label-group">
                    <span><strong>2. Dewasa</strong> (18 – 44 Tahun)</span>
                    <span><strong>{{ $persenDewasa }}%</strong> ({{ $totalDewasa }} Orang)</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ $persenDewasa }}%;"></div>
                </div>
            </div>

            <div class="bar-item">
                <div class="bar-label-group">
                    <span><strong>3. Pra Lansia</strong> (45 – 59 Tahun)</span>
                    <span><strong>{{ $persenPraLansia }}%</strong> ({{ $totalPraLansia }} Orang)</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ $persenPraLansia }}%;"></div>
                </div>
            </div>

            <div class="bar-item">
                <div class="bar-label-group">
                    <span><strong>4. Lansia</strong> (≥ 60 Tahun)</span>
                    <span><strong>{{ $persenLansia }}%</strong> ({{ $totalLansia }} Orang)</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ $persenLansia }}%;"></div>
                </div>
            </div>
        </div>

        {{-- TABEL DATA RINGKAS --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Kelompok Usia</th>
                    <th>Rentang Usia</th>
                    <th style="width:130px">Jumlah Peserta</th>
                    <th style="width:100px">Persentase</th>
                    <th style="width:120px">Kategori Risiko</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center">1</td>
                    <td><strong>Remaja</strong></td>
                    <td style="text-align:center">&lt; 18 Tahun</td>
                    <td style="text-align:center"><strong>{{ $totalRemaja }}</strong> Orang</td>
                    <td style="text-align:center"><strong>{{ $persenRemaja }}%</strong></td>
                    <td style="text-align:center; font-size:11px;">Rendah</td>
                </tr>
                <tr>
                    <td style="text-align:center">2</td>
                    <td><strong>Dewasa</strong></td>
                    <td style="text-align:center">18 – 44 Tahun</td>
                    <td style="text-align:center"><strong>{{ $totalDewasa }}</strong> Orang</td>
                    <td style="text-align:center"><strong>{{ $persenDewasa }}%</strong></td>
                    <td style="text-align:center; font-size:11px;">Sedang</td>
                </tr>
                <tr>
                    <td style="text-align:center">3</td>
                    <td><strong>Pra Lansia</strong></td>
                    <td style="text-align:center">45 – 59 Tahun</td>
                    <td style="text-align:center"><strong>{{ $totalPraLansia }}</strong> Orang</td>
                    <td style="text-align:center"><strong>{{ $persenPraLansia }}%</strong></td>
                    <td style="text-align:center; font-size:11px; font-weight:bold;">Tinggi</td>
                </tr>
                <tr>
                    <td style="text-align:center">4</td>
                    <td><strong>Lansia</strong></td>
                    <td style="text-align:center">≥ 60 Tahun</td>
                    <td style="text-align:center"><strong>{{ $totalLansia }}</strong> Orang</td>
                    <td style="text-align:center"><strong>{{ $persenLansia }}%</strong></td>
                    <td style="text-align:center; font-size:11px; font-weight:bold;">Sangat Tinggi</td>
                </tr>
                {{-- BARIS TOTAL --}}
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" style="text-align:right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                    <td style="text-align:center;">{{ $grandTotal }} Orang</td>
                    <td style="text-align:center;">100%</td>
                    <td style="text-align:center;">-</td>
                </tr>
            </tbody>
        </table>

        {{-- EXECUTIVE INSIGHT BOX --}}
        <div style="border: 1px solid #198754; border-left: 5px solid #198754; background-color: #f8fdf9; padding: 8px 12px; margin-top: 12px; font-size: 11px; line-height: 1.4;">
            <strong>💡 Catatan:</strong><br>
            Berdasarkan data rekapitulasi di atas, kelompok penderita PTM terbesar didominasi oleh kategori <strong>{{ $highestCategory }}</strong> yaitu sebesar <strong>{{ $highestPersen }}%</strong> (dari total {{ $grandTotal }} peserta). Direkomendasikan penguatan intervensi deteksi dini dan edukasi gaya hidup sehat pada kelompok usia tersebut melalui kegiatan Posbindu PTM dan fasilitas pelayanan kesehatan.
        </div>

        {{-- TTD & QR CODE --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container" style="margin: 8px 0;">
                        <div style="height: 85px;"></div>
                    </div>

                    <div class="name" style="margin-top: 0;">
                        {{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                    </div>
                    <div style="margin-top:4px;">
                        NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}
                    </div>

                @else
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container" style="margin: 8px 0;">
                        @if(isset($qrToken))
                            @php
                                $namaBulanIndoUsiaQr = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                if (request('bulan')) {
                                    $bNum = (int) request('bulan');
                                    $periode = ($namaBulanIndoUsiaQr[$bNum] ?? 'Juli') . ' ' . request('tahun', date('Y'));
                                } else {
                                    $now = \Carbon\Carbon::now();
                                    $periode = ($namaBulanIndoUsiaQr[(int)$now->format('m')] ?? 'Juli') . ' ' . $now->format('Y');
                                }

                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS';
                                $nipPejabat = $kepalaAktif->nip ?? '197008081990031003';
                            @endphp

                            {!! QrCode::size(85)->generate(\App\Helpers\DocumentSigner::url([
                                'judul' => 'Laporan Kelompok Usia PTM',
                                'periode' => $periode,
                                'tanggal_sah' => $tanggalSah,
                                'nama_kepala' => $namaPejabat,
                                'nip' => $nipPejabat,
                                'jabatan' => $kepalaAktif->jabatan ?? 'Kepala Bidang P2PTM',
                                'catatan' => request('catatan_pengesahan') ?? 'Analisis distribusi tren kelompok usia penderita PTM telah diverifikasi sah.'
                            ])) !!}
                        @else
                            <div style="height: 85px;"></div>
                        @endif
                    </div>

                    <div class="name" style="margin-top: 0;">
                        {{ $kepalaAktif->nama_kepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                    </div>
                    <div style="margin-top:4px;">
                        NIP. {{ $kepalaAktif->nip ?? '197008081990031003' }}
                    </div>
                @endif

            </div>
        </div>

    </div>

</body>

</html>
