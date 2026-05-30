<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan Dokumen</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 10px;
        }

        /* Desain Kop Surat */
        .kop-surat {
            border-bottom: 5px double #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-dinkes {
            width: 70px;
            height: auto;
        }

        .instansi-text {
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
            margin: 0;
        }

        .dinkes-text {
            font-size: 18px;
            text-transform: uppercase;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }

        .alamat-text {
            font-size: 11px;
            font-style: italic;
            margin: 5px 0 0 0;
            font-weight: normal;
        }

        /* Isi Dokumen */
        .judul-halaman {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
            text-decoration: underline;
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 40px;
            font-size: 13px;
        }

        .info-table td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .info-label {
            width: 25%;
            font-weight: bold;
        }

        .info-titik {
            width: 3%;
        }

        /* Bagian Tanda Tangan (TTD) */
        .ttd-container {
            width: 100%;
            margin-top: 50px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-waktu {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .ttd-jabatan {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .qr-code-img {
            margin-bottom: 15px;
        }

        .ttd-nama {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }

        .ttd-nip {
            font-size: 13px;
            margin: 0;
        }

        .catatan-kaki {
            margin-top: 60px;
            font-size: 10px;
            color: #555;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td style="width: 15%; text-align: center;">
                    {{-- Menggunakan path mentah agar dompdf lancar membaca logo lokal --}}
                    <img src="images/dinkes.png" class="logo-dinkes" alt="Logo">
                </td>
                <td style="width: 85%; text-align: center; padding-right: 40px;">
                    <p class="instansi-text">Pemerintah Provinsi / Kabupaten / Kota</p>
                    <p class="dinkes-text">Dinas Kesehatan</p>
                    <p class="alamat-text">Jl. Alamat Kantor Dinas Kesehatan No. XX, Telp: (0XX) XXXXXX, Email:
                        dinkes@mail.go.id</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="judul-halaman">
        LEMBAR PENGESAHAN DOKUMEN LAPORAN
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Jenis Berkas Laporan</td>
            <td class="info-titik">:</td>
            <td><strong>{{ $dokumen->jenis_laporan }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Periode Rekapitulasi</td>
            <td class="info-titik">:</td>
            <td>{{ $dokumen->bulan }} {{ $dokumen->tahun }}</td>
        </tr>
        <tr>
            <td class="info-label">Status Verifikasi Sistem</td>
            <td class="info-titik">:</td>
            <td>Selesai Diverifikasi & Dinyatakan Valid secara Digital</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Pengesahan</td>
            <td class="info-titik">:</td>
            <td>{{ \Carbon\Carbon::parse($dokumen->tanggal_pengesahan)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Metode Otentikasi</td>
            <td class="info-titik">:</td>
            <td>Tanda Tangan Elektronik (TTE) via QR Code Berbasis UUID Token</td>
        </tr>
    </table>

    <p style="font-size: 13px; text-align: justify;">
        Menyatakan bahwa data rekapitulasi Penyakit Tidak Menular (PTM) untuk jenis laporan dan periode yang tertera di
        atas telah selesai diteliti, diverifikasi berjenjang oleh tim verifikator dinas, dan disahkan secara resmi oleh
        Kepala Bidang yang berwenang. Lembar pengesahan ini berfungsi sebagai bukti sah tanpa memerlukan tanda tangan
        basah dan stempel fisik.
    </p>

    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: left;">
                    <div class="ttd-waktu">Disahkan pada:
                        {{ \Carbon\Carbon::parse($dokumen->tanggal_pengesahan)->translatedFormat('d F Y') }}</div>
                    <div class="ttd-jabatan">{{ $dokumen->kepalaP2ptm->jabatan ?? 'Kepala Bidang' }}</div>

                    <div class="qr-code-img">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" width="100" height="100">
                    </div>

                    <p class="ttd-nama">{{ $dokumen->kepalaP2ptm->nama_kepala }}</p>
                    <p class="ttd-nip">NIP. {{ $dokumen->kepalaP2ptm->nip }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="catatan-kaki">
        * Catatan Dokumen Elektronik: Dokumen ini diterbitkan secara sah melalui sistem pelaporan PTM terintegrasi.
        Keaslian berkas dapat dibuktikan dengan memindai (scan) QR Code di atas menggunakan kamera ponsel untuk mengarah
        ke tautan validasi server resmi Dinas Kesehatan.
    </div>

</body>

</html>