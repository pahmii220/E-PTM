<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanLaporanMonitoringNotification extends Notification
{
    use Queueable;

    protected $laporan;
    protected $namaPegawai;

    public function __construct($laporan, $namaPegawai)
    {
        $this->laporan = $laporan;
        $this->namaPegawai = $namaPegawai;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $puskesmasNama = $this->laporan->puskesmas->nama_puskesmas ?? 'Puskesmas';

        return [
            'type'       => 'warning',
            'title'      => 'Pengajuan Laporan Hasil Monitoring Baru',
            'message'    => $this->namaPegawai . ' mengajukan Laporan Hasil Monitoring untuk ' . $puskesmasNama . ' (' . $this->laporan->judul_laporan . '). Segera tinjau & berikan persetujuan.',
            'laporan_id' => $this->laporan->id,
            'url'        => route('kepala.laporan_monitoring.index'),
        ];
    }
}
