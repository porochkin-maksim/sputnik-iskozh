<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Claim;
use App\Observers\AbstractObserver;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Illuminate\Database\Eloquent\Model;

class ClaimObserver extends AbstractObserver
{
    /**
     * @var Claim $item
     */
    public function created(Model $item): void
    {
        parent::created($item);

        if (
            $item->getAttribute(Claim::COST) > 0
            || $item->getAttribute(Claim::PAID) > 0
        ) {
            InvoiceLocator::InvoiceService()->recalcInvoice($item->invoice_id);
        }
    }

    /**
     * @var Claim $item
     */
    public function updated(Model $item): void
    {
        parent::created($item);

        if (
            $item->getOriginal(Claim::COST) !== $item->getAttribute(Claim::COST)
            || $item->getOriginal(Claim::PAID) !== $item->getAttribute(Claim::PAID)
        ) {
            InvoiceLocator::InvoiceService()->recalcInvoice($item->invoice_id);
        }
    }

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
