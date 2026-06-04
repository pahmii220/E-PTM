<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataPtmBaruNotification extends Notification
{
    use Queueable;

    public $data; // Variabel untuk menyimpan data laporan

    // Constructor untuk menerima data dari Controller nanti
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

public function toMail($notifiable)
    {
        $namaPasien = $this->data->pasien->nama_lengkap ?? 'Tidak diketahui';

        return (new MailMessage)
                    ->subject('Antrean Verifikasi PTM: Kunjungan Pasien Baru')
                    ->greeting('Halo, Tim Dinas Kesehatan')
                    ->line('Terdapat Entri Data Baru (meliputi Data Pasien, Deteksi Dini, dan Faktor Risiko) telah selesai diinput oleh Petugas Puskesmas.')
                    ->line('Nama Pasien: ' . $namaPasien)
                    ->line('Status: Menunggu Verifikasi')
                    ->action('Mulai Verifikasi', url('/pengguna/verifikasi-pasien')) 
                    ->line('Silakan untuk meninjau kelengkapan data tersebut.');
    }
}