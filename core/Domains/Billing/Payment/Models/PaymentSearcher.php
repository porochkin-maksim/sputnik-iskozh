<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Models;

use App\Models\Billing\Payment;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class PaymentSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setInvoiceId(?int $id): static
    {
        if (null === $id) {
            $this->addWhere(Payment::INVOICE_ID, SearcherInterface::IS_NULL);
        }
        else {
            $this->addWhere(Payment::INVOICE_ID, SearcherInterface::EQUALS, $id);
        }


        return $this;
    }

    public function setWithFiles(): static
    {
        $this->with[] = Payment::FILES;

        return $this;
    }

    public function withAccount(): static
    {
        $this->with[] = Payment::ACCOUNT;

        return $this;
    }
}
