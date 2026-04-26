<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory;

use Carbon\Carbon;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Files\FileEntity;

class CounterHistoryEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?int $counterId = null;
    private ?int $previousId = null;
    private ?float $value = null;
    private ?Carbon $date = null;
    private ?bool $isVerified = null;
    private ?float $previousValue = null;
    private ?FileEntity $file = null;
    private ?CounterEntity $counter = null;
    private ?ClaimEntity $claimDTO = null;
    private ?CounterHistoryEntity $previous = null;

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): static { $this->id = $id; return $this; }
    public function getCounterId(): ?int { return $this->counterId; }
    public function setCounterId(?int $counterId): static { $this->counterId = $counterId; return $this; }
    public function getPreviousId(): ?int { return $this->previousId; }
    public function setPreviousId(?int $previousId): static { $this->previousId = $previousId; return $this; }
    public function getValue(): ?float { return $this->value; }
    public function setValue(?float $value): static { $this->value = $value; return $this; }
    public function getDate(): ?Carbon { return $this->date ? clone $this->date : null; }
    public function setDate(?Carbon $date): static { $this->date = $date; return $this; }
    public function isVerified(): ?bool { return $this->isVerified; }
    public function setIsVerified(?bool $isVerified): static { $this->isVerified = $isVerified; return $this; }
    public function setPreviousValue(?float $value): static { $this->previousValue = $value; return $this; }
    public function getPreviousValue(): ?float { return $this->previousValue; }
    public function setFile(FileEntity $file): static { $this->file = $file; return $this; }
    public function getFile(): ?FileEntity { return $this->file; }
    public function setCounter(?CounterEntity $counter): static { $this->counter = $counter; return $this; }
    public function getCounter(): ?CounterEntity { return $this->counter; }
    public function getClaim(): ?ClaimEntity { return $this->claimDTO; }
    public function setClaim(?ClaimEntity $claimDTO): static { $this->claimDTO = $claimDTO; return $this; }
    public function setPrevious(?CounterHistoryEntity $previous): static { $this->previous = $previous; return $this; }
    public function getPrevious(): ?CounterHistoryEntity { return $this->previous; }

    public function getDelta(): ?float
    {
        return $this->getPreviousValue() ? ($this->getValue() - $this->getPreviousValue()) : null;
    }
}
