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
        return ['mail', 'database']; // Tambahkan 'database' untuk in-app notification
    }

    public function toMail($notifiable): MailMessage
    {
        // Tentukan URL, Nama Peserta, dan Jenis Data secara dinamis
        $url = '#';
        $namaPeserta = '-';
        $jenisData = 'Data PTM';

        if ($this->item instanceof \App\Models\DeteksiDiniPTM) {
            $url = route('petugas.deteksi_dini.edit', $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Deteksi Dini';
            
        } elseif ($this->item instanceof \App\Models\FaktorResikoPTM) {
            $url = route('petugas.faktor_resiko.edit', $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Faktor Risiko';
            
        } elseif ($this->item instanceof \App\Models\Peserta) {
            $url = route('petugas.peserta.edit', $this->item->id);
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Data Peserta';
        }

        return (new MailMessage)
            ->error() // BIKIN TOMBOL EMAIL JADI MERAH (Indikator Ditolak/Error)
            ->subject('Pemberitahuan: ' . $jenisData . ' Ditolak / Perlu Revisi')
            ->greeting('Halo, ' . ($notifiable->name ?? 'Petugas'))
            ->line('Mohon maaf, ' . $jenisData . ' yang Anda kirimkan belum dapat disetujui oleh pihak Dinas Kesehatan P2PTM.')
            ->line('Nama Peserta: ' . $namaPeserta)
            ->line('Catatan dari Dinkes: ' . ($this->item->catatan_verifikasi ?? 'Tidak ada catatan.'))
            ->action('Perbaiki Data', $url)
            ->line('Silakan segera klik tombol di atas untuk melakukan perbaikan agar data dapat diverifikasi kembali.');
    }

    public function toDatabase($notifiable): array
    {
        $url = '#';
        $namaPeserta = '-';
        $jenisData = 'Data PTM';

        if ($this->item instanceof \App\Models\DeteksiDiniPTM) {
            $url = route('petugas.deteksi_dini.edit', $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Deteksi Dini';
        } elseif ($this->item instanceof \App\Models\FaktorResikoPTM) {
            $url = route('petugas.faktor_resiko.edit', $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Faktor Risiko';
        } elseif ($this->item instanceof \App\Models\Peserta) {
            $url = route('petugas.peserta.edit', $this->item->id);
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Peserta';
        }

        return [
            'title' => "Revisi {$jenisData}",
            'message' => "Data {$namaPeserta} butuh revisi: " . ($this->item->catatan_verifikasi ?? '-'),
            'url' => $url,
            'type' => 'danger' // untuk warna indikator (merah)
        ];
    }
}