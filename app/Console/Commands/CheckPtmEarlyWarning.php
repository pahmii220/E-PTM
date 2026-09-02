<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EarlyWarningService;

class CheckPtmEarlyWarning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptm:check-early-warning {--bulan= : Bulan analisis (1-12)} {--tahun= : Tahun analisis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menganalisis tren kasus PTM per kecamatan dan mengirim notifikasi peringatan dini otomatis jika terjadi lonjakan kasus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bulan = $this->option('bulan') ? (int) $this->option('bulan') : null;
        $tahun = $this->option('tahun') ? (int) $this->option('tahun') : null;

        $this->info('Memulai analisis peringatan dini tren kasus PTM...');

        $result = EarlyWarningService::getKecamatanAlerts($bulan, $tahun);

        $this->table(
            ['Kecamatan', 'Puskesmas', 'Kasus Aktif', 'Rata-rata', 'Perubahan (%)', 'Level', 'Penyakit Dominan'],
            array_map(function ($item) {
                return [
                    $item['kecamatan'],
                    $item['puskesmas_count'],
                    $item['kasus_aktif'],
                    $item['rata_rata'],
                    ($item['persentase'] > 0 ? '+' : '') . $item['persentase'] . '%',
                    strtoupper($item['level']),
                    $item['penyakit_dominan'],
                ];
            }, $result['semua_kecamatan'])
        );

        if ($result['has_alerts']) {
            $this->warn("Ditemukan {$result['total_alerts']} kecamatan dengan status LONJAKAN KASUS.");
            $sent = EarlyWarningService::checkAndSendAutomatedNotifications($bulan, $tahun);
            $this->info("Berhasil mengirim notifikasi peringatan dini ke {$sent} kecamatan yang memenuhi kriteria.");
        } else {
            $this->info('Semua wilayah kecamatan dalam kondisi normal dan terkendali.');
        }

        return Command::SUCCESS;
    }
}
