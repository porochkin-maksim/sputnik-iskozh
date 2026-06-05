<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Invoice;
use App\Observers\AbstractObserver;
use App\Jobs\Billing\CreateClaimsAndPaymentsForRegularInvoiceJob;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Database\Eloquent\Model;

class InvoiceObserver extends AbstractObserver
{
    /**
     * @var Invoice $item
     */
    public function created(Model $item): void
    {
        parent::created($item);

        CreateClaimsAndPaymentsForRegularInvoiceJob::dispatchIfNeeded($item->id);
    }

    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::INVOICE;
    }

    protected function getPropertyTitles(): array
    {
        return Invoice::PROPERTIES_TO_TITLES;
    }
}
