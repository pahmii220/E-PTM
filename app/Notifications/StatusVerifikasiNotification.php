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
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $status = $this->dataPtm->status_verifikasi; 
        $labelStatus = ($status == 'verified' || $status == 'disetujui' || $status == 'approved') ? 'Telah Diverifikasi ✅' : 'Perlu Direvisi/Ditolak ❌';

        return [
            'title' => 'Status Verifikasi Laporan',
            'message' => 'Laporan PTM Anda ' . $labelStatus . '. Catatan: ' . ($this->dataPtm->catatan_verifikasi ?? 'Tidak ada catatan.'),
            'url' => route('petugas.laporan.index'),
            'type' => ($status == 'verified' || $status == 'disetujui' || $status == 'approved') ? 'success' : 'danger'
        ];
    }

    public function toMail($notifiable)
    {
        // Mengecek status dari database (sesuaikan dengan nama isian status di aplikasimu)
        $status = $this->dataPtm->status_verifikasi; 
        $labelStatus = ($status == 'verified' || $status == 'disetujui') ? 'Telah Diverifikasi ✅' : 'Perlu Direvisi/Ditolak ❌';

        return (new MailMessage)
                    ->subject('[Update Status] Hasil Verifikasi Laporan PTM')
                    ->greeting('Halo Petugas Puskesmas,')
                    ->line('Data laporan PTM yang Anda kirimkan telah diperiksa oleh Dinas Kesehatan.')
                    ->line('Status laporan saat ini: **' . $labelStatus . '**')
                    ->line('Catatan dari Verifikator: ' . ($this->dataPtm->catatan_verifikasi ?? 'Tidak ada catatan tambahan.'))
                    ->line('Silakan login ke aplikasi untuk melihat rincian lebih lanjut.');
    }
}