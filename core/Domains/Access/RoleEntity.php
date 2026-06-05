<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Throwable;

class RoleEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?string $name = null;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function is(?RoleEnum $roleId): bool
    {
        return $this->getId() === $roleId?->value;
    }

    public function addUser(UserEntity $user): static
    {
        $this->users ??= new UserCollection();
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

    /**
     * @return PermissionEnum[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array<int|string|PermissionEnum>|null $codes
     */
    public function setPermissions(?array $codes): static
    {
        $permissions = [];
        foreach ($codes ?? [] as $code) {
            try {
                $permission = $code instanceof PermissionEnum
                    ? $code
                    : PermissionEnum::tryFrom((int) $code);

                if ($permission) {
                    $permissions[$permission->value] = $permission;
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
        return isset($this->permissions[$permission->value]) || in_array($permission, $this->permissions, true);
    }
}
