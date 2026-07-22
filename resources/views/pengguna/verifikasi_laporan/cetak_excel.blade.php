<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

@php
    $bulanIndo = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
    $namaBulan = $bulanIndo[\Carbon\Carbon::parse($startDate)->format('m')] . ' ' . \Carbon\Carbon::parse($startDate)->format('Y');

    // Inisialisasi total keseluruhan
    $totalSemuaPasien = 0;
    $totalSemuaRemaja = 0;
    $totalSemuaDewasa = 0;
    $totalSemuaPraLansia = 0;
    $totalSemuaLansia = 0;
    $totalPenyakitGlobal = array_fill_keys($penyakitList, 0);
@endphp

<table>
    <tr>
        <th colspan="{{ count($penyakitList) + 3 }}" style="font-size: 16px; font-weight: bold; text-align: center;">
            LAPORAN STATISTIK PEMERIKSAAN PTM<br>
            @if($kecFilter)
                KECAMATAN {{ strtoupper($kecFilter) }}
            @elseif($kotaFilter)
                KOTA/KABUPATEN {{ strtoupper($kotaFilter) }}
            @else
                PROVINSI KALIMANTAN SELATAN
            @endif
        </th>
    </tr>
    <tr>
        <th colspan="{{ count($penyakitList) + 3 }}" style="text-align: center;">Periode: {{ $namaBulan }}</th>
    </tr>
    <tr></tr> <!-- baris kosong untuk jarak -->
    
    <tr>
        <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #d9edf7; text-align: center; vertical-align: middle;">No</th>
        <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #d9edf7; vertical-align: middle;">Wilayah Puskesmas</th>
        <th colspan="{{ count($penyakitList) }}" style="border: 1px solid #000; font-weight: bold; background-color: #fcf8e3; text-align: center;">Berdasarkan Jenis Penyakit Terdeteksi</th>
        <th rowspan="2" style="border: 1px solid #000; font-weight: bold; background-color: #d9edf7; text-align: center; vertical-align: middle;">Total Pasien</th>
    </tr>
    <tr>
        @foreach($penyakitList as $penyakit)
            <th style="border: 1px solid #000; font-weight: bold; background-color: #fcf8e3; text-align: center;">{{ $penyakit }}</th>
        @endforeach
    </tr>

    @if($matriksLaporan->count() == 0)
        <tr>
            <td colspan="{{ count($penyakitList) + 3 }}" style="border: 1px solid #000; text-align: center;">Tidak ada data pada periode ini</td>
        </tr>
    @else
        @foreach($matriksLaporan as $no => $pkm)
        @php
            $totalSemuaPasien += $pkm['total_pasien'];
            $totalSemuaRemaja += $pkm['remaja'];
            $totalSemuaDewasa += $pkm['dewasa'];
            $totalSemuaPraLansia += $pkm['pra_lansia'];
            $totalSemuaLansia += $pkm['lansia'];
            foreach($penyakitList as $p) {
                $totalPenyakitGlobal[$p] += $pkm['ptm'][$p];
            }
        @endphp
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $no + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $pkm['puskesmas'] }}</td>
            @foreach($penyakitList as $penyakit)
                <td style="border: 1px solid #000; text-align: center;">{{ $pkm['ptm'][$penyakit] }}</td>
            @endforeach
            <td style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #f4f4f4;">{{ $pkm['total_pasien'] }}</td>
        </tr>
        @endforeach
        
        <!-- BARIS TOTAL KESELURUHAN -->
        <tr>
            <td colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: right; padding-right: 10px;">TOTAL KESELURUHAN :</td>
            @foreach($penyakitList as $penyakit)
                <td style="border: 1px solid #000; font-weight: bold; text-align: center;">{{ $totalPenyakitGlobal[$penyakit] }}</td>
            @endforeach
            <td style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #e2e3e5;">{{ $totalSemuaPasien }}</td>
        </tr>
    @endif
</table>

</body>
</html>
