<?php
namespace App\Helpers;

class SkriningSimple
{
    /**
     * Input: berat_badan (kg), tinggi_badan (cm), tekanan_darah (string "120/80")
     * Output: imt, kategori_imt, hipertensi (bool), risk_level, summary
     */
    public static function compute(array $input): array
    {
        $berat = isset($input['berat_badan']) && $input['berat_badan'] !== '' ? (float)$input['berat_badan'] : null;
        $tinggi_cm = isset($input['tinggi_badan']) && $input['tinggi_badan'] !== '' ? (float)$input['tinggi_badan'] : null;
        $tekanan = isset($input['tekanan_darah']) ? trim($input['tekanan_darah']) : null;

        $imt = null; $kategori = null;
        if ($berat && $tinggi_cm && $tinggi_cm > 0) {
            $t = $tinggi_cm / 100;
            $imt = round($berat / ($t*$t), 2);
            if ($imt < 18.5) $kategori = 'Kurus';
            elseif ($imt < 25) $kategori = 'Normal';
            elseif ($imt < 30) $kategori = 'Overweight';
            else $kategori = 'Obesitas';
        }

        $sbp = $dbp = null;
        if ($tekanan && strpos($tekanan, '/') !== false) {
            list($a,$b) = explode('/', $tekanan);
            $sbp = is_numeric($a)? (int)trim($a) : null;
            $dbp = is_numeric($b)? (int)trim($b) : null;
        }

        // hipertensi sederhana: >=140/90
        $hipertensi = false;
        if (($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90)) $hipertensi = true;

        // risk: Tinggi jika hipertensi OR obesitas; Sedang if overweight; else Rendah
        if ($hipertensi || $kategori === 'Obesitas') $risk = 'Tinggi';
        elseif ($kategori === 'Overweight') $risk = 'Sedang';
        else $risk = 'Rendah';

        $summaryParts = [];
        if ($imt !== null) $summaryParts[] = "IMT {$imt} ({$kategori})";
        if ($sbp !== null || $dbp !== null) $summaryParts[] = "TD: ".($sbp ?? '-')."/".($dbp ?? '-').($hipertensi? ' (Hipertensi)':'');
        $summary = implode(' | ', $summaryParts) ?: 'Data skrining terbatas.';

        return [
            'imt' => $imt,
            'kategori_imt' => $kategori,
            'sbp' => $sbp,
            'dbp' => $dbp,
            'hipertensi' => $hipertensi,
            'risk_level' => $risk,
            'summary' => $summary,
        ];
    }
}
