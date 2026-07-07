<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Skrining PTM</title>

    <style>
        /* ====== SETTING CETAK ====== */
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
            padding: 4px 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.grid th {
            background: #eee;
            font-weight: 700;
            text-align: center;
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

        {{-- TOMBOL PRINT (Sembunyi saat dicetak) --}}
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 12px; margin-right:6px; cursor:pointer;">Print</button>
            <button onclick="window.close()"
                style="padding:8px 12px; background:#eee; color:#000; border:1px solid #ccc; cursor:pointer;">Tutup</button>
        </div>

        {{-- KOP SURAT --}}
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

        {{-- JUDUL --}}
        <div style="width:100%; text-align:center; margin-bottom:15px;">
            <h3 style="margin:0; font-size:15px; letter-spacing:0.6px; font-weight:700;">
                LAPORAN REKAPITULASI HASIL SKRINING <br>
                PENYAKIT TIDAK MENULAR (PTM)
            </h3>
        </div>

        @php
            $totalPemeriksaan = $data->sum('jumlah');
        @endphp

        {{-- TABEL --}}
        <table class="grid">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Status Kesehatan</th>
                    <th style="width:120px">Jumlah Peserta</th>
                    <th style="width:90px">Persentase</th>
                    <th>Rekomendasi Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $row)
                    @php
                        $persen = $totalPemeriksaan > 0 ? round(($row->jumlah / $totalPemeriksaan) * 100, 1) : 0;
                        
                        $statusLower = strtolower($row->hasil_skrining);
                        $rekomendasi = 'Edukasi hidup sehat dan pemeriksaan berkala';
                        if ($statusLower === 'normal') {
                            $rekomendasi = 'Edukasi pola hidup sehat dan skrining berkala 1 tahun sekali';
                        } elseif ($statusLower === 'risiko tinggi') {
                            $rekomendasi = 'Rujukan ke Puskesmas/Rumah Sakit untuk penanganan medis';
                        } elseif (str_contains($statusLower, 'risiko') || str_contains($statusLower, 'berisiko')) {
                            $rekomendasi = 'Konseling faktor risiko dan pemeriksaan rutin 3-6 bulan sekali';
                        }
                    @endphp
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td style="text-align:center">{{ $row->hasil_skrining }}</td>
                        <td style="text-align:center">{{ $row->jumlah }} Orang</td>
                        <td style="text-align:center">{{ $persen }}%</td>
                        <td style="padding: 4px 10px;">{{ $rekomendasi }}</td>
                    </tr>
                @endforeach
                {{-- BARIS TOTAL --}}
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td colspan="2" style="text-align:right; padding-right: 10px;">Total Keseluruhan</td>
                    <td style="text-align:center;">{{ $totalPemeriksaan }} Orang</td>
                    <td style="text-align:center;">100%</td>
                    <td style="padding: 4px 10px;">-</td>
                </tr>
            </tbody>
        </table>

        {{-- TTD & QR CODE (Sudah Disinkronkan Sesuai Hak Akses Role) --}}
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

                    <div class="qr-container">
                        @if(isset($qrToken))
                            @php
                                // Laporan ini bersifat rekap, jadi kita beri judul yang sesuai
                                $judul = 'Laporan Rekap Skrining PTM';
                                $periode = 'Data Keseluruhan';
                                $tanggalSah = now()->setTimezone('Asia/Makassar')->format('d-m-Y H:i');

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