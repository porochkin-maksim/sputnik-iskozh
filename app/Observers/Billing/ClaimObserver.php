<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Claim;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class ClaimObserver extends AbstractObserver
{
    protected function getPrimaryIdField(): ?string
    {
        return Claim::INVOICE_ID;
    }

    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::INVOICE;
    }

    protected function getReferenceIdField(): ?string
    {
        return Claim::ID;
    }

    protected function getReferenceHistoryType(): HistoryType
    {
        return HistoryType::CLAIM;
    }

    protected function getPropertyTitles(): array
    {
        return Claim::PROPERTIES_TO_TITLES;
    }
}
