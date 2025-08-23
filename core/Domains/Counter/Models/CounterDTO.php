<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\Enums\CounterTypeEnum;

class CounterDTO
{
    use TimestampsTrait;

    private ?int             $id          = null;
    private ?CounterTypeEnum $type        = null;
    private ?int             $accountId   = null;
    private ?string          $number      = null;
    private ?bool            $isInvoicing = null;
    private ?int             $increment   = null;

    private ?AccountDTO               $account           = null;
    private ?CounterHistoryCollection $historyCollection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): CounterDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?CounterTypeEnum
    {
        return $this->type;
    }

    public function setType(?CounterTypeEnum $type): CounterDTO
    {
        $this->type = $type;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): CounterDTO
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): CounterDTO
    {
        $this->number = $number;

        return $this;
    }

    public function isInvoicing(): ?bool
    {
        return $this->isInvoicing;
    }

    public function setIsInvoicing(?bool $isInvoicing): CounterDTO
    {
        $this->isInvoicing = $isInvoicing;

        return $this;
    }

    public function getIncrement(): ?int
    {
        return $this->increment;
    }

    public function setIncrement(?int $increment): static
    {
        $this->increment = $increment;

        return $this;
    }

    public function setHistoryCollection(CounterHistoryCollection $historyCollection): static
    {
        $this->historyCollection = $historyCollection;

        return $this;
    }

    public function getHistoryCollection(): CounterHistoryCollection//
    {
        return $this->historyCollection ? : new CounterHistoryCollection();
    }

    public function setAccount(?AccountDTO $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getAccount(bool $eagerLoad = false): ?AccountDTO
    {
        if ( ! $this->account && $this->accountId) {
            if ($eagerLoad) {
                $this->account = AccountLocator::AccountService()->getById($this->accountId);
            }
        }

        return $this->account;
    }
}
