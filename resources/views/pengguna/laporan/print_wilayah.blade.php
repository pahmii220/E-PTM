<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Rekap PTM Per Wilayah</title>
    <style>
        /* ====== SETTING CETAK ====== */
        @page {
            size: landscape;
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
                <div class="addr">Jalan Belitung Darat No.118 — Telp: (0511) 3355661 — Banjarmasin 70116</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        {{-- JUDUL --}}
        <div class="title">
            LAPORAN PEMETAAN KASUS PENYAKIT TIDAK MENULAR (PTM)<br>
            TINGKAT KECAMATAN
        </div>

        {{-- PENJELASAN SINGKAT --}}
        <div style="background-color: #f8f9fa; border-left: 4px solid #198754; padding: 12px 15px; margin-bottom: 15px; font-size: 12px; line-height: 1.5; text-align: justify;">
            Laporan ini menyajikan akumulasi data riwayat pemeriksaan skrining PTM, deteksi penyakit dominan, tingkat status risiko kesehatan masyarakat, serta total penanganan tindak lanjut per wilayah kecamatan.
        </div>

        {{-- TABEL DATA --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:3%;">No</th>
                    <th style="width:18%;">Wilayah (Kecamatan)</th>
                    <th style="width:15%;">Kota / Kabupaten</th>
                    <th style="width:11%;">Jumlah Faskes</th>
                    <th style="width:11%;">Total Pasien</th>
                    <th style="width:10%;">Riwayat Skrining</th>
                    <th style="width:16%;">Penyakit Dominan</th>
                    <th style="width:12%;">Status Risiko</th>
                    <th style="width:4%;">Penanganan Selesai</th>
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

                        $statusBadge = match($item->status_risiko ?? 'Aman / Rendah') {
                            'Risiko Tinggi' => 'background:#fee2e2;color:#991b1b;padding:2px 6px;border-radius:4px;font-weight:bold;',
                            'Risiko Sedang' => 'background:#fef3c7;color:#92400e;padding:2px 6px;border-radius:4px;font-weight:bold;',
                            default         => 'background:#dcfce7;color:#166534;padding:2px 6px;border-radius:4px;font-weight:bold;',
                        };
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="left"><strong>{{ $item->kecamatan }}</strong></td>
                        <td>{{ $item->nama_kabupaten }}</td>
                        <td>{{ $item->jumlah_puskesmas }} Puskesmas</td>
                        <td><strong>{{ $item->total_peserta }}</strong></td>
                        <td>{{ $item->total_skrining }}</td>
                        <td style="font-weight:600;">{{ $item->penyakit_dominan ?? 'Nihil / Normal' }}</td>
                        <td><span style="{{ $statusBadge }}">{{ $item->status_risiko ?? 'Aman / Rendah' }}</span></td>
                        <td>{{ $item->total_tindak_lanjut }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">Tidak ada data wilayah yang dapat ditampilkan.</td>
                    </tr>
                @endforelse
                
                @if($dataWilayah->count() > 0)
                    <tr style="font-weight: bold; background-color: #f1f1f1;">
                        <td colspan="3" style="text-align: right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                        <td>{{ $sumFaskes }} Puskesmas</td>
                        <td>{{ $sumPasien }}</td>
                        <td>{{ $sumSkrining }}</td>
                        <td>-</td>
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
                                $bulanAngka = (int) request('bulan', now()->month);
                                $tahun = request('tahun', now()->year);
                                $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;
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
