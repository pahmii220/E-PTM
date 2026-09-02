<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Pasien - {{ $peserta->nama_lengkap }} ({{ $peserta->puskesmas->nama_puskesmas ?? 'Puskesmas' }})</title>
    <style>
        /* ====== SETTING CETAK LANDSCAPE ====== */
        @page {
            size: landscape;
            margin: 0;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #111;
            -webkit-print-color-adjust: exact;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px 18px;
            box-sizing: border-box;
        }

        /* ====== KOP SURAT ====== */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .kop-table td.logo-cell {
            width: 75px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-table td.logo-cell img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .kop-table td.text-cell {
            text-align: center;
            vertical-align: middle;
        }
        .kop-table td.spacer-cell {
            width: 75px;
        }

        .prov {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dinas {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 1px;
            letter-spacing: 0.5px;
        }

        .puskesmas-name {
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f766e;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .addr {
            font-size: 10.5px;
            margin-top: 3px;
            font-style: italic;
            line-height: 1.3;
            color: #333;
        }

        hr.top {
            border: none;
            border-top: 2px solid #000;
            margin: 6px 0 10px 0;
        }

        /* ====== TOMBOL PRINT ====== */
        .no-print {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 8mm 10mm;
            }
            .no-print {
                display: none;
            }
        }

        /* ====== JUDUL LAPORAN ====== */
        .title {
            text-align: center;
            font-weight: 700;
            font-size: 13.5px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        /* ====== BOX BIODATA PASIEN ====== */
        .patient-box {
            width: 100%;
            border: 1px solid #333;
            background: #f8fafc;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .patient-table td {
            padding: 2.5px 5px;
            vertical-align: top;
        }

        /* ====== TABEL GRID RIWAYAT ====== */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            table-layout: fixed;
            box-sizing: border-box;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #222;
            padding: 5px 6px;
            vertical-align: middle;
            word-wrap: break-word;
            box-sizing: border-box;
            line-height: 1.35;
        }

        table.grid th {
            background: #e2e8f0;
            font-weight: 700;
            text-align: center;
            color: #1e293b;
        }

        table.grid td {
            text-align: center;
        }

        table.grid td.left {
            text-align: left;
        }

        /* ====== TTD PETUGAS ====== */
        .ttd {
            width: 100%;
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd .block {
            width: 32%;
            text-align: center;
            font-size: 11px;
        }

        .ttd .block .name {
            margin-top: 4px;
            font-weight: 700;
            text-decoration: underline;
        }

        @media print {
            table.grid {
                -webkit-print-color-adjust: exact;
                box-shadow: inset -1px 0 0 #222;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- TOMBOL ACTION --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:7px 16px; background:#0f766e; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                🖨️ Cetak Dokumen Riwayat
            </button>
            <button onclick="window.close()" style="padding:7px 14px; background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; border-radius:6px; margin-left:6px; cursor:pointer; font-size:12px;">
                Tutup
            </button>
        </div>

        {{-- KOP SURAT RESMI PUSKESMAS --}}
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ asset('images/dinkes.png') }}" alt="logo">
                </td>
                <td class="text-cell">
                    <div class="prov">PEMERINTAH KOTA BANJARMASIN</div>
                    <div class="dinas">DINAS KESEHATAN</div>
                    <div class="puskesmas-name">
                        {{ strtoupper($peserta->puskesmas->nama_puskesmas ?? 'PUSKESMAS KOTA BANJARMASIN') }}
                    </div>
                    <div class="addr">
                        {{ $peserta->puskesmas->alamat ?? 'Kawasan Kota Banjarmasin, Kalimantan Selatan' }} 
                        @if($peserta->puskesmas && $peserta->puskesmas->kecamatan)
                            | Wilayah Kerja: Kec. {{ $peserta->puskesmas->kecamatan }}
                        @endif
                    </div>
                </td>
                <td class="spacer-cell"></td>
            </tr>
        </table>

        <hr class="top">

        {{-- JUDUL DOKUMEN --}}
        <div class="title">
            LAPORAN RIWAYAT PASIEN PEMERIKSAAN KESEHATAN BERKALA PTM<br>
            <span style="font-size: 11px; font-weight: normal; text-transform: none; color: #475569;">
                Rekam Jejak Medis &amp; Riwayat Deteksi Dini Penyakit Tidak Menular Individu
            </span>
        </div>

        {{-- PROFIL IDENTITAS PASIEN --}}
        <div class="patient-box">
            <table class="patient-table">
                <tr>
                    <td style="width: 14%; font-weight: bold;">Nama Pasien</td>
                    <td style="width: 36%;">: <strong>{{ $peserta->nama_lengkap }}</strong></td>
                    <td style="width: 15%; font-weight: bold;">No. Rekam Medis</td>
                    <td style="width: 35%;">: <strong>{{ $peserta->no_rekam_medis ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NIK</td>
                    <td>: {{ $peserta->nik ?? '-' }}</td>
                    <td style="font-weight: bold;">Umur / Jenis Kelamin</td>
                    <td>: {{ \Carbon\Carbon::parse($peserta->tanggal_lahir)->age }} Tahun / {{ $peserta->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Tempat, Tgl Lahir</td>
                    <td>: {{ $peserta->tempat_lahir ?? '-' }}, {{ \Carbon\Carbon::parse($peserta->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                    <td style="font-weight: bold;">Faskes / Puskesmas</td>
                    <td>: <strong>{{ $peserta->puskesmas->nama_puskesmas ?? '-' }}</strong> (Kec. {{ $peserta->kecamatan ?? '-' }})</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Alamat Pasien</td>
                    <td>: {{ $peserta->alamat ?? '-' }}</td>
                    <td style="font-weight: bold;">Total Riwayat Kunjungan</td>
                    <td>: <span style="font-weight: bold; color: #0f766e;">{{ count($riwayatKunjungan) }} Kali Pemeriksaan</span></td>
                </tr>
            </table>
        </div>

        {{-- TABEL KRONOLOGIS RIWAYAT PEMERIKSAAN --}}
        <table class="grid">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">No</th>
                    <th rowspan="2" style="width: 7.5%;">Tgl Periksa</th>
                    <th rowspan="2" style="width: 11%;">Fasilitas Kesehatan</th>
                    <th colspan="3" style="width: 20%;">Parameter Klinis &amp; Vital Sign</th>
                    <th rowspan="2" style="width: 6%;">IMT / LP</th>
                    <th rowspan="2" style="width: 8.5%;">Faktor Risiko</th>
                    <th rowspan="2" style="width: 9%;">Hasil &amp; Diagnosa</th>
                    <th rowspan="2" style="width: 35%;">Tindak Lanjut &amp; Catatan Medis</th>
                </tr>
                <tr>
                    <th style="width: 7%;">TD (mmHg)</th>
                    <th style="width: 6.5%;">Gula Darah</th>
                    <th style="width: 6.5%;">Kolesterol</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatKunjungan as $rk)
                    @php
                        $isHipertensi = ($rk->sistole >= 140 || $rk->diastole >= 90);
                        $isDiabetes   = ($rk->gula_darah > 200);
                        $isKolesterol = ($rk->kolesterol > 200);
                        $tdText = ($rk->sistole && $rk->diastole) ? $rk->sistole . '/' . $rk->diastole : ($rk->tekanan_darah ?? '-');

                        // Faktor Risiko
                        $frList = [];
                        if (optional($rk->faktor_risiko)->merokok == 'Ya') $frList[] = 'Merokok';
                        if (optional($rk->faktor_risiko)->kurang_aktivitas == 'Ya') $frList[] = 'Kurang Olahraga';
                        if (optional($rk->faktor_risiko)->kurang_sayur_buah == 'Ya') $frList[] = 'Kurang Serat/Buah';
                        if (optional($rk->faktor_risiko)->konsumsi_alkohol == 'Ya') $frList[] = 'Alkohol';

                        // Tindak Lanjut
                        $tl = $rk->tindakLanjut;

                        // Format Jenis Tindak Lanjut yang Rapi (Menghilangkan snake_case)
                        $rawJenis = $tl->jenis_tindak_lanjut ?? ($tl->tindak_lanjut ?? '');
                        $formattedJenis = ucwords(str_replace('_', ' ', $rawJenis));
                        if (strtolower($rawJenis) === 'rujukan') {
                            $formattedJenis = 'Rujukan Faskes Lanjutan';
                        } elseif (strtolower($rawJenis) === 'anjuran_gaya_hidup') {
                            $formattedJenis = 'Anjuran Gaya Hidup Sehat';
                        }

                        // Format Status
                        $rawStatus = strtolower($tl->status_tindak_lanjut ?? '');
                        $statusBadge = '';
                        if ($rawStatus === 'sudah' || $rawStatus === 'selesai') {
                            $statusBadge = '<span style="color:#15803d; font-weight:bold;">(Selesai)</span>';
                        } elseif ($rawStatus === 'belum' || $rawStatus === 'proses') {
                            $statusBadge = '<span style="color:#b45309; font-weight:bold;">(Dalam Proses)</span>';
                        } elseif (!empty($rawStatus)) {
                            $statusBadge = '<span style="color:#475569;">(' . ucfirst($rawStatus) . ')</span>';
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($rk->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                        
                        {{-- Puskesmas / Faskes --}}
                        <td class="left">
                            <strong>{{ $rk->puskesmas->nama_puskesmas ?? ($peserta->puskesmas->nama_puskesmas ?? 'Puskesmas') }}</strong>
                        </td>

                        {{-- TD --}}
                        <td style="{{ $isHipertensi ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                            {{ $tdText }}
                        </td>

                        {{-- Gula Darah --}}
                        <td style="{{ $isDiabetes ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                            {{ $rk->gula_darah ? $rk->gula_darah . ' mg/dL' : '-' }}
                        </td>

                        {{-- Kolesterol --}}
                        <td style="{{ $isKolesterol ? 'color:#b91c1c; font-weight:bold;' : '' }}">
                            {{ $rk->kolesterol ? $rk->kolesterol . ' mg/dL' : '-' }}
                        </td>

                        {{-- IMT & LP --}}
                        <td>
                            {{ $rk->imt ?? '-' }}
                            @if($rk->lingkar_perut)
                                <br><small style="color:#64748b;">LP: {{ $rk->lingkar_perut }} cm</small>
                            @endif
                        </td>

                        {{-- Faktor Risiko --}}
                        <td class="left">
                            @if(count($frList) > 0)
                                {{ implode(', ', $frList) }}
                            @else
                                <span style="color:#15803d;">Tidak Ada</span>
                            @endif
                        </td>

                        {{-- Diagnosa & Hasil --}}
                        <td class="left">
                            @if($rk->diagnosa_penyakit)
                                <strong style="color:#b91c1c;">{{ $rk->diagnosa_penyakit }}</strong>
                            @else
                                <span style="color:#15803d;">{{ $rk->hasil_skrining ?? 'Normal' }}</span>
                            @endif
                        </td>

                        {{-- Tindak Lanjut & Catatan Medis --}}
                        <td class="left">
                            @if($tl)
                                <div>
                                    <strong>{{ $formattedJenis ?: 'Konseling Medis' }}</strong> {!! $statusBadge !!}
                                </div>
                                @if(!empty($tl->catatan_petugas ?? $tl->keterangan))
                                    <div style="margin-top:2px; font-size:9.5px; color:#334155;">
                                        <strong>Catatan:</strong> {{ $tl->catatan_petugas ?? $tl->keterangan }}
                                    </div>
                                @endif
                                @if(!empty($tl->rujukan_ke))
                                    <div style="margin-top:2px; font-size:9.5px; color:#b91c1c; font-weight:bold;">
                                        🏥 Rujukan Ke: {{ $tl->rujukan_ke }}
                                    </div>
                                @endif
                            @else
                                <span style="color:#94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="padding: 15px; color: #888;">
                            Belum ada catatan riwayat pemeriksaan medis untuk pasien ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN & PENGESAHAN PETUGAS PEMERIKSA --}}
        <div class="ttd">
            <div class="block">
                <div>DIKELUARKAN DI BANJARMASIN</div>
                <div>TANGGAL: {{ now()->format('d-m-Y') }}</div>

                <div style="margin-top:8px; font-weight: bold; text-transform: uppercase;">
                    PETUGAS PEMERIKSA PTM<br>
                    
                </div>

                <div style="height: 55px;"></div>

                <div class="name">
                    {{ $petugasPemeriksa->nama_pegawai ?? (Auth::user()->nama ?? 'Petugas Pemeriksa PTM') }}
                </div>
                <div style="margin-top:2px;">
                    NIP. {{ $petugasPemeriksa->nip ?? '-' }}
                </div>
            </div>
        </div>

    </div>
</body>

</html>
