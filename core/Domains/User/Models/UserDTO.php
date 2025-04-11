<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Carbon\Carbon;
use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Helpers\DateTime\DateTimeHelper;

class UserDTO
{
    use TimestampsTrait;

    private ?int    $id            = null;
    private ?string $first_name    = null;
    private ?string $middle_name   = null;
    private ?string $last_name     = null;
    private ?string $email         = null;
    private ?string $phone         = null;
    private ?string $password      = null;
    private ?bool   $rememberToken = null;

    private ?AccountDTO    $account         = null;
    private ?RoleDTO       $role            = null;
    private ?Carbon        $emailVerifiedAt = null;
    private ?UserExDataDTO $exData          = null;

    public function __construct(
        private readonly ?User $user = null,
    )
    {
    }

    public function getModel(): ?User
    {
        return $this->user;
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
        return $this->first_name;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->first_name = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middle_name;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middle_name = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $lastName): static
    {
        $this->last_name = $lastName;

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

    public function setAccount(?AccountDTO $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getAccount(bool $load = false): ?AccountDTO
    {
        if ($load && ! $this->account) {
            $result = AccountLocator::AccountService()->getByUserId($this->getId());
            $this->setAccount($result);
        }

        return $this->account;
    }

    public function setRole(?RoleDTO $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(bool $load = false): ?RoleDTO
    {
        if ($load && ! $this->role) {
            $result = RoleLocator::RoleService()->getByUserId($this->getId());
            $this->setRole($result);
        }

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

    public function getExData(): UserExDataDTO
    {
        $this->exData = $this->exData ? : new UserExDataDTO();

        return $this->exData;
    }

    public function setExData(?UserExDataDTO $exData): static
    {
        $this->exData = $exData;

        return $this;
    }
}
