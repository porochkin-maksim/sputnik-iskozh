<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Models;

use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Common\Traits\TimestampsTrait;

class InvoiceDTO
{
    use TimestampsTrait;

    private ?int             $id         = null;
    private ?int             $period_id  = null;
    private ?int             $account_id = null;
    private ?InvoiceTypeEnum $type       = null;
    private ?float           $cost       = null;
    private ?float           $payed      = null;
    private ?string          $comment    = null;

    private ?ClaimCollection   $claims     = null;
    private ?PaymentCollection $payments   = null;
    private ?AccountDTO        $accountDTO = null;
    private ?PeriodDTO         $periodDTO  = null;

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
        return $this->period_id;
    }

    public function setPeriodId(?int $period_id): static
    {
        $this->period_id = $period_id;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(?int $account_id): static
    {
        $this->account_id = $account_id;

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

    public function getPayed(): ?float
    {
        return $this->payed;
    }

    public function setPayed(?float $payed): static
    {
        $this->payed = $payed;

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

    // дополнительно

    public function getDelta(): ?float
    {
        if ($this->getCost() === null || $this->getPayed() === null) {
            return null;
        }

        return $this->getCost() - $this->getPayed();
    }

    public function getClaims(): ?ClaimCollection
    {
        return $this->claims;
    }

    public function setClaims(ClaimCollection $claims): static
    {
        $this->claims = $claims;

        return $this;
    }

    public function getPayments(): ?PaymentCollection
    {
        return $this->payments;
    }

    public function setPayments(?PaymentCollection $payments): InvoiceDTO
    {
        $this->payments = $payments;

        return $this;
    }

    public function getAccount(): ?AccountDTO
    {
        return $this->accountDTO;
    }

    public function setAccount(?AccountDTO $accountDTO): static
    {
        $this->accountDTO = $accountDTO;

        return $this;
    }

    public function getPeriod(): ?PeriodDTO
    {
        return $this->periodDTO;
    }

    public function setPeriod(?PeriodDTO $periodDTO): void
    {
        $this->periodDTO = $periodDTO;
    }

    // логика

    public function isPayed(): bool
    {
        return $this->getCost() === $this->getPayed();
    }
}
