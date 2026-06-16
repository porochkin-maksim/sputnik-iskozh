<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

readonly class TransactionFactory
{
    public function makeDefault(): TransactionEntity
    {
        return (new TransactionEntity())
            ->setCost(0.00);
    }
}
