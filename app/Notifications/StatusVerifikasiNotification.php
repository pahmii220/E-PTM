<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusVerifikasiNotification extends Notification
{
    use Queueable;

    public $dataPtm;

    public function __construct($dataPtm)
    {
        $this->dataPtm = $dataPtm;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Mengecek status dari database (sesuaikan dengan nama isian status di aplikasimu)
        $status = $this->dataPtm->verification_status; 
        $labelStatus = ($status == 'verified' || $status == 'disetujui') ? 'Telah Diverifikasi ✅' : 'Perlu Direvisi/Ditolak ❌';

        return (new MailMessage)
                    ->subject('[Update Status] Hasil Verifikasi Laporan PTM')
                    ->greeting('Halo Petugas Puskesmas,')
                    ->line('Data laporan PTM yang Anda kirimkan telah diperiksa oleh Dinas Kesehatan.')
                    ->line('Status laporan saat ini: **' . $labelStatus . '**')
                    ->line('Catatan dari Verifikator: ' . ($this->dataPtm->verification_note ?? 'Tidak ada catatan tambahan.'))
                    ->line('Silakan login ke aplikasi untuk melihat rincian lebih lanjut.');
    }
}