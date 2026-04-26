<?php declare(strict_types=1);

namespace App\Mail\Billing;

use App\Resources\RouteNames;
use Core\Domains\Billing\Payment\PaymentEntity;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewPaymentCreatedEmail extends Mailable
{
    public function __construct(
        private readonly string $toEmail,
        private readonly PaymentEntity $payment,
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->toEmail,
            subject: 'Получено уведомление о платеже',
        );
    }

    public function content(): Content
    {
        $link = route(RouteNames::ADMIN_NEW_PAYMENT_INDEX);
        $comment = nl2br((string) $this->payment->getComment());
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
