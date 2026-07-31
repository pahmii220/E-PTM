<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class StatusResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;

    public function __construct(User $user, string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subjectStatus = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        return new Envelope(
            subject: '[E-PTM] Permintaan Reset Password Anda ' . $subjectStatus,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status_reset_password',
        );
    }
}
