<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Common\Traits\TimestampsTrait;

class InvoiceEntity
{
    use TimestampsTrait;

    private ?int               $id        = null;
    private ?int               $periodId  = null;
    private ?int               $accountId = null;
    private ?InvoiceTypeEnum   $type      = null;
    private ?float             $cost      = null;
    private ?float             $paid      = null;
    private ?float             $advance   = null;
    private ?float             $debt      = null;
    private ?string            $comment   = null;
    private ?string            $name      = null;
    private ?ClaimCollection   $claims    = null;
    private ?PaymentCollection $payments  = null;
    private ?AccountEntity     $account   = null;
    private ?PeriodEntity      $period    = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPeriodId(): ?int
    {
        return $this->periodId;
    }

    public function setPeriodId(?int $periodId): static
    {
        $this->periodId = $periodId;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): static
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getType(): ?InvoiceTypeEnum
    {
        return $this->type;
    }

    public function setType(?InvoiceTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPaid(): ?float
    {
        return $this->paid;
    }

    public function setPaid(?float $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getAdvance(): ?float
    {
        return $this->advance;
    }

    public function setAdvance(?float $advance): static
    {
        $this->advance = $advance;

        return $this;
    }

    public function getDebt(): ?float
    {
        return $this->debt;
    }

    public function setDebt(?float $debt): static
    {
        $this->debt = $debt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDelta(): ?float
    {
        if ($this->getCost() === null || $this->getPaid() === null) {
            return null;
        }

        return $this->getCost() - $this->getPaid();
    }

    public function getClaims(): ?ClaimCollection
    {
        return $this->claims;
    }

    public function setClaims(?ClaimCollection $claims): static
    {
        $this->claims = $claims;

        return $this;
    }

    public function getPayments(): ?PaymentCollection
    {
        return $this->payments;
    }

    public function setPayments(?PaymentCollection $payments): static
    {
        $this->payments = $payments;

        return $this;
    }

    public function getAccount(): ?AccountEntity
    {
        return $this->account;
    }

    public function setAccount(?AccountEntity $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getPeriod(): ?PeriodEntity
    {
        return $this->period;
    }

    public function setPeriod(?PeriodEntity $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->getCost() === $this->getPaid();
    }
}
