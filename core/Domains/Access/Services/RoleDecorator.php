<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use Core\Domains\Access\Enums\RoleIdEnum;
use Core\Domains\Access\Models\RoleDTO;

readonly class RoleDecorator
{
    public function __construct(
        private ?RoleDTO $role,
    )
    {
    }

    public function canEditNews(): bool
    {
        if ($this->role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canEditFiles(): bool
    {
        if ($this->role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canEditReports(): bool
    {
        if ($this->role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canEditOptions(): bool
    {
        if ($this->role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }

    public function canEditTemplates(): bool
    {
        if ($this->role?->is(RoleIdEnum::ADMIN)) {
            return true;
        }

        return false;
    }
}
