<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

class InvoiceFactory
{
    public function makeDefault(): InvoiceEntity
    {
        return (new InvoiceEntity())
            ->setCost(0.0)
            ->setPaid(0.0)
            ->setAdvance(0.0)
            ->setDebt(0.0);
    }
}
