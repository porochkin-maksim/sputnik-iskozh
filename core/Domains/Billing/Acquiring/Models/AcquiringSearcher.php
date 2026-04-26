<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Models;

use App\Models\Billing\Acquiring;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class AcquiringSearcher extends BaseSearcher
{
    public function setInvoiceId(int $id): static
    {
        $this->addWhere(Acquiring::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setUserId(int $id): static
    {
        $this->addWhere(Acquiring::USER_ID, SearcherInterface::EQUALS, $id);

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
