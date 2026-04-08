<?php declare(strict_types=1);

namespace App\Observers\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Illuminate\Database\Eloquent\Model;

class TicketServiceObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::TICKET_SERVICE;
    }

    protected function getPropertyTitles(): array
    {
        return TicketService::PROPERTIES_TO_TITLES;
    }

    /**
     * @param TicketService $model
     */
    protected function formatValue($value, string $field, Model $model): string
    {
        if ($field === TicketService::CATEGORY_ID) {
            return TicketCategory::find($value)?->name;
        }

        return parent::formatValue($value, $field, $model);
    }
}
