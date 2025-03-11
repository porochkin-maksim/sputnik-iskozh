<?php declare(strict_types=1);

namespace Core\Domains\Access\Models;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Models\UserDTO;
use Throwable;

class RoleDTO
{
    use TimestampsTrait;

    private ?int            $id    = null;
    private ?string         $name  = null;
    private ?UserCollection $users = null;

    /**
     * @var PermissionEnum[]
     */
    private array $permissions = [];

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

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function is(?RoleIdEnum $roleId): bool
    {
        return $this->getId() === $roleId?->value;
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

    public function setUsers(?UserCollection $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param int[] $codes
     */
    public function setPermissions(?array $codes): static
    {
        $permissions = [];
        foreach ($codes as $code) {
            try {
                $permission = PermissionEnum::tryFrom((int) $code);
                if ($permission) {
                    $permissions[] = $permission;
                }
            }
            catch (Throwable) {
            }
        }
        $this->permissions = $permissions;

        return $this;
    }

    public function hasPermission(PermissionEnum $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
