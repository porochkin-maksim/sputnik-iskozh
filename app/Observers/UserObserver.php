<?php declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Core\Domains\HistoryChanges\HistoryType;

class UserObserver extends AbstractObserver
{
    public function updating(User $user): void
    {
        if ($user->email !== $user->getOriginal(User::EMAIL)) {
            $user->forceFill([User::EMAIL_VERIFIED_AT => null]);
        }
    }

    protected function getPropertyTitles(): array
    {
        return User::PROPERTIES_TO_TITLES;
    }

    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::USER;
    }
}
