<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\PasswordResetRequest;

class PermintaanResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetRequest;

    public function __construct(User $user, PasswordResetRequest $resetRequest)
    {
        $this->user = $user;
        $this->resetRequest = $resetRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[E-PTM] Permintaan Reset Password Baru: ' . $this->user->Username,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permintaan_reset_password',
        );
    }
}
