<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Jobs\Billing\NotifyAboutNewUnverifiedPaymentJob;
use App\Models\Billing\Payment;
use App\Observers\AbstractObserver;
use Carbon\Carbon;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Database\Eloquent\Model;

class PaymentObserver extends AbstractObserver
{
    /**
     * @var Payment $item
     */
    public function creating(Model $item): void
    {
        $item->paid_at = $item->paid_at ?: Carbon::now();
    }

    /**
     * @var Payment $item
     */
    public function created(Model $item): void
    {
        parent::created($item);

        if (
            $item->invoice_id
            && $item->verified
            && $item->moderated
        ) {
            app(InvoiceService::class)->recalcInvoice($item->invoice_id);
        }
        else {
            dispatch(new NotifyAboutNewUnverifiedPaymentJob($item->id));
        }
    }

    /**
     * @var Payment $item
     */
    public function updated(Model $item): void
    {
        parent::updated($item);

        if (
            $item->invoice_id
            && $item->verified
            && $item->moderated
            && $item->getOriginal(Payment::COST) !== $item->getAttribute(Payment::COST)
        ) {
            app(InvoiceService::class)->recalcInvoice($item->invoice_id);
        }
    }

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
