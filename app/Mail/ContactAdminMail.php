<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjek;
    public $pesan;
    public $pengirim;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subjek, $pesan, $pengirim)
    {
        $this->subjek = $subjek;
        $this->pesan = $pesan;
        $this->pengirim = $pengirim;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tiket Bantuan: ' . $this->subjek)
                    ->view('emails.contact_admin');
    }
}
