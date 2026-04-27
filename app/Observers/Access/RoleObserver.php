<?php declare(strict_types=1);

namespace App\Observers\Access;

use App\Models\Access\Role;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class RoleObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::ROLE;
    }

    protected function getPropertyTitles(): array
    {
        return Role::PROPERTIES_TO_TITLES;
    }
}
