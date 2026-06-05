<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use App\Models\Billing\Payment;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class PaymentSearcher extends BaseSearcher
{
    public function setInvoiceId(?int $id): static
    {
        if ($id === null) {
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

    public function setInvoiceIds(array $ids): static
    {
        $this->addWhere(Payment::INVOICE_ID, SearcherInterface::IN, $ids);

        return $this;
    }
}
