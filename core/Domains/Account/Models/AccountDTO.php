<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Models\UserDTO;

class AccountDTO
{
    use TimestampsTrait;

    private ?int    $id              = null;
    private ?int    $size            = null;
    private ?float  $balance         = null;
    private ?bool   $is_verified     = null;
    private ?string $number          = null;
    private ?int    $primary_user_id = null;
    private ?bool   $is_member       = null;
    private ?bool           $is_manager = null;
    private ?UserCollection $users      = null;

    public function __construct()
    {
        $this->users = new UserCollection();
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(?float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->is_verified = $isVerified;

        return $this;
    }

    public function getPrimaryUserId(): ?int
    {
        return $this->primary_user_id;
    }

    public function setPrimaryUserId(?int $primaryUserId): static
    {
        $this->primary_user_id = $primaryUserId;

        return $this;
    }

    public function isMember(): bool
    {
        return (bool) $this->is_member;
    }

    public function setIsMember(?bool $isMember): static
    {
        $this->is_member = $isMember;

        return $this;
    }

    public function isManager(): bool
    {
        return (bool) $this->is_manager;
    }

    public function setIsManager(?bool $is_manager): static
    {
        $this->is_manager = $is_manager;

        return $this;
    }

    public function addUser(UserDTO $user): static
    {
        if ( ! $this->users) {
            $this->users = new UserCollection();
        }
        $this->users->add($user);

        return $this;
    }

    public function getUsers(): ?UserCollection
    {
        return $this->users;
    }
}
