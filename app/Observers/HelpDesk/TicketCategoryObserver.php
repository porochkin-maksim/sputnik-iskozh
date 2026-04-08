<?php declare(strict_types=1);

namespace App\Observers\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Observers\AbstractObserver;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Illuminate\Database\Eloquent\Model;

class TicketCategoryObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::TICKET_CATEGORY;
    }

    protected function getPropertyTitles(): array
    {
        return TicketCategory::PROPERTIES_TO_TITLES;
    }

    /**
     * @param TicketCategory $model
     */
    protected function formatValue($value, string $field, Model $model): string
    {
        if ($field === TicketCategory::TYPE) {
            return $value instanceof TicketTypeEnum ? $value->name() : TicketTypeEnum::tryFrom($value)->name();
        }

        return parent::formatValue($value, $field, $model);
    }
}
