<?php declare(strict_types=1);

namespace Core\Domains\User;

use Carbon\Carbon;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class UserEntity
{
    use TimestampsTrait;

    private ?int               $id                 = null;
    private ?string            $firstName          = null;
    private ?string            $middleName         = null;
    private ?string            $lastName           = null;
    private ?string            $email              = null;
    private ?string            $phone              = null;
    private ?string            $password           = null;
    private ?bool              $rememberToken      = null;
    private ?float             $fraction           = null;
    private ?Carbon            $ownerDate          = null;
    private ?Carbon            $deletedAt          = null;
    private ?string            $membershipDutyInfo = null;
    private ?Carbon            $membershipDate     = null;
    private ?int               $accountId          = null;
    private ?AccountEntity     $account            = null;
    private ?RoleEntity        $role               = null;
    private ?Carbon            $emailVerifiedAt    = null;
    private ?UserExDataEntity  $exData             = null;
    private ?AccountCollection $accounts           = null;

    public function getViewer(): UserViewer
    {
        return new UserViewer($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRememberToken(): ?bool
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?bool $rememberToken): static
    {
        $this->rememberToken = $rememberToken;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function setAccount(?AccountEntity $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getAccount(): ?AccountEntity
    {
        return $this->account;
    }

    public function setRole(?RoleEntity $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?RoleEntity
    {
        return $this->role;
    }

    public function setEmailVerifiedAt(mixed $emailVerifiedAt): static
    {
        $this->emailVerifiedAt = DateTimeHelper::toCarbonOrNull($emailVerifiedAt);

        return $this;
    }

    public function getEmailVerifiedAt(): ?Carbon
    {
        return $this->emailVerifiedAt;
    }

    public function getFraction(): ?float
    {
        return $this->fraction;
    }

    public function setFraction(null|float|string $fraction): static
    {
        $this->fraction = is_string($fraction) ? (float) $fraction : $fraction;

        return $this;
    }

    public function isDeleted(): ?Carbon
    {
        return $this->deletedAt;
    }

    public function setIsDeleted(mixed $deletedAt): static
    {
        $this->deletedAt = DateTimeHelper::toCarbonOrNull($deletedAt);

        return $this;
    }

    public function getFractionpercent(): ?string
    {
        if ($this->getFraction() === null) {
            return null;
        }

        $fractionPercent = $this->getFraction() * 100;
        if (floor($fractionPercent) === $fractionPercent || fmod($fractionPercent, 1) === 0.0) {
            $fractionPercent = number_format($fractionPercent);
        }
        else {
            $fractionPercent = number_format($fractionPercent, 2);
        }

        return "$fractionPercent%";
    }

    public function getOwnerDate(): ?Carbon
    {
        return $this->ownerDate;
    }

    public function setOwnerDate(?Carbon $ownerDate): static
    {
        $this->ownerDate = $ownerDate;

        return $this;
    }

    public function getExData(): UserExDataEntity
    {
        $this->exData ??= new UserExDataEntity();

        return $this->exData;
    }

    public function setExData(?UserExDataEntity $exData): static
    {
        $this->exData = $exData;

        return $this;
    }

    public function getMembershipDutyInfo(): ?string
    {
        return $this->membershipDutyInfo;
    }

    public function setMembershipDutyInfo(?string $membershipDutyInfo): static
    {
        $this->membershipDutyInfo = $membershipDutyInfo;

        return $this;
    }

    public function getMembershipDate(): ?Carbon
    {
        return $this->membershipDate;
    }

    public function setMembershipDate(mixed $membershipDate): static
    {
        $this->membershipDate = DateTimeHelper::toCarbonOrNull($membershipDate);

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

    /**
     * @return int[]
     */
    public function getAccountIds(): array
    {
        $result = $this->getAccounts()->getIds();

        $id = $this->getAccount()?->getId();
        if ($id) {
            $result[] = $id;
        }

        return array_unique($result);
    }

    public function setAccounts(AccountCollection|array $accounts): static
    {
        $this->accounts = is_array($accounts) ? new AccountCollection($accounts) : $accounts;

        return $this;
    }

    public function getAccounts(): AccountCollection
    {
        return $this->accounts ?? new AccountCollection();
    }

    public function isRealEmail(): bool
    {
        return $this->getEmailVerifiedAt() !== null;
    }
}
