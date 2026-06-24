<?php declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Core\Domains\HistoryChanges\HistoryType;

class UserObserver extends AbstractObserver
{
    protected function getPropertyTitles(): array
    {
        return User::PROPERTIES_TO_TITLES;
    }

    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::USER;
    }
}
