<?php

namespace App\Services;

use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;
use App\Models\User;
use App\Notifications\PeringatanDiniLonjakanNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class EarlyWarningService
{
    /**
     * Menganalisis seluruh kecamatan dan mengembalikan daftar alert jika terdeteksi lonjakan kasus PTM.
     *
     * @param int|null $bulan
     * @param int|null $tahun
     * @return array
     */
    public static function getKecamatanAlerts(?int $bulan = null, ?int $tahun = null): array
    {
        $bulan = $bulan ?? (int) request('bulan', now()->month);
        $tahun = $tahun ?? (int) request('tahun', now()->year);

        $kecamatanList = Puskesmas::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->pluck('kecamatan')
            ->unique()
            ->values();

        $alerts = [];
        $semuaKecamatan = [];

        foreach ($kecamatanList as $kec) {
            $pkmModels = Puskesmas::where('kecamatan', $kec)->get();
            $pkmIds = $pkmModels->pluck('id');
            if ($pkmIds->isEmpty()) {
                continue;
            }

            // Ambil daftar Puskesmas & jumlah kasusnya di kecamatan ini
            $puskesmasDetails = $pkmModels->map(function ($p) use ($bulan, $tahun) {
                $kasusPkm = DeteksiDiniPTM::where('puskesmas_id', $p->id)
                    ->whereMonth('tanggal_pemeriksaan', $bulan)
                    ->whereYear('tanggal_pemeriksaan', $tahun)
                    ->where(function ($q) {
                        $q->whereIn('hasil_skrining', ['berisiko', 'dicurigai', 'risiko_tinggi', 'risiko_sedang'])
                          ->orWhereNotNull('diagnosa_penyakit');
                    })
                    ->count();
                return [
                    'id'             => $p->id,
                    'nama_puskesmas' => $p->nama_puskesmas,
                    'kasus'          => $kasusPkm,
                ];
            })->toArray();

            // 1. Kasus berisiko / terdiagnosa bulan aktif
            $kasusAktif = DeteksiDiniPTM::whereIn('puskesmas_id', $pkmIds)
                ->whereMonth('tanggal_pemeriksaan', $bulan)
                ->whereYear('tanggal_pemeriksaan', $tahun)
                ->where(function ($q) {
                    $q->whereIn('hasil_skrining', ['berisiko', 'dicurigai', 'risiko_tinggi', 'risiko_sedang'])
                      ->orWhereNotNull('diagnosa_penyakit');
                })
                ->count();

            // Total seluruh skrining bulan aktif
            $totalSkrining = DeteksiDiniPTM::whereIn('puskesmas_id', $pkmIds)
                ->whereMonth('tanggal_pemeriksaan', $bulan)
                ->whereYear('tanggal_pemeriksaan', $tahun)
                ->count();

            // 2. Rata-rata bulanan historis (3 bulan sebelumnya)
            $dateNow = Carbon::create($tahun, $bulan, 1);
            $startHist = $dateNow->copy()->subMonths(3)->startOfMonth();
            $endHist = $dateNow->copy()->subMonth()->endOfMonth();

            $kasusHist = DeteksiDiniPTM::whereIn('puskesmas_id', $pkmIds)
                ->whereBetween('tanggal_pemeriksaan', [$startHist, $endHist])
                ->where(function ($q) {
                    $q->whereIn('hasil_skrining', ['berisiko', 'dicurigai', 'risiko_tinggi', 'risiko_sedang'])
                      ->orWhereNotNull('diagnosa_penyakit');
                })
                ->count();

            $rataRata = round($kasusHist / 3, 1);
            if ($rataRata <= 0) {
                $rataRata = 1.0; // Baseline minimal agar tidak division by zero
            }

            // 3. Hitung persentase perubahan
            $selisih = $kasusAktif - $rataRata;
            $persentase = round(($selisih / $rataRata) * 100, 1);

            // 4. Diagnosa penyakit paling dominan di kecamatan tsb (Hanya penyakit riil, abaikan status Normal/Sehat)
            $penyakitDominan = DeteksiDiniPTM::whereIn('puskesmas_id', $pkmIds)
                ->whereMonth('tanggal_pemeriksaan', $bulan)
                ->whereYear('tanggal_pemeriksaan', $tahun)
                ->whereNotNull('diagnosa_penyakit')
                ->where('diagnosa_penyakit', '!=', '')
                ->whereNotIn('diagnosa_penyakit', ['Normal', 'Sehat', 'Normal / Sehat', '-', 'Tidak Ada', 'Nihil'])
                ->groupBy('diagnosa_penyakit')
                ->selectRaw('diagnosa_penyakit, count(*) as total')
                ->orderByDesc('total')
                ->first();

            $namaPenyakit = $penyakitDominan 
                ? "{$penyakitDominan->diagnosa_penyakit} ({$penyakitDominan->total} kasus)" 
                : 'Hipertensi / Diabetes Melitus';

            $itemData = [
                'kecamatan'        => $kec,
                'puskesmas_count'  => $pkmIds->count(),
                'puskesmas_list'   => $puskesmasDetails,
                'kasus_aktif'      => $kasusAktif,
                'total_skrining'   => $totalSkrining,
                'rata_rata'        => $rataRata,
                'persentase'       => $persentase,
                'penyakit_dominan' => $namaPenyakit,
                'bulan'            => $bulan,
                'tahun'            => $tahun,
                'is_lonjakan'      => false,
                'level'            => 'safe', // safe, warning, danger
            ];

            // 5. Evaluasi ambang batas (Threshold)
            // Kategori Bahaya (Danger): Lonjakan >= 25% atau Kasus Aktif > 15 dengan kenaikan signifikan
            if ($persentase >= 25 && $kasusAktif >= 3) {
                $itemData['is_lonjakan'] = true;
                $itemData['level'] = 'danger';
                $itemData['rekomendasi'] = "Lonjakan kasus signifikan (+{$persentase}%). Diperlukan inspeksi Posbindu dan penambahan suplai logistik screening di Kecamatan {$kec}.";
                $alerts[] = $itemData;
            } elseif ($persentase >= 10 && $kasusAktif >= 2) {
                $itemData['is_lonjakan'] = true;
                $itemData['level'] = 'warning';
                $itemData['rekomendasi'] = "Tren kasus meningkat (+{$persentase}%). Disarankan monitoring berkala di fasilitas kesehatan Kecamatan {$kec}.";
                $alerts[] = $itemData;
            }

            $semuaKecamatan[] = $itemData;
        }

        return [
            'alerts'          => $alerts,
            'has_alerts'      => !empty($alerts),
            'total_alerts'    => count($alerts),
            'semua_kecamatan' => $semuaKecamatan,
            'bulan'           => $bulan,
            'tahun'           => $tahun,
        ];
    }

    /**
     * Mengirimkan notifikasi otomatis ke Kepala P2PTM & Pegawai Dinkes jika ada lonjakan kasus.
     *
     * @param int|null $bulan
     * @param int|null $tahun
     * @return int Jumlah notifikasi yang berhasil dikirim
     */
    public static function checkAndSendAutomatedNotifications(?int $bulan = null, ?int $tahun = null): int
    {
        $data = self::getKecamatanAlerts($bulan, $tahun);
        if (!$data['has_alerts']) {
            return 0;
        }

        $recipients = User::whereIn('role_name', ['kepala_p2ptm', 'pegawai'])
            ->where('status_aktif', 1)
            ->get();

        if ($recipients->isEmpty()) {
            return 0;
        }

        $sentCount = 0;

        foreach ($data['alerts'] as $alert) {
            $cacheKey = "early_warning_sent_{$alert['kecamatan']}_{$alert['bulan']}_{$alert['tahun']}";
            
            // Hindari spam notifikasi: kirim maksimal 1x per 3 hari per kecamatan
            if (!Cache::has($cacheKey)) {
                Notification::send($recipients, new PeringatanDiniLonjakanNotification($alert));
                Cache::put($cacheKey, true, now()->addDays(3));
                $sentCount++;
            }
        }

        return $sentCount;
    }
}
