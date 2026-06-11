<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AktivasiAkunPetugas extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Akun Akun Anda Telah Aktif, Silahkan Login Ke Aplikasi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.aktivasi_petugas', // File template email blade
        );
    }
}