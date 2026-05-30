<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DeteksiDiniPTM;

class DataPtmDitolakNotification extends Notification
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
            ->subject('Pemberitahuan: Data PTM Ditolak')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Mohon maaf, data deteksi dini yang Anda kirimkan belum dapat disetujui oleh pihak Dinas Kesehatan P2PTM.')
            ->line('Nama Peserta: ' . ($this->deteksi->pasien->nama_lengkap ?? '-'))
            ->line('Catatan dari Dinkes: ' . ($this->deteksi->catatan_verifikasi ?? 'Tidak ada catatan.'))
            ->action('Perbaiki Data', url('/petugas/deteksi-dini/' . $this->deteksi->id . '/edit'))
            ->line('Silakan segera lakukan perbaikan agar data dapat diverifikasi kembali.');
    }
}