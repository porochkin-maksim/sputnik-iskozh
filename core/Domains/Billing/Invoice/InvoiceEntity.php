<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice;

use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Common\Traits\TimestampsTrait;

class InvoiceEntity
{
    use TimestampsTrait;

    private ?int               $id        = null;
    private ?int               $periodId  = null;
    private ?int               $accountId = null;
    private ?int               $serviceId = null;
    private ?InvoiceTypeEnum   $type      = null;
    private ?float             $cost      = null;
    private ?float             $paid      = null;
    private ?float             $debt      = null;
    private ?float             $rounding  = null;
    private ?string            $comment   = null;
    private ?string            $name      = null;
    private ?ClaimCollection   $claims    = null;
    private ?PaymentCollection $payments  = null;
    private ?AccountEntity     $account   = null;
    private ?PeriodEntity      $period    = null;
    private ?ServiceEntity     $service   = null;

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

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(?int $serviceId): static
    {
        $this->serviceId = $serviceId;

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

    public function getCost(): float
    {
        return (float) $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPaid(): float
    {
        return (float) $this->paid;
    }

    public function setPaid(?float $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getDebt(): float
    {
        return (float) $this->debt;
    }

    public function setDebt(?float $debt): static
    {
        $this->debt = $debt;

        return $this;
    }

    public function getRounding(): float
    {
        return (float) $this->rounding;
    }

    public function setRounding(?float $rounding): static
    {
        $this->rounding = $rounding;

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
        return $this->getCost() - $this->getRounding() - $this->getPaid();
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

    public function getService(): ?ServiceEntity
    {
        return $this->service;
    }

    public function setService(?ServiceEntity $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->getCost() - $this->getRounding() === $this->getPaid();
    }
}
