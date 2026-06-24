<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Payment;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class PaymentObserver extends AbstractObserver
{
    protected function getPrimaryIdField(): ?string
    {
        return Payment::INVOICE_ID;
    }

    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::INVOICE;
    }

    protected function getReferenceIdField(): ?string
    {
        return Payment::ID;
    }

    protected function getReferenceHistoryType(): HistoryType
    {
        return HistoryType::PAYMENT;
    }

    protected function getPropertyTitles(): array
    {
        return Payment::PROPERTIES_TO_TITLES;
    }
}
