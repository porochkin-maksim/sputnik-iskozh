<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Mails;

use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Resources\RouteNames;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewTicketCreatedEmail extends Mailable
{
    public function __construct(
        private readonly string    $toEmail,
        private readonly TicketDTO $ticket,
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
            subject: sprintf('Получена заявка №%s', $this->ticket->getId()),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $account = $this->ticket->getAccount(true);
        $user    = $this->ticket->getUser(true);
        $link    = route(RouteNames::ADMIN_HELP_DESK_TICKETS_VIEW, $this->ticket->getId());

        $content = sprintf(
            <<<HTML
                <h3>Поступило новое обращение от %s</h3>
                <p>Участок: %s</p>
                <p>Пользователь: %s</p>
                <p>Телефон: %s</p>
                <p>Почта: %s</p>
                <p>Обращение:<br> %s</p>
                <p>Посмотреть обращение можно по <a href="{$link}">ссылке</a></p>
                HTML,
            $this->ticket->getContactName(),
            $account?->getNumber(),
            $user?->getViewer()->getFullName(),
            $this->ticket->getContactPhone(),
            $this->ticket->getContactEmail(),
            nl2br($this->ticket->getDescription()),
        );

        return new Content(
            htmlString: "<span>{$content}</span>",
        );
    }
}