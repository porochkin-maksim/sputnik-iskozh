<?php declare(strict_types=1);

namespace Core\Domains\Billing\TransactionToObject\Models;

use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;
use Core\Domains\Common\Traits\TimestampsTrait;

class TransactionToObjectDTO
{
    use TimestampsTrait;

    public ?TransactionObjectTypeEnum $type = null;

    public ?int $transaction_id = null;
    public ?int $reference_id   = null;

    public function getType(): ?TransactionObjectTypeEnum
    {
        return $this->type;
    }

    public function setType(?TransactionObjectTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTransactionId(): ?int
    {
        return $this->transaction_id;
    }

    public function setTransactionId(?int $transaction_id): static
    {
        $this->transaction_id = $transaction_id;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->reference_id;
    }

    public function setReferenceId(?int $reference_id): static
    {
        $this->reference_id = $reference_id;

        return $this;
    }
}