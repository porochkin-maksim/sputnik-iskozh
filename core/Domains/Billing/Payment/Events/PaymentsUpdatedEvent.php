<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentsUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public array $invoiceIds;

    public function __construct(
        int ...$invoiceIds,
    )
    {
        $this->invoiceIds = $invoiceIds;
    }

    public function __serialize(): array
    {
        return [
            'invoiceIds' => $this->invoiceIds,
        ];
    }

    public function hasSingleInvoice(): bool
    {
        return count($this->invoiceIds) === 1;
    }

    /**
     * @return int[]
     */
    public function getInvoiceIds(): array
    {
        return $this->invoiceIds;
    }
}
