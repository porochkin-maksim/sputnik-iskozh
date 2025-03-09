<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Models;

use App\Models\Billing\Transaction;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class TransactionSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setInvoiceId(int $id): static
    {
        $this->addWhere(Transaction::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setWithService(): static
    {
        $this->with[] = Transaction::SERVICE;

        return $this;
    }
}
