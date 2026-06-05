<?php declare(strict_types=1);

namespace Core\Domains\Counter;

use Carbon\Carbon;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class CounterEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?CounterTypeEnum $type = null;
    private ?int $accountId = null;
    private ?string $number = null;
    private ?bool $isInvoicing = null;
    private ?int $increment = null;
    private ?Carbon $expireAt = null;
    private ?AccountEntity $account = null;
    private ?FileEntity $passportFile = null;
    private ?CounterHistoryCollection $historyCollection = null;

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): static { $this->id = $id; return $this; }
    public function getType(): ?CounterTypeEnum { return $this->type; }
    public function setType(?CounterTypeEnum $type): static { $this->type = $type; return $this; }
    public function getAccountId(): ?int { return $this->accountId; }
    public function setAccountId(?int $accountId): static { $this->accountId = $accountId; return $this; }
    public function getNumber(): ?string { return $this->number; }
    public function setNumber(?string $number): static { $this->number = $number; return $this; }
    public function isInvoicing(): ?bool { return $this->isInvoicing; }
    public function setIsInvoicing(?bool $isInvoicing): static { $this->isInvoicing = $isInvoicing; return $this; }
    public function getIncrement(): ?int { return $this->increment; }
    public function setIncrement(?int $increment): static { $this->increment = $increment; return $this; }
    public function setExpireAt(mixed $expireAt): static { $this->expireAt = DateTimeHelper::toCarbonOrNull($expireAt); return $this; }
    public function getExpireAt(): ?Carbon { return $this->expireAt; }
    public function setPassportFile(FileEntity $file): static { $this->passportFile = $file; return $this; }
    public function getPasportFile(): ?FileEntity { return $this->passportFile; }
    public function setHistoryCollection(CounterHistoryCollection $historyCollection): static { $this->historyCollection = $historyCollection; return $this; }
    public function getHistoryCollection(): CounterHistoryCollection { return $this->historyCollection ?: new CounterHistoryCollection(); }
    public function setAccount(?AccountEntity $account): static { $this->account = $account; return $this; }
    public function getAccount(): ?AccountEntity { return $this->account; }

    public function getUid(): ?string
    {
        return $this->getId() ? UidFacade::getUid(UidTypeEnum::COUNTER, $this->getId()) : null;
    }
}
