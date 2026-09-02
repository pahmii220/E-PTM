<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puskesmas;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\TindakLanjutPTM;
use App\Models\Petugas;
use App\Models\User;

class AugustDummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Hapus data dummy Agustus 2026 sebelumnya agar bersih & tidak ada nama berulang
        $augustPesertaIds = Peserta::whereBetween('dibuat_pada', ['2026-08-01 00:00:00', '2026-08-31 23:59:59'])->pluck('id');
        if ($augustPesertaIds->isNotEmpty()) {
            TindakLanjutPTM::whereIn('peserta_id', $augustPesertaIds)->delete();
            FaktorResikoPTM::whereIn('peserta_id', $augustPesertaIds)->delete();
            DeteksiDiniPTM::whereIn('peserta_id', $augustPesertaIds)->delete();
            Peserta::whereIn('id', $augustPesertaIds)->delete();
        }

        $maleFirstNames = [
            'Budi', 'Rahmat', 'Agus', 'Heri', 'Eko', 'Bambang', 'Aris', 'Dedi', 'Rizal', 'Ahmad',
            'Bagas', 'Dharma', 'Dimas', 'Fajar', 'Hendra', 'Indra', 'Joko', 'Lukman', 'Muhammad', 'Nur',
            'Surya', 'Taufik', 'Yudi', 'Zainal', 'Aditya', 'Amir', 'Anton', 'Bayu', 'Candra', 'Danang',
            'Dwi', 'Farhan', 'Guntur', 'Hadi', 'Iwan', 'Johan', 'Krisna', 'Maulana', 'Nanda', 'Panji',
            'Ridwan', 'Teguh', 'Umar', 'Wahyu', 'Yosua', 'Arief', 'Bagus', 'Cahyo', 'Denny', 'Feri'
        ];

        $maleLastNames = [
            'Santoso', 'Hidayat', 'Setiawan', 'Gunawan', 'Prasetyo', 'Kurniawan', 'Nugroho', 'Supriadi',
            'Firmansyah', 'Fauzi', 'Saputra', 'Pratama', 'Wahyudi', 'Ramadhan', 'Wijaya', 'Kusuma',
            'Susilo', 'Hakim', 'Syahril', 'Permana', 'Abidin', 'Nugraha', 'Suhendra', 'Wibowo',
            'Sucipto', 'Arifin', 'Asmara', 'Kamil', 'Faruq', 'Haryanto', 'Subagyo', 'Wicaksono'
        ];

        $femaleFirstNames = [
            'Siti', 'Dewi', 'Sri', 'Nurul', 'Endang', 'Fitri', 'Ratna', 'Dwi', 'Rina', 'Anita',
            'Yulia', 'Maya', 'Tia', 'Rini', 'Wati', 'Novi', 'Lilis', 'Sari', 'Nia', 'Dian',
            'Utami', 'Kartika', 'Melati', 'Mega', 'Rahma', 'Ayu', 'Bunga', 'Citra', 'Desi', 'Elsa',
            'Hani', 'Indah', 'Juli', 'Kusuma', 'Laras', 'Marlina', 'Ningsih', 'Putri', 'Reni', 'Tari',
            'Vina', 'Wulan', 'Yuni', 'Zahra', 'Amalia', 'Bella', 'Chintya', 'Dina', 'Eka', 'Gita'
        ];

        $femaleLastNames = [
            'Aminah', 'Lestari', 'Wahyuni', 'Hidayah', 'Rahayu', 'Sari', 'Astuti', 'Kartika',
            'Rahmawati', 'Indah', 'Puspita', 'Anggraini', 'Kurnia', 'Saputri', 'Karlina', 'Permata',
            'Ramadhani', 'Putri', 'Kurniasari', 'Wulandari', 'Pratiwi', 'Puspasari', 'Handayani',
            'Mulia', 'Nurhayati', 'Sulastri', 'Suciati', 'Damayanti', 'Maharani', 'Kusumawati'
        ];

        $occupations = [
            'Wiraswasta', 'Karyawan Swasta', 'Ibu Rumah Tangga', 'Buruh Harian',
            'Pedagang', 'Pegawai Negeri', 'Petani', 'Pengemudi'
        ];

        $streetNames = [
            'Jln. Ahmad Yani', 'Jln. Veteran', 'Jln. Sutoyo S', 'Jln. Pekapuran Raya',
            'Jln. Pramuka', 'Jln. Belitung Laut', 'Jln. S. Parman', 'Jln. Mulawarman',
            'Jln. Hasan Basri', 'Jln. Gatot Subroto', 'Jln. K.S. Tubun'
        ];

        $allPuskesmas = Puskesmas::all();

        if ($allPuskesmas->isEmpty()) {
            $this->command->error("Tidak ada data Puskesmas di database.");
            return;
        }

        $defaultPetugasUser = User::where('role_name', 'petugas')->first() ?: User::first();

        // Buat kombinasi nama Unik
        $usedNames = [];

        foreach ($allPuskesmas as $pkm) {
            $isTerminal = str_contains(strtolower($pkm->nama_puskesmas), 'terminal');

            // 2 sampai 5 pasien per Puskesmas
            $numPatients = rand(2, 5);

            for ($i = 0; $i < $numPatients; $i++) {
                $isMale = (rand(0, 1) === 1);
                
                // Cari nama unik yang belum dipakai
                do {
                    if ($isMale) {
                        $f = $maleFirstNames[array_rand($maleFirstNames)];
                        $l = $maleLastNames[array_rand($maleLastNames)];
                        $nama = "{$f} {$l}";
                        $gender = 'Laki-laki';
                    } else {
                        $f = $femaleFirstNames[array_rand($femaleFirstNames)];
                        $l = $femaleLastNames[array_rand($femaleLastNames)];
                        $nama = "{$f} {$l}";
                        $gender = 'Perempuan';
                    }
                } while (in_array($nama, $usedNames));

                $usedNames[] = $nama;

                // Random NIK 16 digit unik
                do {
                    $nik = '6371' . str_pad((string)rand(1000000000, 9999999999), 12, '0', STR_PAD_LEFT);
                } while (Peserta::where('nik', $nik)->exists());

                // Random tanggal lahir (umur 22 - 65)
                $birthYear = rand(1961, 2003);
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);
                $tanggalLahir = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

                // Short prefix RM
                $prefix = $pkm->short_prefix ? $pkm->short_prefix . '/' : 'Pk-00' . $pkm->id . '/';
                do {
                    $noRm = $prefix . rand(10000, 99999);
                } while (Peserta::where('no_rekam_medis', $noRm)->exists());

                // Tanggal periksa 1 - 25 Agustus 2026
                $dayPeriksa = rand(1, 25);
                $tglPeriksaStr = sprintf('2026-08-%02d', $dayPeriksa);
                $createdTimestamp = sprintf('2026-08-%02d %02d:%02d:%02d', $dayPeriksa, rand(8, 16), rand(10, 59), rand(10, 59));

                $pekerjaan = ($gender === 'Perempuan' && rand(0, 1) === 1) ? 'Ibu Rumah Tangga' : $occupations[array_rand($occupations)];
                $alamat = $streetNames[array_rand($streetNames)] . ' No. ' . rand(1, 120) . ' RT. ' . rand(1, 20);
                $kontak = '08' . rand(11, 89) . rand(1000000, 9999999);

                // Petugas User ID di pengguna table & Petugas ID di petugas table
                $petugasObj = Petugas::where('puskesmas_id', $pkm->id)->first();
                $petugasUserId = $petugasObj ? $petugasObj->user_id : $defaultPetugasUser->id;
                $petugasId = $petugasObj ? $petugasObj->id : null;

                // Status verifikasi: approved untuk 26 puskesmas, draft untuk Puskesmas Terminal
                $statusVerif = $isTerminal ? 'draft' : 'approved';

                // Create Peserta
                $peserta = Peserta::create([
                    'puskesmas_id'      => $pkm->id,
                    'nik'               => $nik,
                    'nama_lengkap'      => $nama,
                    'no_rekam_medis'    => $noRm,
                    'tempat_lahir'      => 'Banjarmasin',
                    'tanggal_lahir'     => $tanggalLahir,
                    'jenis_kelamin'     => $gender,
                    'pekerjaan'         => $pekerjaan,
                    'alamat'            => $alamat,
                    'kecamatan'         => $pkm->kecamatan ?: 'Banjarmasin',
                    'kontak'            => $kontak,
                    'status_verifikasi' => 'approved',
                    'dibuat_pada'       => $createdTimestamp,
                    'diubah_pada'       => $createdTimestamp,
                ]);

                // Generasi Kondisi Klinis Medis
                $kondisiTipe = rand(1, 5);
                if ($kondisiTipe === 1) { // Normal
                    $sbp = rand(110, 122);
                    $dbp = rand(70, 80);
                    $gula = rand(85, 110);
                    $kol = rand(150, 180);
                    $bb = rand(50, 68);
                    $tb = rand(158, 172);
                    $diagnosa = 'Normal / Sehat';
                    $hasilSkrining = 'Normal';
                } elseif ($kondisiTipe === 2) { // Pre-Hipertensi / Prediabetes
                    $sbp = rand(126, 138);
                    $dbp = rand(82, 88);
                    $gula = rand(120, 155);
                    $kol = rand(186, 205);
                    $bb = rand(65, 78);
                    $tb = rand(160, 170);
                    $diagnosa = rand(0, 1) ? 'Pre-Hipertensi' : 'Prediabetes';
                    $hasilSkrining = 'Dicurigai PTM';
                } elseif ($kondisiTipe === 3) { // Hipertensi
                    $sbp = rand(142, 168);
                    $dbp = rand(92, 104);
                    $gula = rand(110, 160);
                    $kol = rand(190, 230);
                    $bb = rand(68, 85);
                    $tb = rand(155, 170);
                    $diagnosa = 'Hipertensi';
                    $hasilSkrining = 'Risiko Tinggi';
                } elseif ($kondisiTipe === 4) { // Diabetes Melitus
                    $sbp = rand(130, 145);
                    $dbp = rand(85, 92);
                    $gula = rand(205, 270);
                    $kol = rand(200, 245);
                    $bb = rand(62, 80);
                    $tb = rand(156, 172);
                    $diagnosa = 'Diabetes Melitus';
                    $hasilSkrining = 'Risiko Tinggi';
                } else { // Gangguan Penglihatan / Pendengaran / Obesitas
                    $sbp = rand(120, 135);
                    $dbp = rand(78, 85);
                    $gula = rand(95, 140);
                    $kol = rand(175, 210);
                    $bb = rand(75, 92);
                    $tb = rand(155, 168);
                    $diagnosa = rand(0, 1) ? 'Gangguan Pendengaran' : 'Miopia';
                    $hasilSkrining = 'Dicurigai PTM';
                }

                $imt = round($bb / pow($tb / 100, 2), 2);
                $tekananDarah = "{$sbp}/{$dbp}";

                // Create Deteksi Dini PTM
                $deteksi = DeteksiDiniPTM::create([
                    'peserta_id'          => $peserta->id,
                    'petugas_id'          => $petugasUserId,
                    'puskesmas_id'        => $pkm->id,
                    'tanggal_pemeriksaan' => $tglPeriksaStr,
                    'tekanan_darah'       => $tekananDarah,
                    'gula_darah'          => $gula,
                    'kolesterol'          => $kol,
                    'berat_badan'         => $bb,
                    'tinggi_badan'        => $tb,
                    'imt'                 => $imt,
                    'hasil_skrining'      => $hasilSkrining,
                    'diagnosa_penyakit'   => $diagnosa,
                    'status_verifikasi'   => $statusVerif,
                    'catatan_verifikasi'  => $statusVerif === 'approved' ? 'Dikirim dan disetujui secara otomatis oleh Sistem.' : null,
                    'diverifikasi_pada'   => $statusVerif === 'approved' ? $createdTimestamp : null,
                    'dibuat_pada'         => $createdTimestamp,
                    'diubah_pada'         => $createdTimestamp,
                ]);

                // Create Faktor Resiko PTM
                FaktorResikoPTM::create([
                    'peserta_id'          => $peserta->id,
                    'petugas_id'          => $petugasUserId,
                    'puskesmas_id'        => $pkm->id,
                    'tanggal_pemeriksaan' => $tglPeriksaStr,
                    'merokok'             => rand(0, 1) ? 'Ya' : 'Tidak',
                    'alkohol'             => 'Tidak',
                    'riwayat_keluarga'    => rand(0, 1) ? 'Ya' : 'Tidak',
                    'kurang_aktivitas_fisik' => rand(0, 1) ? 'Ya' : 'Tidak',
                    'status_verifikasi'   => $statusVerif,
                    'diverifikasi_pada'   => $statusVerif === 'approved' ? $createdTimestamp : null,
                    'dibuat_pada'         => $createdTimestamp,
                    'diubah_pada'         => $createdTimestamp,
                ]);

                // Tanggal Tindak Lanjut: HARUS SETELAH tanggal periksa (2 - 5 hari setelahnya)
                $dayTindakLanjut = min(28, $dayPeriksa + rand(2, 5));
                $tglTindakLanjutStr = sprintf('2026-08-%02d', $dayTindakLanjut);

                // Opsi Jenis Tindak Lanjut (sesuai Enum: edukasi, anjuran_gaya_hidup, rujukan, monitoring, tidak_ada)
                if (str_contains($diagnosa, 'Hipertensi')) {
                    $jenisTl = 'rujukan';
                    $catatanTl = 'Pasien diberikan obat Amlodipin 5mg 1x1. Dianjurkan mengurangi konsumsi garam dan kontrol tensi 2 minggu lagi.';
                } elseif (str_contains($diagnosa, 'Diabetes')) {
                    $jenisTl = 'rujukan';
                    $catatanTl = 'Pasien dianjurkan membatasi asupan gula/karbohidrat, jalan santai 30 menit, dan pemeriksaan gula darah berkala.';
                } elseif (str_contains($diagnosa, 'Prediabetes') || str_contains($diagnosa, 'Pre-Hipertensi')) {
                    $jenisTl = 'anjuran_gaya_hidup';
                    $catatanTl = 'Edukasi pola hidup CERDIK, konsumsi makanan serat tinggi, serta disarankan cek kesehatan ulang bulan depan.';
                } elseif (str_contains($diagnosa, 'Gangguan') || str_contains($diagnosa, 'Miopia')) {
                    $jenisTl = 'monitoring';
                    $catatanTl = 'Pasien diberikan edukasi kebersihan indera dan dirujuk ke Poli Mata/THT Puskesmas untuk evaluasi lebih lanjut.';
                } else {
                    $jenisTl = 'edukasi';
                    $catatanTl = 'Hasil skrining dalam batas normal. Pasien dihimbau mempertahankan pola hidup sehat dan rutin berolahraga.';
                }

                TindakLanjutPTM::create([
                    'peserta_id'           => $peserta->id,
                    'deteksi_dini_id'      => $deteksi->id,
                    'petugas_id'           => $petugasId,
                    'jenis_tindak_lanjut'  => $jenisTl,
                    'tanggal_tindak_lanjut'=> $tglTindakLanjutStr,
                    'catatan_petugas'      => $catatanTl,
                    'status_tindak_lanjut' => 'sudah',
                    'dibuat_pada'          => sprintf('2026-08-%02d 10:00:00', $dayTindakLanjut),
                    'diubah_pada'          => sprintf('2026-08-%02d 10:00:00', $dayTindakLanjut),
                ]);
            }
        }

        $this->command->info("Seeder Data Agustus 2026 berhasil diperbarui dengan NAMA PASIEN UNIK!");
    }
}
