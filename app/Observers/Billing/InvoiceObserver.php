<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Invoice;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class InvoiceObserver extends AbstractObserver
{
    protected function getHistoryType(): HistoryType
    {
        return HistoryType::INVOICE;
    }

    protected function getPropertyTitles(): array
    {
        return Invoice::PROPERTIES_TO_TITLES;
    }
}
