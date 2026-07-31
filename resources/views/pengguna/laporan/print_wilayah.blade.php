<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Rekap PTM Per Wilayah</title>
    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            size: landscape;
            margin: 0;
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
            max-width: 100%;
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

        /* ====== TOMBOL PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* ====== JUDUL ====== */
        .title {
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        /* ====== TABEL ====== */
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
            padding: 6px;
            vertical-align: middle;
            word-wrap: break-word;
            box-sizing: border-box;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            text-align: center;
        }

        table.grid td {
            text-align: center;
        }

        table.grid td.left {
            text-align: left;
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

        /* ====== PRINT FALLBACK ====== */
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 10mm 12mm;
            }
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

        {{-- TOMBOL ACTION --}}
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
                <div class="addr">Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732 <br>
(Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        @php
            $namaBulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            if (request('filter_waktu') == 'tanggal' && request('tgl_awal') && request('tgl_akhir')) {
                $tAwal = \Carbon\Carbon::parse(request('tgl_awal'));
                $tAkhir = \Carbon\Carbon::parse(request('tgl_akhir'));
                $textPeriode = $tAwal->format('d') . ' ' . $namaBulanIndo[(int)$tAwal->format('m')] . ' ' . $tAwal->format('Y') . ' s/d ' . $tAkhir->format('d') . ' ' . $namaBulanIndo[(int)$tAkhir->format('m')] . ' ' . $tAkhir->format('Y');
            } elseif (request('bulan')) {
                $bIdx = (int) request('bulan');
                $textPeriode = ($namaBulanIndo[$bIdx] ?? '') . ' ' . request('tahun', date('Y'));
            } else {
                $now = \Carbon\Carbon::now();
                $textPeriode = $namaBulanIndo[(int)$now->format('m')] . ' ' . $now->format('Y');
            }
        @endphp

        {{-- JUDUL --}}
        <div class="title">
            LAPORAN PEMETAAN KASUS PENYAKIT TIDAK MENULAR (PTM)<br>
            TINGKAT KECAMATAN
        </div>

        {{-- PENJELASAN SINGKAT & PERIODE --}}
        <div style="background-color: #f8f9fa; border-left: 4px solid #198754; padding: 10px 15px; margin-bottom: 15px; font-size: 12px; line-height: 1.5; text-align: justify;">
            Laporan ini menyajikan akumulasi data riwayat pemeriksaan skrining PTM, deteksi penyakit dominan, tingkat status risiko kesehatan masyarakat, serta total penanganan tindak lanjut per wilayah kecamatan.
            <div style="margin-top: 5px; font-weight: bold; color: #166534;">
                Periode Laporan: {{ $textPeriode }}
            </div>
        </div>

        {{-- TABEL DATA --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:17%;">Wilayah (Kecamatan)</th>
                    <th style="width:16%;">Kota / Kabupaten</th>
                    <th style="width:23%;">Nama Puskesmas / Faskes</th>
                    <th style="width:9%;">Total Pasien</th>
                    <th style="width:9%;">Riwayat Skrining</th>
                    <th style="width:13%;">Penyakit Dominan</th>
                    <th style="width:9%;">Penanganan Selesai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumFaskes = 0; $sumPasien = 0; $sumSkrining = 0;
                    $sumRisiko = 0; $sumTindakLanjut = 0;
                @endphp
                @forelse ($dataWilayah as $item)
                    @php
                        $sumFaskes += $item->jumlah_puskesmas;
                        $sumPasien += $item->total_peserta;
                        $sumSkrining += $item->total_skrining;
                        $sumRisiko += $item->total_risiko;
                        $sumTindakLanjut += $item->total_tindak_lanjut;
                        $pCount = count($item->puskesmas_list);
                    @endphp

                    @if($pCount > 0)
                        @foreach($item->puskesmas_list as $pIndex => $pusk)
                            <tr>
                                @if($pIndex === 0)
                                    <td rowspan="{{ $pCount }}" style="vertical-align: middle;">{{ $loop->parent->iteration }}</td>
                                    <td rowspan="{{ $pCount }}" class="left" style="vertical-align: middle;"><strong>{{ $item->kecamatan }}</strong></td>
                                    <td rowspan="{{ $pCount }}" style="vertical-align: middle;">{{ $item->nama_kabupaten }}</td>
                                @endif
                                <td class="left"><strong>{{ $pusk->nama_puskesmas }}</strong></td>
                                <td><strong>{{ $pusk->total_peserta }}</strong></td>
                                <td>{{ $pusk->total_skrining }}</td>
                                <td style="font-weight:600;">{{ $pusk->penyakit_dominan ?? 'Nihil / Normal' }}</td>
                                <td>{{ $pusk->total_tindak_lanjut }}</td>
                            </tr>
                        @endforeach
                        {{-- SUB-TOTAL PER KECAMATAN --}}
                        <tr style="font-weight: bold; background-color: #e2e8f0; color: #0f172a;">
                            <td colspan="4" style="text-align: right; padding-right: 15px; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; background-color: #e2e8f0; border-top: 1.5px solid #334155; border-bottom: 2px solid #334155;">
                                &Sigma; SUBTOTAL KEC. {{ strtoupper($item->kecamatan) }} ({{ $item->jumlah_puskesmas }} Faskes)
                            </td>
                            <td style="font-weight: 800; font-size: 11px; background-color: #e2e8f0; border-top: 1.5px solid #334155; border-bottom: 2px solid #334155;">{{ $item->total_peserta }}</td>
                            <td style="font-weight: 800; font-size: 11px; background-color: #e2e8f0; border-top: 1.5px solid #334155; border-bottom: 2px solid #334155;">{{ $item->total_skrining }}</td>
                            <td style="font-weight: 700; font-size: 10px; background-color: #e2e8f0; border-top: 1.5px solid #334155; border-bottom: 2px solid #334155;">{{ $item->penyakit_dominan }}</td>
                            <td style="font-weight: 800; font-size: 11px; background-color: #e2e8f0; border-top: 1.5px solid #334155; border-bottom: 2px solid #334155;">{{ $item->total_tindak_lanjut }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="left"><strong>{{ $item->kecamatan }}</strong></td>
                            <td>{{ $item->nama_kabupaten }}</td>
                            <td class="left" style="color:#94a3b8; font-style:italic;">Tidak ada puskesmas</td>
                            <td>0</td>
                            <td>0</td>
                            <td>Nihil / Normal</td>
                            <td>0</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8">Tidak ada data wilayah yang dapat ditampilkan.</td>
                    </tr>
                @endforelse
                
                @if($dataWilayah->count() > 0)
                    <tr style="font-weight: bold; background-color: #e2e8f0; font-size: 11px;">
                        <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                        <td>{{ $sumFaskes }} Puskesmas</td>
                        <td>{{ $sumPasien }} Pasien</td>
                        <td>{{ $sumSkrining }}</td>
                        <td>-</td>
                        <td>{{ $sumTindakLanjut }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- BAGIAN TANDA TANGAN & QR CODE --}}
        <div class="ttd">
            <div class="block">
                <br>
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                @if(auth()->check() && auth()->user()->role_name === 'pegawai')
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
                    <div style="margin-top:10px; font-weight: bold; text-transform: uppercase;">
                        {{ $kepalaAktif->jabatan ?? 'KEPALA BIDANG P2PTM' }}
                    </div>

                    <div class="qr-container">
                        @if(isset($qrToken))
                            @php
                                $namaBulanIndoQr = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $bulanAngka = (int) request('bulan', now()->month);
                                $tahun = request('tahun', now()->year);
                                $periode = ($namaBulanIndoQr[$bulanAngka] ?? '') . ' ' . $tahun;
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                            @endphp

                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=Laporan%20Rekap%20PTM%20Per%20Wilayah&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
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
