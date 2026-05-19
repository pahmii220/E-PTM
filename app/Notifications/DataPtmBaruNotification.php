<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataPtmBaruNotification extends Notification
{
    use Queueable;

    public $dataPtm; // Variabel untuk menyimpan data laporan

    // Constructor untuk menerima data dari Controller nanti
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
        return (new MailMessage)
                    ->subject('[Pemberitahuan] Laporan Data PTM Baru')
                    ->greeting('Halo Tim Verifikator Dinkes,')
                    ->line('Terdapat entri data pelaporan PTM baru yang memerlukan pengecekan dan verifikasi dari Anda.')
                    // Kamu bisa mengaktifkan tombol di bawah ini jika halaman detailnya sudah ada
                    // ->action('Lihat Data', url('/dinkes/verifikasi/' . $this->dataPtm->id))
                    ->line('Terima kasih telah menggunakan Sistem Pemantauan PTM.');
    }
}