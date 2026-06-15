<?php declare(strict_types=1);

namespace Core\Domains\Billing\Events;

readonly class ImportPaymentData
{
    public function __construct(
        public int     $invoiceId,
        public float   $amount,
        public ?string $name = null,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            invoiceId: (int) ($data['invoice_id'] ?? 0),
            amount   : (float) ($data['amount'] ?? 0),
            name     : $data['name'] ?? null,
        );
    }
}
