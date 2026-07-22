<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataPtmDisetujuiNotification extends Notification
{
    use Queueable;

    protected $item;

    // Hapus Type Hint agar bisa menerima model apa saja
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $jenisData = 'Data PTM';
        $namaPeserta = '-';

        if (get_class($this->item) === 'App\Models\DeteksiDiniPTM') {
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Deteksi Dini';
        } elseif (get_class($this->item) === 'App\Models\FaktorResikoPTM') {
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Faktor Risiko';
        } elseif (get_class($this->item) === 'App\Models\Peserta') {
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Data Peserta Baru';
        }

        return [
            'title' => $jenisData . ' Disetujui',
            'message' => 'Dinkes menyetujui data ' . $jenisData . ' untuk ' . $namaPeserta,
            'url' => route('petugas.laporan.index'),
            'type' => 'success'
        ];
    }

public function toMail($notifiable): MailMessage
    {
        // Tentukan URL dan Nama Peserta secara dinamis
        $url = '#';
        $namaPeserta = '-';
        $jenisData = 'Data PTM';

        if (get_class($this->item) === 'App\Models\DeteksiDiniPTM') {
            $url = url('/petugas/deteksi-dini/' . $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Deteksi Dini';
            
        } elseif (get_class($this->item) === 'App\Models\FaktorResikoPTM') {
            $url = url('/petugas/faktor-resiko/' . $this->item->id);
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Faktor Risiko';
            
        } elseif (get_class($this->item) === 'App\Models\Peserta') {
            $url = url('/petugas/peserta/' . $this->item->id);
            // KARENA INI DATA PESERTA, LANGSUNG PANGGIL NAMA LENGKAPNYA
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Data Peserta Baru';
        }

        return (new MailMessage)
            ->subject('Pemberitahuan: ' . $jenisData . ' Disetujui')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Kabar gembira! ' . $jenisData . ' yang Anda kirimkan telah disetujui oleh pihak Dinas Kesehatan P2PTM.')
            ->line('Nama Peserta: ' . $namaPeserta)
            ->line('Catatan: ' . ($this->item->catatan_verifikasi ?? 'Data telah diverifikasi dan valid.'))
            ->action('Lihat Data', $url)
            ->line('Terima kasih atas partisipasi Anda dalam layanan PTM.');
    }
}