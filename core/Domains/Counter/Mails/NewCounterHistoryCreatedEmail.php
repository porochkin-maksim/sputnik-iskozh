<?php

namespace Core\Domains\Counter\Mails;

use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCounterHistoryCreatedEmail extends Mailable
{
    public function __construct(
        private readonly string             $toEmail,
        private readonly CounterHistoryDTO  $counterHistory,
        private readonly ?CounterDTO        $counter,
        private readonly ?CounterHistoryDTO $previousCounterHistory,
        private readonly ?AccountDTO        $account,
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