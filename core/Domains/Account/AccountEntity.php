<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Carbon\Carbon;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Exception;

class AccountEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?int $size = null;
    private ?float $balance = null;
    private ?bool $isVerified = null;
    private ?string $number = null;
    private ?int $primaryUserId = null;
    private ?bool $isInvoicing = null;
    private ?string $sortValue = null;
    private ?float $fraction = null;
    private ?Carbon $ownerDate = null;
    private ?AccountExDataEntity $exData = null;
    private ?UserCollection $users = null;

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): static { $this->id = $id; return $this; }
    public function getNumber(): ?string { return $this->number; }
    public function setNumber(?string $number): static { $this->number = $number; return $this; }
    public function getSize(): ?int { return $this->size; }
    public function setSize(?int $size): static { $this->size = $size; return $this; }
    public function getBalance(): ?float { return $this->balance; }
    public function setBalance(?float $balance): static { $this->balance = $balance; return $this; }
    public function isVerified(): ?bool { return $this->isVerified; }
    public function setIsVerified(?bool $isVerified): static { $this->isVerified = $isVerified; return $this; }
    public function getPrimaryUserId(): ?int { return $this->primaryUserId; }
    public function setPrimaryUserId(?int $primaryUserId): static { $this->primaryUserId = $primaryUserId; return $this; }
    public function isInvoicing(): bool { return (bool) $this->isInvoicing; }
    public function setIsInvoicing(?bool $isInvoicing): static { $this->isInvoicing = $isInvoicing; return $this; }
    public function getFraction(): ?float { return $this->fraction; }
    public function setFraction(null|float|string $fraction): static { $this->fraction = is_string($fraction) ? (float) $fraction : $fraction; return $this; }
    public function getOwnerDate(): ?Carbon { return $this->ownerDate; }
    public function setOwnerDate(?Carbon $ownerDate): static { $this->ownerDate = $ownerDate; return $this; }
    public function getExData(): AccountExDataEntity { $this->exData ??= new AccountExDataEntity(); return $this->exData; }
    public function setExData(?AccountExDataEntity $exData): static { $this->exData = $exData; return $this; }

    public function addUser(UserEntity $user): static
    {
        $this->users ??= new UserCollection();
        $this->users->add($user);
        return $this;
    }

    public function setUsers(UserCollection|array|null $users): static
    {
        $this->users = is_array($users) ? new UserCollection($users) : $users;
        return $this;
    }

    public function getUsers(): UserCollection
    {
        return $this->users ?? new UserCollection();
    }

    public function hasUsers(): bool
    {
        return $this->users !== null;
    }

    public function isSnt(): bool
    {
        return $this->getId() === AccountIdEnum::SNT->value;
    }

    public function getSortValue(): ?string
    {
        return $this->sortValue ?: $this->normalizePlotNumber();
    }

    public function setSortValue(?string $sortValue): static
    {
        $this->sortValue = $sortValue;
        return $this;
    }

    public function getFractionPercent(): ?string
    {
        if ( ! $this->getFraction()) {
            return null;
        }

        $fractionPercent = $this->getFraction() * 100;
        if (floor($fractionPercent) === $fractionPercent || fmod($fractionPercent, 1) === 0.00) {
            $fractionPercent = number_format($fractionPercent);
        } else {
            $fractionPercent = number_format($fractionPercent, 2);
        }

        return "$fractionPercent%";
    }

    private function normalizePlotNumber(): string
    {
        try {
            $parts = explode('/', (string) $this->getNumber());
            if (empty($parts)) {
                return '0';
            }
            $dachaNumber = $parts[0];
            preg_match('/^(\d+)([А-Яа-я]*)/', $dachaNumber, $matches);
            $number = ($parts[1] ?? '') . str_pad($matches[1] ?? '0', 5, '0', STR_PAD_LEFT);
            $letter = $matches[2] ?? '';
            return $number . $letter;
        } catch (Exception) {
            return '';
        }
    }
}
