<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

use App\Models\Billing\Claim;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class ClaimSearcher extends BaseSearcher
{
    public function setInvoiceId(?int $id): static
    {
        $this->addWhere(Claim::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setWithService(): static
    {
        $this->with[] = Claim::RELATION_SERVICE;

        return $this;
    }

    public function setName(string $string): static
    {
        $this->addWhere(Claim::NAME, SearcherInterface::EQUALS, $string);

        return $this;
    }

    public function setServiceId(?int $serviceId): static
    {
        $this->addWhere(Claim::SERVICE_ID, SearcherInterface::EQUALS, $serviceId);

        return $this;
    }
}
