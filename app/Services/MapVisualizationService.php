<?php

namespace App\Services;

use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;
use Illuminate\Support\Facades\DB;

class MapVisualizationService
{
    /**
     * Mengambil dan memformat data peta komprehensif untuk Heat Map, Marker Cluster, dan Choropleth Wilayah.
     *
     * @param int|string|null $bulan (1-12 atau 'semua')
     * @param int|null $tahun
     * @return array
     */
    public static function getMapData($bulan = null, ?int $tahun = null): array
    {
        $bulanFilter = ($bulan !== null && $bulan !== 'semua' && $bulan !== '') ? (int) $bulan : null;
        $tahunFilter = $tahun ?? (int) date('Y');

        // 1. Ambil Semua Puskesmas yang memiliki koordinat valid
        $puskesmasList = Puskesmas::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->get();

        // 2. Query skrining deteksi dini sesuai filter waktu
        $skriningQuery = DeteksiDiniPTM::query();
        if ($bulanFilter) {
            $skriningQuery->whereMonth('tanggal_pemeriksaan', $bulanFilter);
        }
        if ($tahunFilter) {
            $skriningQuery->whereYear('tanggal_pemeriksaan', $tahunFilter);
        }
        $allSkrining = $skriningQuery->get();

        // Kelompokkan skrining per puskesmas
        $skriningByPkm = $allSkrining->groupBy('puskesmas_id');

        $puskesmasData = [];
        $heatPointsAll = [];
        $heatPointsHipertensi = [];
        $heatPointsDiabetes = [];
        $heatPointsKolesterol = [];
        $heatPointsObesitas = [];
        $heatPointsRisikoTinggi = [];

        $kecamatanStats = [
            'Banjarmasin Utara'   => ['skrining' => 0, 'kasus_ptm' => 0, 'hipertensi' => 0, 'diabetes' => 0, 'kolesterol' => 0, 'obesitas' => 0, 'risiko_tinggi' => 0, 'puskesmas' => []],
            'Banjarmasin Barat'   => ['skrining' => 0, 'kasus_ptm' => 0, 'hipertensi' => 0, 'diabetes' => 0, 'kolesterol' => 0, 'obesitas' => 0, 'risiko_tinggi' => 0, 'puskesmas' => []],
            'Banjarmasin Tengah'  => ['skrining' => 0, 'kasus_ptm' => 0, 'hipertensi' => 0, 'diabetes' => 0, 'kolesterol' => 0, 'obesitas' => 0, 'risiko_tinggi' => 0, 'puskesmas' => []],
            'Banjarmasin Timur'   => ['skrining' => 0, 'kasus_ptm' => 0, 'hipertensi' => 0, 'diabetes' => 0, 'kolesterol' => 0, 'obesitas' => 0, 'risiko_tinggi' => 0, 'puskesmas' => []],
            'Banjarmasin Selatan' => ['skrining' => 0, 'kasus_ptm' => 0, 'hipertensi' => 0, 'diabetes' => 0, 'kolesterol' => 0, 'obesitas' => 0, 'risiko_tinggi' => 0, 'puskesmas' => []],
        ];

        foreach ($puskesmasList as $pkm) {
            $lat = (float) $pkm->latitude;
            $lng = (float) $pkm->longitude;
            $pkmSkrining = $skriningByPkm->get($pkm->id, collect());

            $totalSkrining = $pkmSkrining->count();
            
            // Hitung penyakit & status
            $countHipertensi = 0;
            $countDiabetes = 0;
            $countKolesterol = 0;
            $countObesitas = 0;
            $countRisikoTinggi = 0;
            $countDicurigai = 0;
            $countNormal = 0;
            $penyakitBreakdown = [];

            foreach ($pkmSkrining as $item) {
                $diagnosa = (string) ($item->diagnosa_penyakit ?? '');
                $hasil = (string) ($item->hasil_skrining ?? '');
                $diagLower = strtolower($diagnosa);
                $hasilLower = strtolower($hasil);

                $isHipertensi = str_contains($diagLower, 'hipertensi');
                $isDiabetes = str_contains($diagLower, 'diabetes') || str_contains($diagLower, 'prediabetes');
                $isKolesterol = str_contains($diagLower, 'kolesterol') || str_contains($diagLower, 'lipid');
                $isObesitas = str_contains($diagLower, 'obesitas');

                if ($isHipertensi) $countHipertensi++;
                if ($isDiabetes) $countDiabetes++;
                if ($isKolesterol) $countKolesterol++;
                if ($isObesitas) $countObesitas++;

                if (str_contains($hasilLower, 'tinggi') || str_contains($hasilLower, 'berat')) {
                    $countRisikoTinggi++;
                } elseif (str_contains($hasilLower, 'curiga') || str_contains($hasilLower, 'sedang')) {
                    $countDicurigai++;
                } else {
                    $countNormal++;
                }

                // Top breakdown
                if ($diagnosa && !in_array($diagLower, ['normal', 'sehat', 'normal / sehat', '-', 'nihil'])) {
                    $penyakitBreakdown[$diagnosa] = ($penyakitBreakdown[$diagnosa] ?? 0) + 1;
                }
            }

            arsort($penyakitBreakdown);
            $topPenyakit = array_slice($penyakitBreakdown, 0, 3, true);

            $totalKasusPTM = $countHipertensi + $countDiabetes + $countKolesterol + $countObesitas;
            if ($totalKasusPTM === 0 && ($countRisikoTinggi + $countDicurigai) > 0) {
                $totalKasusPTM = $countRisikoTinggi + $countDicurigai;
            }

            // Hitung rasio kasus & status keparahan puskesmas
            $rasioRisiko = $totalSkrining > 0 ? round((($countRisikoTinggi + $countDicurigai) / $totalSkrining) * 100, 1) : 0;
            $severity = 'low';
            if ($totalKasusPTM >= 10 || $rasioRisiko >= 60) {
                $severity = 'high';
            } elseif ($totalKasusPTM >= 5 || $rasioRisiko >= 30) {
                $severity = 'medium';
            }

            $pkmDetail = [
                'id'                 => $pkm->id,
                'nama_puskesmas'     => $pkm->nama_puskesmas,
                'kecamatan'          => $pkm->kecamatan,
                'alamat'             => $pkm->alamat ?? 'Kota Banjarmasin',
                'lat'                => $lat,
                'lng'                => $lng,
                'total_skrining'     => $totalSkrining,
                'total_kasus_ptm'    => $totalKasusPTM,
                'hipertensi'         => $countHipertensi,
                'diabetes'           => $countDiabetes,
                'kolesterol'         => $countKolesterol,
                'obesitas'           => $countObesitas,
                'risiko_tinggi'      => $countRisikoTinggi,
                'dicurigai'          => $countDicurigai,
                'normal'             => $countNormal,
                'rasio_risiko'       => $rasioRisiko,
                'severity'           => $severity,
                'top_penyakit'       => $topPenyakit,
            ];

            $puskesmasData[] = $pkmDetail;

            // Generate Heatmap points dengan dispersi mikro (spatial jitter) di sekitar puskesmas
            self::generateHeatPoints($heatPointsAll, $lat, $lng, $totalKasusPTM, 0.9);
            if ($countHipertensi > 0) {
                self::generateHeatPoints($heatPointsHipertensi, $lat, $lng, $countHipertensi, 0.95);
            }
            if ($countDiabetes > 0) {
                self::generateHeatPoints($heatPointsDiabetes, $lat, $lng, $countDiabetes, 0.95);
            }
            if ($countKolesterol > 0) {
                self::generateHeatPoints($heatPointsKolesterol, $lat, $lng, $countKolesterol, 0.9);
            }
            if ($countObesitas > 0) {
                self::generateHeatPoints($heatPointsObesitas, $lat, $lng, $countObesitas, 0.9);
            }
            if ($countRisikoTinggi > 0) {
                self::generateHeatPoints($heatPointsRisikoTinggi, $lat, $lng, $countRisikoTinggi, 1.0);
            }

            // Agregasi Kecamatan Stats
            $matchedKec = null;
            foreach (array_keys($kecamatanStats) as $kName) {
                if (str_contains(strtolower($pkm->kecamatan), strtolower(str_replace('Banjarmasin ', '', $kName)))) {
                    $matchedKec = $kName;
                    break;
                }
            }
            if ($matchedKec) {
                $kecamatanStats[$matchedKec]['skrining'] += $totalSkrining;
                $kecamatanStats[$matchedKec]['kasus_ptm'] += $totalKasusPTM;
                $kecamatanStats[$matchedKec]['hipertensi'] += $countHipertensi;
                $kecamatanStats[$matchedKec]['diabetes'] += $countDiabetes;
                $kecamatanStats[$matchedKec]['kolesterol'] += $countKolesterol;
                $kecamatanStats[$matchedKec]['obesitas'] += $countObesitas;
                $kecamatanStats[$matchedKec]['risiko_tinggi'] += $countRisikoTinggi;
                $kecamatanStats[$matchedKec]['puskesmas'][] = $pkm->nama_puskesmas;
            }
        }

        // 3. Format Choropleth Severity & Prevalensi Wilayah Kecamatan
        $kecamatanChoropleth = [];

        foreach ($kecamatanStats as $namaKec => $stats) {
            $totalKasus = $stats['kasus_ptm'];
            $totalSkrining = $stats['skrining'];
            $prevalensi = $totalSkrining > 0 ? round(($totalKasus / $totalSkrining) * 100, 1) : 0;

            // Klasifikasi Outbreak Level
            $zoneClass = 'safe'; // Hijau
            $color = '#10b981';
            $zoneLabel = 'Zona Hijau (Terkendali)';

            if ($totalKasus >= 25 || $prevalensi >= 60) {
                $zoneClass = 'danger'; // Merah
                $color = '#ef4444';
                $zoneLabel = 'Zona Merah (Outbreak Tinggi)';
            } elseif ($totalKasus >= 15 || $prevalensi >= 40) {
                $zoneClass = 'warning'; // Oranye
                $color = '#f97316';
                $zoneLabel = 'Zona Oranye (Waspada)';
            } elseif ($totalKasus >= 5 || $prevalensi >= 20) {
                $zoneClass = 'moderate'; // Kuning
                $color = '#eab308';
                $zoneLabel = 'Zona Kuning (Sedang)';
            }

            // Diagnosa Dominan
            $diagCounts = [
                'Hipertensi' => $stats['hipertensi'],
                'Diabetes Melitus' => $stats['diabetes'],
                'Kolesterol' => $stats['kolesterol'],
                'Obesitas' => $stats['obesitas'],
            ];
            arsort($diagCounts);
            $dominantName = array_key_first($diagCounts);
            $dominantCount = reset($diagCounts);

            $kecDetail = [
                'nama'             => $namaKec,
                'total_skrining'   => $totalSkrining,
                'total_kasus_ptm'  => $totalKasus,
                'prevalensi_rate'  => $prevalensi,
                'hipertensi'       => $stats['hipertensi'],
                'diabetes'         => $stats['diabetes'],
                'kolesterol'       => $stats['kolesterol'],
                'obesitas'         => $stats['obesitas'],
                'risiko_tinggi'    => $stats['risiko_tinggi'],
                'dominant_disease' => $dominantName . ' (' . $dominantCount . ' kasus)',
                'zone_class'       => $zoneClass,
                'zone_label'       => $zoneLabel,
                'zone_color'       => $color,
                'puskesmas_count'  => count($stats['puskesmas']),
                'puskesmas_list'   => $stats['puskesmas'],
            ];

            $kecamatanChoropleth[$namaKec] = $kecDetail;
        }

        // 4. Hitung Hotspot Teratas per Kategori Penyakit
        $diseaseHotspots = [];
        foreach (['all', 'hipertensi', 'diabetes', 'kolesterol', 'obesitas', 'risiko_tinggi'] as $dKey) {
            $sortedPkm = $puskesmasData;
            usort($sortedPkm, function($a, $b) use ($dKey) {
                $cA = ($dKey === 'all') ? $a['total_kasus_ptm'] : ($a[$dKey] ?? 0);
                $cB = ($dKey === 'all') ? $b['total_kasus_ptm'] : ($b[$dKey] ?? 0);
                return $cB <=> $cA;
            });
            $topPkmFiltered = array_values(array_filter($sortedPkm, function($p) use ($dKey) {
                $c = ($dKey === 'all') ? $p['total_kasus_ptm'] : ($p[$dKey] ?? 0);
                return $c > 0;
            }));

            // Ranking Kecamatan untuk penyakit ini
            $sortedKec = array_values($kecamatanChoropleth);
            usort($sortedKec, function($a, $b) use ($dKey) {
                $cA = ($dKey === 'all') ? $a['total_kasus_ptm'] : ($a[$dKey] ?? 0);
                $cB = ($dKey === 'all') ? $b['total_kasus_ptm'] : ($b[$dKey] ?? 0);
                return $cB <=> $cA;
            });
            $topKec = !empty($sortedKec) ? $sortedKec[0] : null;

            $diseaseHotspots[$dKey] = [
                'top_kecamatan' => $topKec,
                'top_puskesmas' => array_slice($topPkmFiltered, 0, 3),
            ];
        }

        return [
            'puskesmas_data'         => $puskesmasData,
            'heat_points'            => [
                'all'            => $heatPointsAll,
                'hipertensi'     => $heatPointsHipertensi,
                'diabetes'       => $heatPointsDiabetes,
                'kolesterol'     => $heatPointsKolesterol,
                'obesitas'       => $heatPointsObesitas,
                'risiko_tinggi'  => $heatPointsRisikoTinggi,
            ],
            'kecamatan_choropleth'   => $kecamatanChoropleth,
            'disease_hotspots'       => $diseaseHotspots,
            'top_hotspots'           => $diseaseHotspots['all'],
            'total_skrining_global'  => $allSkrining->count(),
            'bulan_filter'           => $bulanFilter,
            'tahun_filter'           => $tahunFilter,
        ];
    }

    /**
     * Membantu membuat titik panas dengan bobot dan jitter mikro agar gradien Heatmap tampak realistis dan halus
     */
    private static function generateHeatPoints(array &$points, float $centerLat, float $centerLng, int $count, float $baseIntensity = 0.9): void
    {
        if ($count <= 0) return;

        // Titik pusat puskesmas - bobot kuat
        $weight = min(1.0, 0.45 + ($count * 0.08));
        $points[] = [$centerLat, $centerLng, $weight * $baseIntensity];

        // Sebar titik mikro (jitter) sekeliling puskesmas sesuai jumlah kasus (~80m - 350m)
        $subPointsCount = min(25, max(4, $count * 3));
        for ($i = 0; $i < $subPointsCount; $i++) {
            $angle = ($i / $subPointsCount) * 2 * M_PI;
            $distance = 0.0008 + (($i % 4) * 0.0007);
            $jLat = $centerLat + (cos($angle) * $distance);
            $jLng = $centerLng + (sin($angle) * $distance * 1.15);
            $jIntensity = max(0.25, ($weight * 0.75) - (($i % 3) * 0.1));
            $points[] = [round($jLat, 6), round($jLng, 6), round($jIntensity, 2)];
        }
    }
}
