<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use Carbon\Carbon;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class TransactionEntity
{
    private ?int           $id        = null;
    private ?int           $paymentId = null;
    private ?int           $claimId   = null;
    private ?float         $cost      = null;
    private ?Carbon        $createdAt = null;
    private ?PaymentEntity $payment   = null;
    private ?ClaimEntity   $claim     = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): static
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getClaimId(): ?int
    {
        return $this->claimId;
    }

    public function setClaimId(?int $claimId): static
    {
        $this->claimId = $claimId;

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

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(mixed $createdAt): static
    {
        $this->createdAt = DateTimeHelper::toCarbonOrNull($createdAt);

        return $this;
    }

    public function getPayment(): ?PaymentEntity
    {
        return $this->payment;
    }

    public function setPayment(?PaymentEntity $payment): static
    {
        $this->payment = $payment;

        return $this;
    }

    public function getClaim(): ?ClaimEntity
    {
        return $this->claim;
    }

    public function setClaim(?ClaimEntity $claim): static
    {
        $this->claim = $claim;

        return $this;
    }
}
