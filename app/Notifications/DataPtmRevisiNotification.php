<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DeteksiDiniPTM;

class DataPtmRevisiNotification extends Notification
{
    use Queueable;

    protected $deteksi;

    /**
     * Create a new notification instance.
     */
    public function __construct(DeteksiDiniPTM $deteksi)
    {
        $this->deteksi = $deteksi;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pemberitahuan: Revisi Data PTM Terkirim')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Terdapat Data Deteksi Dini PTM yang telah di REVISI.')
            ->line('Nama Peserta: ' . ($this->deteksi->pasien->nama_lengkap ?? 'Tidak diketahui'))
            ->line('Tanggal Pemeriksaan: ' . $this->deteksi->tanggal_pemeriksaan)
            ->line('Status saat ini: Menunggu Verifikasi Kembali')
            ->action('Lihat Data', url('/petugas/deteksi-dini/' . $this->deteksi->id . '/edit'))
            ->line('Terima kasih atas kerja sama Anda.');
    }
}