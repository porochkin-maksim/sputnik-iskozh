<?php

namespace Core\Domains\Billing\Payment\Mails;

use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Resources\RouteNames;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewPaymentCreatedEmail extends Mailable
{
    public function __construct(
        private readonly string     $toEmail,
        private readonly PaymentDTO $payment,
    )
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to     : $this->toEmail,
            subject: 'Получено уведомление о платеже',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $link    = route(RouteNames::ADMIN_NEW_PAYMENT_INDEX);
        $comment = nl2br($this->payment->getComment());
        $content = <<<HTML
            <h3>Требуется подтвердить платёж</h3>
            <p>Комментарий:<br> {$comment}</p>
            <p>Подтвердить платеж можете по <a href="{$link}">этой ссылке</a></p>
            HTML;

        return new Content(
            htmlString: "<span>{$content}</span>",
        );
    }
}