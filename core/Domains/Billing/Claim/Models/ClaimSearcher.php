<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Models;

use App\Models\Billing\Claim;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class ClaimSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setInvoiceId(int $id): static
    {
        $this->addWhere(Claim::INVOICE_ID, SearcherInterface::EQUALS, $id);

        return $this;
    }

    public function setWithService(): static
    {
        $this->with[] = Claim::SERVICE;

        return $this;
    }

    public function setName(string $string): static
    {
        $this->addWhere(Claim::NAME, SearcherInterface::EQUALS, $string);

        return $this;
    }

    public function setServiceId(?int $debtServiceId): static
    {
        $this->addWhere(Claim::SERVICE_ID, SearcherInterface::EQUALS, $debtServiceId);

        return $this;
    }
}
