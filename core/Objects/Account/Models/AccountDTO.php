<?php declare(strict_types=1);

namespace Core\Objects\Account\Models;

use Core\Objects\Common\Traits\TimestampsTrait;
use Core\Objects\User\Collections\Users;
use Core\Objects\User\Models\UserDTO;

class AccountDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id            = null;
    private ?string $number        = null;
    private ?int    $primaryUserId = null;
    private ?bool   $isMember      = null;
    private ?bool   $isManager     = null;
    private ?Users   $users = null;

    public function __construct()
    {
        $this->users = new Users();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?int $id): AccountDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): AccountDTO
    {
        $this->number = $number;

        return $this;
    }

    public function getPrimaryUserId(): ?int
    {
        return $this->primaryUserId;
    }

    public function setPrimaryUserId(?int $primaryUserId): AccountDTO
    {
        $this->primaryUserId = $primaryUserId;

        return $this;
    }

    public function getIsMember(): bool
    {
        return (bool) $this->isMember;
    }

    public function setIsMember(?bool $isMember): AccountDTO
    {
        $this->isMember = $isMember;

        return $this;
    }

    public function getIsManager(): bool
    {
        return (bool) $this->isManager;
    }

    public function setIsManager(?bool $isManager): AccountDTO
    {
        $this->isManager = $isManager;

        return $this;
    }

    public function addUser(UserDTO $user): static
    {
        if ( ! $this->users) {
            $this->users = new Users();
        }
        $this->users->add($user);

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $dossier = new Dossier($this);

        return [
            'dossier'         => $dossier,
            'id'              => $this->id,
            'number'          => $this->number,
            'primary_user_id' => $this->primaryUserId,
            'is_member'       => $this->isMember,
            'is_manager'      => $this->isManager,
        ];
    }
}
