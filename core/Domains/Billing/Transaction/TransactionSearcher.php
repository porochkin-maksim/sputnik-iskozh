<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use App\Models\Billing\Transaction;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class TransactionSearcher extends BaseSearcher
{
    public function setPaymentId(?int $id): static
    {
        if ($id === null) {
            $this->addWhere(Transaction::PAYMENT_ID, SearcherInterface::IS_NULL);
        }
        else {
            $this->addWhere(Transaction::PAYMENT_ID, SearcherInterface::EQUALS, $id);
        }

        return $this;
    }

    public function setPaymentIds(array $ids): static
    {
        $this->addWhere(Transaction::PAYMENT_ID, SearcherInterface::IN, $ids);

        return $this;
    }

    public function setClaimId(?int $id): static
    {
        if ($id === null) {
            $this->addWhere(Transaction::CLAIM_ID, SearcherInterface::IS_NULL);
        }
        else {
            $this->addWhere(Transaction::CLAIM_ID, SearcherInterface::EQUALS, $id);
        }

        return $this;
    }

    public function setClaimIdNotNull(): static
    {
        $this->addWhere(Transaction::CLAIM_ID, SearcherInterface::IS_NOT_NULL);

        return $this;
    }

    public function setClaimIds(array $ids): static
    {
        $this->addWhere(Transaction::CLAIM_ID, SearcherInterface::IN, $ids);

        return $this;
    }

    public function setWithPayment(): static
    {
        $this->with[] = Transaction::PAYMENT;

        return $this;
    }

    public function setWithClaim(): static
    {
        $this->with[] = Transaction::CLAIM;
        $this->with[] = Transaction::CLAIM . '.' . \App\Models\Billing\Claim::RELATION_SERVICE;

        return $this;
    }
}
