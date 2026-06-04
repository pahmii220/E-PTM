<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataPtmDitolakNotification extends Notification
{
    use Queueable;

    protected $item; // Kita ganti jadi variabel umum

    // Hapus Type Hint agar bisa menerima model apa saja
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

public function toMail($notifiable): MailMessage
    {
        // Tentukan URL, Nama Peserta, dan Jenis Data secara dinamis
        $url = '#';
        $namaPeserta = '-';
        $jenisData = 'Data PTM';

        if (get_class($this->item) === 'App\Models\DeteksiDiniPTM') {
            $url = url('/petugas/deteksi-dini/' . $this->item->id . '/edit');
            $namaPeserta = $this->item->pasien->nama_lengkap ?? '-';
            $jenisData = 'Data Deteksi Dini';
            
        } elseif (get_class($this->item) === 'App\Models\FaktorResikoPTM') {
            $url = url('/petugas/faktor-resiko/' . $this->item->id . '/edit');
            $namaPeserta = $this->item->pasien->nama_lengkap ?? '-';
            $jenisData = 'Data Faktor Risiko';
            
        } elseif (get_class($this->item) === 'App\Models\Pasien') {
            $url = url('/petugas/pasien/' . $this->item->id . '/edit');
            // Pemanggilan khusus untuk data pasien agar tidak error
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Data Peserta';
        }

        return (new MailMessage)
            ->error() // BIKIN TOMBOL EMAIL JADI MERAH (Indikator Ditolak/Error)
            ->subject('Pemberitahuan: ' . $jenisData . ' Ditolak / Perlu Revisi')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Mohon maaf, ' . $jenisData . ' yang Anda kirimkan belum dapat disetujui oleh pihak Dinas Kesehatan P2PTM.')
            ->line('Nama Peserta: ' . $namaPeserta)
            ->line('Catatan dari Dinkes: ' . ($this->item->catatan_verifikasi ?? 'Tidak ada catatan.'))
            ->action('Perbaiki Data', $url)
            ->line('Silakan segera klik tombol di atas untuk melakukan perbaikan agar data dapat diverifikasi kembali.');
    }
}