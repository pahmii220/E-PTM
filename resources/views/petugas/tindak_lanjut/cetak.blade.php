<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Hasil Skrining PTM</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.5;
            font-size: 14px;
            margin: 0;
            padding: 10px 30px;
        }
        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            border-bottom: 3px solid black;
            margin-bottom: 2px;
            padding-bottom: 10px;
        }
        .kop-surat td {
            text-align: center;
            vertical-align: middle;
        }
        .kop-surat .logo {
            width: 15%;
        }
        .kop-surat .logo img {
            width: 75px;
            height: auto;
        }
        .kop-surat .teks {
            width: 70%;
            line-height: 1.2;
        }
        .kop-surat .teks h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .kop-surat .teks h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .kop-surat .teks h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-surat .teks p {
            margin: 3px 0 0;
            font-size: 12px;
        }
        .garis-bawah {
            border-top: 1px solid black;
            margin-bottom: 25px;
        }

        /* JUDUL SURAT */
        .judul-surat {
            text-align: center;
            margin-bottom: 25px;
        }
        .judul-surat h3 {
            margin: 0;
            font-size: 16px;
            text-decoration: underline;
        }
        .judul-surat p {
            margin: 2px 0 0;
            font-size: 14px;
        }

        /* ISI SURAT */
        .isi-surat {
            text-align: justify;
        }
        
        table.tabel-identitas {
            width: 100%;
            margin-left: 15px;
            margin-bottom: 15px;
        }
        table.tabel-identitas td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        table.tabel-hasil {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.tabel-hasil th, table.tabel-hasil td {
            border: 1px solid black;
            padding: 6px 10px;
        }
        table.tabel-hasil th {
            width: 40%;
            text-align: left;
            font-weight: normal;
        }
        table.tabel-hasil td {
            font-weight: bold;
        }

        /* TANDA TANGAN */
        .ttd-container {
            width: 100%;
            margin-top: 50px;
        }
        .ttd-box {
            float: right;
            width: 300px;
            text-align: left;
        }
        .ttd-box p {
            margin: 0;
        }
        .ttd-nama {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT PUSKESMAS -->
    <table class="kop-surat">
        <tr>
            <td class="logo">
                <img src="{{ asset('images/dinkes.png') }}" alt="Logo">
            </td>
            <td class="teks">
                <h1>{{ strtoupper($tindakLanjut->peserta->puskesmas->nama_puskesmas ?? 'UPTD PUSKESMAS') }}</h1>
                <p>{{ $tindakLanjut->peserta->puskesmas->alamat ?? 'Alamat belum diatur' }}</p>
            </td>
            <td class="logo">
                <!-- Kosong untuk menyeimbangkan -->
            </td>
        </tr>
    </table>
    <div style="border-top: 1px solid black; margin-top: 2px; margin-bottom: 20px;"></div>

    <!-- JUDUL SURAT -->
    <div class="judul-surat">
        <h3>Laporan Deteksi Dini Penyakit Tidak Menular (PTM)</h3>
    </div>

    <!-- ISI SURAT -->
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini, Petugas Pemeriksa pada {{ $tindakLanjut->peserta->puskesmas->nama_puskesmas ?? 'UPTD Puskesmas' }}, dengan ini menerangkan bahwa:</p>
        
        <table class="tabel-identitas">
            <tr>
                <td style="width: 30%;">Nama Lengkap</td>
                <td style="width: 3%;">:</td>
                <td><strong>{{ $tindakLanjut->peserta->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>Nomor Induk Kependudukan</td>
                <td>:</td>
                <td>{{ $tindakLanjut->peserta->nik }}</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $tindakLanjut->peserta->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>
                    {{ \Carbon\Carbon::parse($tindakLanjut->peserta->tanggal_lahir)->translatedFormat('d F Y') }} 
                    ({{ \Carbon\Carbon::parse($tindakLanjut->peserta->tanggal_lahir)->age }} Tahun)
                </td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $tindakLanjut->peserta->alamat ?? '-' }}</td>
            </tr>
        </table>

        <p>Telah melakukan pemeriksaan kesehatan / Skrining Penyakit Tidak Menular (PTM) pada tanggal <strong>{{ \Carbon\Carbon::parse($tindakLanjut->deteksiDini->tanggal_pemeriksaan)->translatedFormat('d F Y') }}</strong>, dengan hasil pemeriksaan klinis sebagai berikut:</p>

        <table class="tabel-hasil">
            <tr>
                <th>Tekanan Darah</th>
                <td>{{ $tindakLanjut->deteksiDini->tekanan_darah ?? '-' }} mmHg</td>
            </tr>
            <tr>
                <th>Gula Darah Sewaktu</th>
                <td>{{ $tindakLanjut->deteksiDini->gula_darah ?? '-' }} mg/dL</td>
            </tr>
            <tr>
                <th>Kolesterol Total</th>
                <td>{{ $tindakLanjut->deteksiDini->kolesterol ?? '-' }} mg/dL</td>
            </tr>
            <tr>
                <th>Indeks Massa Tubuh (IMT)</th>
                <td>
                    {{ $tindakLanjut->deteksiDini->imt ?? '-' }} 
                    @if($tindakLanjut->deteksiDini->imt >= 27) (Obesitas)
                    @elseif($tindakLanjut->deteksiDini->imt >= 25) (Overweight)
                    @else (Normal)
                    @endif
                </td>
            </tr>
            <tr>
                <th>Diagnosa Terdeteksi</th>
                <td style="color: #ef4444;">
                    {{ $tindakLanjut->deteksiDini->diagnosa_penyakit ?? 'Normal' }}
                </td>
            </tr>
        </table>

        <p><strong>Catatan Medis & Rekomendasi Tindak Lanjut:</strong><br>
        <span style="white-space: pre-line;">{{ $tindakLanjut->catatan_petugas ?? 'Pasien dalam kondisi sehat dan tidak memerlukan tindak lanjut khusus.' }}</span></p>

        <p>Demikian surat keterangan hasil skrining kesehatan ini dibuat dengan sebenarnya, untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-container clearfix">
        <div class="ttd-box">
            @php
$namaKabupaten = $tindakLanjut->peserta->puskesmas->nama_kabupaten ?? 'Banjarmasin';
// Membersihkan kata 'KOTA' atau 'KABUPATEN' jika ada
$namaKabupaten = str_replace(['KOTA ', 'KABUPATEN '], '', strtoupper($namaKabupaten));
$namaKotaBersih = ucwords(strtolower($namaKabupaten));
            @endphp
            <p>{{ $namaKotaBersih }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Petugas Pemeriksa PTM,</p>
            <br>
            <br>
                    

            <p class="ttd-nama">{{ Auth::user()->petugas->nama_pegawai ?? Auth::user()->name }}</p>
            <p>NIP. {{ Auth::user()->petugas->nip ?? '___________________' }}</p>
        </div>
    </div>

</body>
</html>
