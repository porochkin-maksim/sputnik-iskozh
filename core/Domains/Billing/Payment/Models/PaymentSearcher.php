<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Models;

use App\Models\Billing\Payment;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class PaymentSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setInvoiceId(int $id): static
    {
        $this->addWhere(Payment::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }
}
