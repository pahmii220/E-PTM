<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Puskesmas;
use App\Models\Petugas;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\TindakLanjutPTM;
use Illuminate\Support\Facades\DB;

echo "=== Memulai Regenerasi Data PTM (Kategorisasi Diagnosa Medis Sesuai Hasil Skrining) ===\n";

// Hapus data July 2026 sebelumnya
$julyPesertaIds = Peserta::whereBetween('dibuat_pada', ['2026-07-01 00:00:00', '2026-07-31 23:59:59'])->pluck('id');
echo "Menghapus " . count($julyPesertaIds) . " data pasien Juli 2026 sebelumnya...\n";

DB::beginTransaction();

try {
    if (count($julyPesertaIds) > 0) {
        TindakLanjutPTM::whereIn('peserta_id', $julyPesertaIds)->delete();
        FaktorResikoPTM::whereIn('peserta_id', $julyPesertaIds)->delete();
        DeteksiDiniPTM::whereIn('peserta_id', $julyPesertaIds)->delete();
        Peserta::whereIn('id', $julyPesertaIds)->delete();
    }

    $maleFirstNames = [
        'Agus', 'Budi', 'Bambang', 'Dedi', 'Eko', 'Fajar', 'Hendra', 'Irfan', 'Joko', 'Kurniawan',
        'Lukman', 'Muhammad', 'Nugroho', 'Oki', 'Prasetyo', 'Rahmat', 'Rizky', 'Surya', 'Taufik', 'Wahyu',
        'Aditya', 'Bayu', 'Candra', 'Dimas', 'Fery', 'Gilang', 'Herman', 'Indra', 'Jaya', 'Khairul',
        'Lutfi', 'Maulana', 'Nanda', 'Pratama', 'Rendy', 'Satria', 'Tri', 'Utomo', 'Yudi', 'Zainal',
        'Aris', 'Bagas', 'Danang', 'Faris', 'Hafiz', 'Iqbal', 'Kiki', 'Latif', 'Mustofa', 'Nizar'
    ];

    $femaleFirstNames = [
        'Ani', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Indah', 'Kartika', 'Lestari',
        'Maya', 'Nirmala', 'Novi', 'Putri', 'Ratna', 'Siti', 'Tri', 'Utami', 'Wulandari', 'Yuni',
        'Ayu', 'Dian', 'Eva', 'Hani', 'Intan', 'Lia', 'Megawati', 'Nabila', 'Rina', 'Sari',
        'Tania', 'Vina', 'Widia', 'Yulia', 'Zahra', 'Rini', 'Dina', 'Tuti', 'Endang', 'Sri',
        'Alya', 'Bella', 'Chintya', 'Desi', 'Elsa', 'Farida', 'Hana', 'Ika', 'Juli', 'Kiki'
    ];

    $lastNames = [
        'Santoso', 'Wijaya', 'Saputra', 'Hidayat', 'Kusuma', 'Nugraha', 'Pratama', 'Setiawan', 'Ramadhan', 'Lestari',
        'Suryadi', 'Permana', 'Utama', 'Hadi', 'Wibowo', 'Prasetya', 'Nusantara', 'Firmansyah', 'Ginting', 'Siregar',
        'Harahap', 'Nasution', 'Simanjuntak', 'Subakti', 'Kurnia', 'Purnama', 'Syahputra', 'Bachtiar', 'Fachri', 'Gunawan',
        'Hermawan', 'Irawan', 'Kusnadi', 'Mahendra', 'Octavian', 'Riyadi', 'Sukarno', 'Tanjung', 'Wicaksono', 'Zulkarnaen'
    ];

    $streetNames = [
        'Jln. Ahmad Yani', 'Jln. Veteran', 'Jln. S. Parman', 'Jln. Gatot Subroto', 'Jln. Pangeran Antasari',
        'Jln. Lambung Mangkurat', 'Jln. Hasan Basri', 'Jln. Sutoyo S', 'Jln. Mayjend Sutoyo', 'Jln. Pramuka',
        'Jln. Perintis Kemerdekaan', 'Jln. Belitung Darat', 'Jln. RK Ilir', 'Jln. Kelayan A', 'Jln. Kelayan B',
        'Jln. Pekapuran Raya', 'Jln. Beruntung Jaya', 'Jln. Sungai Andai', 'Jln. Kuin Utara', 'Jln. Kuin Selatan'
    ];

    $subDistricts = [
        'Banjarmasin Tengah', 'Banjarmasin Utara', 'Banjarmasin Selatan', 'Banjarmasin Timur', 'Banjarmasin Barat'
    ];

    $jobs = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Ibu Rumah Tangga', 'Pedagang', 'Pensiunan'];

    // KATEGORISASI PENYAKIT SESUAI LOGIKA MEDIS SKRINING
    // 1. Dicurigai PTM (Fase Awal / Predisposisi)
    $dicurigaiDiseases = [
        'Pre-Hipertensi',
        'Prediabetes',
        'Obesitas',
        'Miopia',
        'Gangguan Penglihatan Katarak',
        'Gangguan Pendengaran'
    ];

    // 2. Risiko Tinggi (Penyakit Kronis / Berat)
    $risikoTinggiDiseasesGeneral = [
        'Hipertensi',
        'Diabetes Melitus',
        'Jantung Koroner',
        'Gagal Jantung',
        'Gangguan Jantung',
        'Gangguan Stroke',
        'PPOK Umum',
        'Kanker Paru-Paru',
        'Kanker Kolorektal',
        'Thalassemia'
    ];

    $risikoTinggiDiseasesFemaleOnly = [
        'Kanker Payudara',
        'Kanker Serviks'
    ];

    $usedNames = [];
    $usedNIKs = [];

    function generateUniqueName($gender, &$usedNames, $maleFirstNames, $femaleFirstNames, $lastNames) {
        do {
            $first = $gender === 'Laki-laki' 
                ? $maleFirstNames[array_rand($maleFirstNames)] 
                : $femaleFirstNames[array_rand($femaleFirstNames)];
            $last = $lastNames[array_rand($lastNames)];
            $name = "$first $last";
        } while (in_array($name, $usedNames));
        
        $usedNames[] = $name;
        return $name;
    }

    function generateUniqueNIK(&$usedNIKs) {
        do {
            $nik = '6371' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (in_array($nik, $usedNIKs));
        
        $usedNIKs[] = $nik;
        return $nik;
    }

    $puskesmasList = Puskesmas::all();
    $totalPatientsInserted = 0;
    $totalNormal = 0;
    $totalDicurigai = 0;
    $totalRisikoTinggi = 0;

    foreach ($puskesmasList as $index => $puskesmas) {
        $petugas = Petugas::where('puskesmas_id', $puskesmas->id)->first();
        if (!$petugas) {
            $petugas = Petugas::first();
        }

        $targetCount = rand(2, 13);
        $num = $index + 1;
        echo "[$num/27] Processing Puskesmas ID {$puskesmas->id}: {$puskesmas->nama_puskesmas} ($targetCount data)...\n";

        for ($i = 0; $i < $targetCount; $i++) {
            $jk = rand(0, 1) === 1 ? 'Laki-laki' : 'Perempuan';
            $nama = generateUniqueName($jk, $usedNames, $maleFirstNames, $femaleFirstNames, $lastNames);
            $nik = generateUniqueNIK($usedNIKs);
            $noHp = '08' . rand(11, 89) . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $alamat = $streetNames[array_rand($streetNames)] . ' No. ' . rand(1, 150);
            $kecamatan = $subDistricts[array_rand($subDistricts)];
            
            $birthYear = rand(1955, 2002);
            $birthMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
            $birthDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
            $tglLahir = "$birthYear-$birthMonth-$birthDay";

            $dayJuly = str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT);
            $tglPemeriksaan = "2026-07-$dayJuly";
            $createdTime = "$tglPemeriksaan " . str_pad(rand(8, 16), 2, '0', STR_PAD_LEFT) . ":" . str_pad(rand(10, 59), 2, '0', STR_PAD_LEFT) . ":00";

            $pekerjaanList = $jobs;
            if ($jk === 'Laki-laki') {
                $pekerjaanList = array_diff($pekerjaanList, ['Ibu Rumah Tangga']);
            }
            $pekerjaan = $pekerjaanList[array_rand($pekerjaanList)];

            // 1. Simpan Peserta
            $peserta = Peserta::create([
                'puskesmas_id'      => $puskesmas->id,
                'nik'               => $nik,
                'nama_lengkap'      => $nama,
                'no_rekam_medis'    => 'RM-202607-' . rand(10000, 99999),
                'tempat_lahir'      => 'Banjarmasin',
                'tanggal_lahir'     => $tglLahir,
                'jenis_kelamin'     => $jk,
                'pekerjaan'         => $pekerjaan,
                'alamat'            => $alamat,
                'kecamatan'         => $kecamatan,
                'kontak'            => $noHp,
                'status_verifikasi' => 'approved',
                'catatan_verifikasi'=> 'Otomatis disetujui oleh sistem',
                'diverifikasi_oleh' => $petugas->user_id ?? 1,
                'diverifikasi_pada'=> $createdTime,
                'dibuat_pada'       => $createdTime,
                'diubah_pada'       => $createdTime,
            ]);

            // 2. PROPORSI & KATEGORISASI SKRINING MEDIS KETAT
            $catRoll = rand(1, 100);

            if ($catRoll <= 40) {
                // NORMAL (40%)
                $sistole = rand(110, 124);
                $diastole = rand(70, 80);
                $gulaDarah = rand(80, 115);
                $kolesterol = rand(145, 185);
                $tb = rand(152, 176);
                $bb = rand(50, 68);
                $imt = round($bb / (($tb/100) * ($tb/100)), 1);

                $hasilSkrining = 'Normal';
                $diagnosaStr = 'Normal';

                $jenisTL = rand(0, 1) === 1 ? 'edukasi' : 'anjuran_gaya_hidup';
                $catatanTL = "Hasil skrining normal. Pasien disarankan mempertahankan pola hidup sehat dan rutin olahraga 30 menit sehari.";
                $totalNormal++;

            } elseif ($catRoll <= 70) {
                // DICURIGAI PTM (30%) -> HANYA DIAGNOSA FASE AWAL / DICURIGAI (Pre-Hipertensi, Prediabetes, Obesitas, Gangguan Penglihatan/Pendengaran)
                $sistole = rand(126, 138);
                $diastole = rand(81, 88);
                $gulaDarah = rand(118, 160);
                $kolesterol = rand(186, 215);
                $tb = rand(150, 175);
                $bb = rand(58, 85);
                $imt = round($bb / (($tb/100) * ($tb/100)), 1);

                $hasilSkrining = 'Dicurigai PTM';
                $diagnosaStr = $dicurigaiDiseases[array_rand($dicurigaiDiseases)];

                $jenisTL = rand(0, 1) === 1 ? 'monitoring' : 'anjuran_gaya_hidup';
                $catatanTL = "Dicurigai terdapat potensi PTM ($diagnosaStr). Pasien disarankan kontrol ulang bulan depan serta konsumsi makanan sehat.";
                $totalDicurigai++;

            } else {
                // RISIKO TINGGI (30%) -> PENYAKIT KRONIS BERAT (Hipertensi, Diabetes Melitus, Gagal Jantung, Jantung Koroner, Stroke, Kanker, dsb.)
                $sistole = rand(142, 175);
                $diastole = rand(90, 105);
                $gulaDarah = rand(185, 240);
                $kolesterol = rand(218, 260);
                $tb = rand(150, 175);
                $bb = rand(65, 95);
                $imt = round($bb / (($tb/100) * ($tb/100)), 1);

                $hasilSkrining = 'Risiko Tinggi';
                
                $pool = $risikoTinggiDiseasesGeneral;
                if ($jk === 'Perempuan') {
                    $pool = array_merge($pool, $risikoTinggiDiseasesFemaleOnly);
                }
                
                $numPenyakit = rand(1, 2);
                $selectedKeys = (array) array_rand($pool, $numPenyakit);
                $selectedDiseases = [];
                foreach ($selectedKeys as $k) {
                    $selectedDiseases[] = $pool[$k];
                }
                
                $diagnosaStr = implode(', ', array_unique($selectedDiseases));

                $jenisTL = rand(0, 1) === 1 ? 'rujukan' : 'monitoring';
                $catatanTL = "Pasien berisiko tinggi PTM ($diagnosaStr). Pasien dirujuk ke FKTP/Poli Spesialis untuk pengobatan dan perawatan medis.";
                $totalRisikoTinggi++;
            }

            // Simpan Deteksi Dini
            $deteksi = DeteksiDiniPTM::create([
                'peserta_id'          => $peserta->id,
                'petugas_id'          => $petugas->id,
                'puskesmas_id'        => $puskesmas->id,
                'tanggal_pemeriksaan' => $tglPemeriksaan,
                'tekanan_darah'       => "$sistole/$diastole",
                'gula_darah'          => $gulaDarah,
                'kolesterol'          => $kolesterol,
                'berat_badan'         => $bb,
                'tinggi_badan'        => $tb,
                'imt'                 => $imt,
                'hasil_skrining'      => $hasilSkrining,
                'diagnosa_penyakit'   => $diagnosaStr,
                'status_verifikasi'   => 'approved',
                'catatan_verifikasi'  => 'Otomatis disetujui oleh sistem',
                'diverifikasi_oleh'   => $petugas->user_id ?? 1,
                'diverifikasi_pada'   => $createdTime,
                'dibuat_pada'         => $createdTime,
                'diubah_pada'         => $createdTime,
            ]);

            // Simpan Faktor Risiko
            $merokok = ($jk === 'Laki-laki' && rand(0, 10) > 4) ? 'Ya' : 'Tidak';
            $alkohol = 'Tidak';
            $riwayatKeluarga = rand(0, 10) > 6 ? 'Ya' : 'Tidak';
            $kurangAktivitas = rand(0, 10) > 5 ? 'Ya' : 'Tidak';

            FaktorResikoPTM::create([
                'peserta_id'             => $peserta->id,
                'puskesmas_id'           => $puskesmas->id,
                'tanggal_pemeriksaan'    => $tglPemeriksaan,
                'merokok'                => $merokok,
                'alkohol'                => $alkohol,
                'riwayat_keluarga'       => $riwayatKeluarga,
                'kurang_aktivitas_fisik' => $kurangAktivitas,
                'petugas_id'             => $petugas->user_id ?? 1,
                'status_verifikasi'      => 'approved',
                'catatan_verifikasi'     => 'Otomatis disetujui oleh sistem',
                'diverifikasi_oleh'      => $petugas->user_id ?? 1,
                'diverifikasi_pada'      => $createdTime,
                'dibuat_pada'            => $createdTime,
                'diubah_pada'            => $createdTime,
            ]);

            // Simpan Tindak Lanjut
            $tglTL = date('Y-m-d', strtotime($tglPemeriksaan . ' + ' . rand(0, 2) . ' days'));
            if ($tglTL > '2026-07-30') $tglTL = '2026-07-30';

            TindakLanjutPTM::create([
                'peserta_id'            => $peserta->id,
                'deteksi_dini_id'       => $deteksi->id,
                'petugas_id'            => $petugas->id,
                'jenis_tindak_lanjut'   => $jenisTL,
                'tanggal_tindak_lanjut' => $tglTL,
                'catatan_petugas'       => $catatanTL,
                'status_tindak_lanjut'  => 'sudah',
                'dibuat_pada'           => $createdTime,
                'diubah_pada'           => $createdTime,
            ]);

            $totalPatientsInserted++;
        }
    }

    DB::commit();
    echo "\n=== REGENERASI BERHASIL! ===\n";
    echo "Total Pasien Baru: $totalPatientsInserted\n";
    echo "Proporsi Hasil Skrining:\n";
    echo "- Normal: $totalNormal (" . round(($totalNormal / $totalPatientsInserted) * 100, 1) . "%)\n";
    echo "- Dicurigai PTM: $totalDicurigai (" . round(($totalDicurigai / $totalPatientsInserted) * 100, 1) . "%)\n";
    echo "- Risiko Tinggi: $totalRisikoTinggi (" . round(($totalRisikoTinggi / $totalPatientsInserted) * 100, 1) . "%)\n";
    echo "Penyesuaian Medis Ketat: Diagnosa penyakit kronis berat (Gagal Jantung, Stroke, Kanker, dll.) HANYA untuk status 'Risiko Tinggi'.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n[ERROR] Gagal mereset data: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
