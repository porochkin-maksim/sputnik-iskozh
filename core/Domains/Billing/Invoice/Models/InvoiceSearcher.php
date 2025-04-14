<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Models;

use App\Models\Billing\Invoice;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;

class InvoiceSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithClaims(): static
    {
        $this->with[] = Invoice::CLAIMS;

        return $this;
    }

    public function setWithPayments(): static
    {
        $this->with[] = Invoice::PAYMENTS;

        return $this;
    }

    public function setPeriodId(int $periodId): static
    {
        $this->addWhere(Invoice::PERIOD_ID, SearcherInterface::EQUALS, $periodId);

        return $this;
    }

    public function setAccountId(?int $accountId): static
    {
        $this->addWhere(Invoice::ACCOUNT_ID, SearcherInterface::EQUALS, $accountId);

        return $this;
    }

    public function setAccountIds(array $ids): static
    {
        $this->addWhere(Invoice::ACCOUNT_ID, SearcherInterface::IN, $ids);

        return $this;
    }

    public function setType(null|int|InvoiceTypeEnum $type): static
    {
        $value = $type instanceof InvoiceTypeEnum ? $type->value : $type;
        $this->addWhere(Invoice::TYPE, SearcherInterface::EQUALS, $value);

        return $this;
    }

    public function setWithPeriod(): static
    {
        $this->with[] = Invoice::PERIOD;

        return $this;
    }

    public function setWithAccount(): static
    {
        $this->with[] = Invoice::ACCOUNT;

        return $this;
    }
}
