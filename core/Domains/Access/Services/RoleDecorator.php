<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\Access\Models\RoleDTO;

readonly class RoleDecorator
{
    public function __construct(
        private ?RoleDTO $role,
    )
    {
    }

    private function isAdmin(): bool
    {
        return $this->role?->is(RoleIdEnum::ADMIN);
    }

    public function canEditNews(): bool
    {
        return $this->isAdmin();
    }

    public function canEditFiles(): bool
    {
        return $this->isAdmin();
    }

    public function canEditReports(): bool
    {
        return $this->isAdmin();
    }

    public function canEditOptions(): bool
    {
        return $this->isAdmin();
    }

    public function canEditTemplates(): bool
    {
        return $this->isAdmin();
    }

    public function canEditServices(): bool
    {
        return $this->isAdmin();
    }

    public function canEditPeriods(): bool
    {
        return $this->isAdmin();
    }

    public function canEditAccounts(): bool
    {
        return $this->isAdmin();
    }

    public function canEditInvoices(): bool
    {
        return $this->isAdmin();
    }

    public function canEditRoles(): bool
    {
        return $this->isAdmin();
    }

    public function canRoles(PermissionEnum $permission): bool
    {
        return $this->isAdmin() || $this->role?->hasPermission($permission);
    }
}
