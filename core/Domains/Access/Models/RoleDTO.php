<?php declare(strict_types=1);

namespace Core\Domains\Access\Models;

use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\User\Collections\Users;
use Core\Domains\User\Models\UserDTO;

class RoleDTO implements \JsonSerializable
{
    private ?int   $id    = null;
    private ?Users $users = null;

    public function __construct()
    {
        $this->users = new Users();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): RoleDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getId() ? RoleIdEnum::from($this->getId())->name() : null;
    }

    public function is(?RoleIdEnum $roleId): bool
    {
        return $this->getId() === $roleId?->value;
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
        return [
            'id' => $this->id,
        ];
    }
}
