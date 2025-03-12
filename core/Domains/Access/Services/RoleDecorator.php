<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Models\RoleDTO;

class RoleDecorator
{
    public function __construct(
        private readonly ?RoleDTO $role,
    )
    {
    }

    public function canEditTemplates(): bool
    {
        return $this->can(...PermissionEnum::cases());
    }

    public function can(PermissionEnum ...$permissions): bool
    {
        $result = true;
        foreach ($permissions as $permission) {
            if ( ! $result) {
                break;
            }
            $result = $this->role->hasPermission($permission);
        }

        return $result;
    }

    public function canRoles(): bool
    {
        $actions = [
            PermissionEnum::ROLES_VIEW,
            PermissionEnum::ROLES_EDIT,
            PermissionEnum::ROLES_CREATE,
            PermissionEnum::ROLES_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canUsers(): bool
    {
        $actions = [
            PermissionEnum::USERS_VIEW,
            PermissionEnum::USERS_EDIT,
            PermissionEnum::USERS_CREATE,
            PermissionEnum::USERS_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canNews(): bool
    {
        $actions = [
            PermissionEnum::NEWS_VIEW,
            PermissionEnum::NEWS_EDIT,
            PermissionEnum::NEWS_CREATE,
            PermissionEnum::NEWS_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canFiles(): bool
    {
        $actions = [
            PermissionEnum::FILES_VIEW,
            PermissionEnum::FILES_EDIT,
            PermissionEnum::FILES_CREATE,
            PermissionEnum::FILES_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canServices(): bool
    {
        $actions = [
            PermissionEnum::SERVICES_VIEW,
            PermissionEnum::SERVICES_EDIT,
            PermissionEnum::SERVICES_CREATE,
            PermissionEnum::SERVICES_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canPeriods(): bool
    {
        $actions = [
            PermissionEnum::PERIODS_VIEW,
            PermissionEnum::PERIODS_EDIT,
            PermissionEnum::PERIODS_CREATE,
            PermissionEnum::PERIODS_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canAccounts(): bool
    {
        $actions = [
            PermissionEnum::ACCOUNTS_VIEW,
            PermissionEnum::ACCOUNTS_EDIT,
            PermissionEnum::ACCOUNTS_CREATE,
            PermissionEnum::ACCOUNTS_DROP,
        ];

        return $this->can(...$actions);
    }

    public function canInvoices(): bool
    {
        $actions = [
            PermissionEnum::INVOICES_VIEW,
            PermissionEnum::INVOICES_EDIT,
            PermissionEnum::INVOICES_CREATE,
            PermissionEnum::INVOICES_DROP,
        ];

        return $this->can(...$actions);
    }
}
