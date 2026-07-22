<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Rekap PTM Per Puskesmas</title>
    <style>
        /* ====== SETTING CETAK LANDSCAPE ====== */
        @page {
            size: landscape;
            margin: 10mm 10mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 4px 6px;
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
            border-collapse: collapse;
            font-size: 10.5px;
            table-layout: fixed;
            box-sizing: border-box;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #111;
            padding: 5px 6px;
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
            width: 30%;
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
                font-size: 10px;
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
                <div class="addr">Jalan Dharma Praja, Banjarbaru, Kalimantan Selatan Kode Pos 70732
                    <br>
                (Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan)</div>
            </div>
            <div class="clear"></div>
        </div>

        <hr class="top">

        {{-- JUDUL --}}
        <div class="title">
            @if(isset($puskesmasTerpilih) && $puskesmasTerpilih)
                LAPORAN DETAIL REGISTER PASIEN PTM<br>
                {{ strtoupper($puskesmasTerpilih->nama_puskesmas) }}
            @else
                LAPORAN REKAP PENYAKIT TIDAK MENULAR (PTM)<br>
                PER PUSKESMAS
            @endif
        </div>

        {{-- NARASI EKSEKUTIF --}}
        <div style="background-color: #f8f9fa; border-left: 4px solid #198754; padding: 12px 15px; margin-bottom: 15px; font-size: 12px; line-height: 1.5; text-align: justify;">
            {!! $narasiEksekutif ?? 'Tidak ada data untuk periode ini.' !!}
        </div>

        {{-- TABEL MATRIKS REKAPITULASI KINERJA PUSKESMAS (TAMPIL HANYA JIKA TIDAK MEMILIH PUSKESMAS SPESIFIK) --}}
        @if(!isset($puskesmasTerpilih) || !$puskesmasTerpilih)
            <table class="grid">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:4%;">No</th>
                        <th rowspan="2" style="width:32%;">Nama Puskesmas</th>
                        <th rowspan="2" style="width:14%;">Total Pasien</th>
                        <th colspan="2" style="width:16%;">Demografi</th>
                        <th colspan="2" style="width:17%;">Temuan Skrining</th>
                        <th colspan="2" style="width:17%;">Tindak Lanjut</th>
                    </tr>
                    <tr>
                        <th style="width:8%;">L</th>
                        <th style="width:8%;">P</th>
                        <th style="width:8.5%;">Berisiko PTM</th>
                        <th style="width:8.5%;">Normal</th>
                        <th style="width:8.5%;">Edukasi</th>
                        <th style="width:8.5%;">Rujukan RS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumPasien = 0; $sumLaki = 0; $sumPerem = 0;
                        $sumBerisiko = 0; $sumNormal = 0; $sumEdukasi = 0; $sumRujukan = 0;
                    @endphp
                    @forelse ($rekapPuskesmas as $item)
                        @php
                            $sumPasien += $item->total_peserta;
                            $sumLaki += $item->total_laki;
                            $sumPerem += $item->total_perempuan;
                            $sumBerisiko += $item->total_berisiko ?? 0;
                            $sumNormal += $item->total_normal ?? 0;
                            $sumEdukasi += $item->total_edukasi;
                            $sumRujukan += $item->total_rujukan;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="left"><strong>{{ $item->nama_puskesmas }}</strong></td>
                            <td><strong>{{ $item->total_peserta }}</strong></td>
                            <td>{{ $item->total_laki }}</td>
                            <td>{{ $item->total_perempuan }}</td>
                            <td><span style="{{ ($item->total_berisiko ?? 0) > 0 ? 'color:#b91c1c;font-weight:bold;' : '' }}">{{ $item->total_berisiko ?? 0 }}</span></td>
                            <td>{{ $item->total_normal ?? 0 }}</td>
                            <td>{{ $item->total_edukasi }}</td>
                            <td>{{ $item->total_rujukan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Tidak ada data rekap puskesmas.</td>
                        </tr>
                    @endforelse
                    @if($rekapPuskesmas->count() > 0)
                        <tr style="font-weight: bold; background-color: #f1f1f1;">
                            <td colspan="2" style="text-align: right; padding-right: 15px;">TOTAL KESELURUHAN</td>
                            <td>{{ $sumPasien }}</td>
                            <td>{{ $sumLaki }}</td>
                            <td>{{ $sumPerem }}</td>
                            <td>{{ $sumBerisiko }}</td>
                            <td>{{ $sumNormal }}</td>
                            <td>{{ $sumEdukasi }}</td>
                            <td>{{ $sumRujukan }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        {{-- TABEL DETAIL REGISTER PASIEN PTM (TAMPIL HANYA JIKA MEMILIH PUSKESMAS SPESIFIK & ADA DETAIL PASIEN) --}}
        @if(isset($puskesmasTerpilih) && $puskesmasTerpilih && isset($detailPasienPuskesmas) && $detailPasienPuskesmas->count() > 0)
            <div style="margin-top: 20px; margin-bottom: 8px;">
                <div style="font-size: 13px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #111; padding-bottom: 4px; margin-bottom: 6px;">
                    DATA DETAIL REGISTER PASIEN PTM @if(isset($puskesmasTerpilih) && $puskesmasTerpilih) — {{ strtoupper($puskesmasTerpilih->nama_puskesmas) }} @endif
                </div>
                <div style="font-size: 10.5px; color: #333; margin-bottom: 6px;">
                    Wilayah: {{ $puskesmasTerpilih->kecamatan ?? '-' }}, {{ $puskesmasTerpilih->nama_kabupaten ?? 'Kota Banjarmasin' }} | Total Pasien: <strong>{{ $detailPasienPuskesmas->count() }} Orang</strong>
                </div>
            </div>

            <table class="grid">
                <thead>
                    <tr>
                        <th style="width: 3.5%;">No</th>
                        <th style="width: 8.5%;">Tanggal</th>
                        <th style="width: 14%;">Nama Pasien</th>
                        <th style="width: 11%;">No RM</th>
                        <th style="width: 5%;">Umur</th>
                        <th style="width: 6%;">JK</th>
                        <th style="width: 7%;">TD (mmHg)</th>
                        <th style="width: 6.5%;">Gula</th>
                        <th style="width: 6.5%;">Kolesterol</th>
                        <th style="width: 5%;">IMT</th>
                        <th style="width: 13%;">Faktor Risiko</th>
                        <th style="width: 14%;">Diagnosa &amp; Penyakit PTM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailPasienPuskesmas as $d)
                        @php
                            $umur = $d->peserta && $d->peserta->tanggal_lahir ? \Carbon\Carbon::parse($d->peserta->tanggal_lahir)->age : '-';
                            $isHipertensi = ($d->sistole >= 140 || $d->diastole >= 90);
                            $isDiabetes = ($d->gula_darah > 200);
                            $isKolesterol = ($d->kolesterol > 200);
                            $isImtTinggi = ($d->imt > 25);

                            $noRmFormatted = $d->peserta->no_rekam_medis 
                                ?? ($d->peserta->nik 
                                    ?? ('RM-' . str_replace('-', '', \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('Ymd')) . '-' . str_pad($d->peserta_id ?? 1, 3, '0', STR_PAD_LEFT)));
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td style="text-align:center;">{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                            <td class="left"><strong>{{ $d->peserta->nama_lengkap ?? '-' }}</strong></td>
                            <td style="text-align:center;">{{ $noRmFormatted }}</td>
                            <td style="text-align:center;">{{ $umur }} Thn</td>
                            <td style="text-align:center;">{{ $d->peserta->jenis_kelamin ?? '-' }}</td>
                            <td style="text-align:center; {{ $isHipertensi ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                                {{ $d->sistole && $d->diastole ? $d->sistole . '/' . $d->diastole : ($d->tekanan_darah ?? '-') }}
                            </td>
                            <td style="text-align:center; {{ $isDiabetes ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                                {{ $d->gula_darah ?? '-' }}
                            </td>
                            <td style="text-align:center; {{ $isKolesterol ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                                {{ $d->kolesterol ?? '-' }}
                            </td>
                            <td style="text-align:center; {{ $isImtTinggi ? 'color:#b45309; font-weight:bold;' : '' }}">
                                {{ $d->imt ?? '-' }}
                            </td>
                            <td class="left">
                                @php
                                    $riskList = [];
                                    if(optional($d->faktorRisiko)->merokok == 'Ya') $riskList[] = 'Merokok';
                                    if(optional($d->faktorRisiko)->kurang_aktivitas == 'Ya') $riskList[] = 'Kurang Olahraga';
                                    if(optional($d->faktorRisiko)->kurang_sayur_buah == 'Ya') $riskList[] = 'Kurang Sayur/Buah';
                                @endphp
                                @if(count($riskList) > 0)
                                    {{ implode(', ', $riskList) }}
                                @else
                                    Aman / Normal
                                @endif
                            </td>
                            <td class="left">
                                @if($d->diagnosa_penyakit)
                                    <strong style="color: #b91c1c;">{{ $d->diagnosa_penyakit }}</strong>
                                @else
                                    <span style="color: #15803d;">Sehat / Normal</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- BAGIAN TANDA TANGAN & QR CODE (Sudah Disinkronkan Berdasarkan Role) --}}
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

                    <div class="qr-container">
                        @if(isset($qrToken))
                            @php
                                // Ambil parameter dari request, default ke bulan/tahun saat ini
                                $bulanAngka = (int) request('bulan', now()->month);
                                $tahun = request('tahun', now()->year);
                                $periode = \Carbon\Carbon::create()->month($bulanAngka)->format('F') . ' ' . $tahun;
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

                                $namaPejabat = $kepalaAktif->nama_kepala ?? 'Deny Haryuniansyah';
                                $nipPejabat = $kepalaAktif->nip ?? '1973062022006041016';
                                $judulQR = isset($puskesmasTerpilih) && $puskesmasTerpilih 
                                    ? 'Laporan Detail Register Pasien PTM - ' . $puskesmasTerpilih->nama_puskesmas 
                                    : 'Laporan Rekap PTM Per Puskesmas';
                            @endphp

                            {!! QrCode::size(85)->generate(url('/verifikasi-laporan?judul=' . urlencode($judulQR) . '&periode=' . urlencode($periode) . '&tanggal_sah=' . urlencode($tanggalSah) . '&nama_kepala=' . urlencode($namaPejabat) . '&nip=' . urlencode($nipPejabat))) !!}
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