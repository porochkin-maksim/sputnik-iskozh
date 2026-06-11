<?php declare(strict_types=1);

namespace App\Mail\User;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LoginLinkMailable extends Mailable
{
    public function __construct(
        private readonly string $toEmail,
        private readonly string $tokenLink,
        private readonly string $pin,
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to     : $this->toEmail,
            subject: 'Постоянная ссылка для входа на сайт',
        );
    }

    public function content(): Content
    {
        $qrPng = QrCode::format('png')
            ->size(200)
            ->generate($this->tokenLink)
            ?->toHtml()
        ;

        $qrDataUri = 'data:image/png;base64,' . base64_encode($qrPng);

        return new Content(
            markdown: 'emails.user.login-link',
            with    : [
                'qrDataUri' => $qrDataUri,
                'tokenLink' => $this->tokenLink,
                'pin'       => $this->pin,
            ],
        );
    }
}
