<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DeteksiDiniPTM;

class DataPtmDisetujuiNotification extends Notification
{
    use Queueable;

    protected $deteksi;

    public function __construct(DeteksiDiniPTM $deteksi)
    {
        $this->deteksi = $deteksi;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat! Data PTM Anda Telah Disetujui')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Kabar baik! Data deteksi dini pasien Anda telah diperiksa dan disetujui oleh Dinkes.')
            ->line('Nama Pasien: ' . ($this->deteksi->pasien->nama_lengkap ?? '-'))
            ->line('Tanggal Verifikasi: ' . now()->format('d-m-Y H:i'))
            ->action('Lihat Data', url('/petugas/deteksi-dini'))
            ->line('Terima kasih atas dedikasi Anda dalam pelayanan kesehatan.');
    }
}