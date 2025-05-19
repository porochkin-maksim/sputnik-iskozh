<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedEvent
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
}
