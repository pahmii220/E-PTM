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

    // 1. Ubah namanya menjadi $item agar lebih umum
    public $item;

    /**
     * Create a new notification instance.
     */
    // 2. Hapus kata "DeteksiDiniPTM" di dalam kurung
    public function __construct($item) 
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $jenisData = 'Data PTM';
        $namaPeserta = '-';

        if (get_class($this->item) === 'App\Models\DeteksiDiniPTM') {
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Deteksi Dini';
        } elseif (get_class($this->item) === 'App\Models\FaktorResikoPTM') {
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Faktor Risiko';
        } elseif (get_class($this->item) === 'App\Models\Peserta') {
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Peserta';
        }

        return [
            'title' => "Revisi {$jenisData}",
            'message' => "Petugas telah merevisi data {$jenisData} untuk {$namaPeserta}.",
            'url' => route('pengguna.verifikasi_laporan.index'),
            'type' => 'warning'
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
public function toMail(object $notifiable): MailMessage
    {
        // Tentukan URL, Nama Peserta, Jenis Data, dan Tanggal secara dinamis
        $url = '#';
        $namaPeserta = '-';
        $jenisData = 'Data PTM';
        $tanggal = '-';

        // Pastikan variabel di constructor Anda bernama $this->item 
        // (Ubah $this->deteksi menjadi $this->item di seluruh class ini jika sebelumnya berbeda)
        if (get_class($this->item) === 'App\Models\DeteksiDiniPTM') {
            $url = url('/pengguna/verifikasi/deteksi'); // Arahkan ke halaman verifikasi Dinkes
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Deteksi Dini';
            $tanggal = $this->item->tanggal_pemeriksaan ?? '-';
            
        } elseif (get_class($this->item) === 'App\Models\FaktorResikoPTM') {
            $url = url('/pengguna/verifikasi/faktor'); // Arahkan ke halaman verifikasi Dinkes
            $namaPeserta = $this->item->peserta->nama_lengkap ?? '-';
            $jenisData = 'Data Faktor Risiko';
            $tanggal = $this->item->tanggal_pemeriksaan ?? '-';
            
        } elseif (get_class($this->item) === 'App\Models\Peserta') {
            $url = url('/pengguna/verifikasi/peserta'); // Arahkan ke halaman verifikasi Dinkes
            $namaPeserta = $this->item->nama_lengkap ?? '-'; 
            $jenisData = 'Data Peserta';
            // Pasien biasanya tidak punya tanggal_pemeriksaan, gunakan created_at
            $tanggal = $this->item->created_at ? $this->item->created_at->format('Y-m-d') : '-'; 
        }

        return (new MailMessage)
            ->subject('Pemberitahuan: Revisi ' . $jenisData . ' Terkirim')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Terdapat ' . $jenisData . ' yang telah selesai di-REVISI oleh Petugas Puskesmas.')
            ->line('Nama Peserta: ' . $namaPeserta)
            ->line('Tanggal Pemeriksaan / Input: ' . $tanggal)
            ->line('Status saat ini: Menunggu Verifikasi Kembali')
            ->action('Verifikasi Data', $url)
            ->line('Mohon segera ditinjau kembali. Terima kasih atas kerja sama Anda.');
    }
}