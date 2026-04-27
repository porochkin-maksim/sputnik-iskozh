<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Models;

use App\Models\Billing\Acquiring;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;

class AcquiringSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setInvoiceId(int $id): static
    {
        $this->addWhere(Acquiring::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setPaymentId(int $id): static
    {
        $this->addWhere(Acquiring::PAYMENT_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setStatus(StatusEnum $status): static
    {
        $this->addWhere(Acquiring::STATUS, SearcherInterface::EQUALS, $status->value);

        return $this;
    }
}
