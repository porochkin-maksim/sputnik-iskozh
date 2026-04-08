<?php declare(strict_types=1);

namespace App\Observers\HelpDesk;

use App\Models\Account\Account;
use App\Models\HelpDesk\Ticket;
use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use App\Models\User;
use App\Observers\AbstractObserver;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\User\UserLocator;
use Illuminate\Database\Eloquent\Model;

class TicketObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::TICKET;
    }

    protected function getPropertyTitles(): array
    {
        return Ticket::PROPERTIES_TO_TITLES;
    }

    /**
     * @param Ticket $model
     */
    protected function formatValue($value, string $field, Model $model): string
    {
        return match ($field) {
            Ticket::TYPE        => $value instanceof TicketTypeEnum ? $value->name() : TicketTypeEnum::tryFrom($value)->name(),
            Ticket::PRIORITY    => $value instanceof TicketPriorityEnum ? $value->name() : TicketPriorityEnum::tryFrom($value)->name(),
            Ticket::STATUS      => $value instanceof TicketStatusEnum ? $value->name() : TicketStatusEnum::tryFrom($value)->name(),
            Ticket::CATEGORY_ID => TicketCategory::find($value)?->name,
            Ticket::SERVICE_ID  => TicketService::find($value)?->name,
            Ticket::ACCOUNT_ID  => Account::find($value)?->number,
            Ticket::USER_ID     => $value ? UserLocator::UserDecorator(UserLocator::UserFactory()->makeDtoFromObject(User::find($value))) : null,
            default             => parent::formatValue($value, $field, $model),
        };
    }
}
