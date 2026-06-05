<?php declare(strict_types=1);

namespace App\Observers\Account;

use App\Models\Account\Account;
use App\Models\User;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Database\Eloquent\Model;

class AccountObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::ACCOUNT;
    }

    protected function getPropertyTitles(): array
    {
        return Account::PROPERTIES_TO_TITLES;
    }

    /**
     * @param Account $model
     */
    protected function formatValue($value, string $field, Model $model): string
    {
        return match ($field) {
            Account::PRIMARY_USER_ID => User::find($value)?->name ?? parent::formatValue($value, $field, $model),
            default                  => parent::formatValue($value, $field, $model),
        };
    }
}
