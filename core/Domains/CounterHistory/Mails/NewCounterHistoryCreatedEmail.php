<?php

namespace Core\Domains\CounterHistory\Mails;

use App\Resources\RouteNames;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCounterHistoryCreatedEmail extends Mailable
{
    public function __construct(
        private readonly string                $toEmail,
        private readonly CounterHistoryEntity  $counterHistory,
        private readonly ?CounterEntity        $counter,
        private readonly ?CounterHistoryEntity $previousCounterHistory,
        private readonly ?AccountEntity        $account,
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
            subject: 'Получены новые показания счётчиков',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $link    = route(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX);
        $content = <<<HTML
            <h3>Требуется подтвердить показания</h3>
            <p>Номер участка: {$this->account?->getNumber()}</p>
            <p>Номер счётчика: {$this->counter?->getNumber()}</p>
            <p>Показания счётчика: {$this->counterHistory->getValue()}</p>
            <p>Предыдущие показания счётчика: {$this->previousCounterHistory?->getValue()}</p>
            <p>Дата показаний: {$this->counterHistory->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT)}</p>
            <p>Подтвердить показания можете по <a href="{$link}">этой ссылке</a></p>
            HTML;

        return new Content(
            htmlString: "<span>{$content}</span>",
        );
    }
}
