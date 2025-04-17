<?php declare(strict_types=1);

namespace App\Console\Commands\Accounts;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Illuminate\Console\Command;

class RefillAccountsSortValues extends Command
{
    protected $signature   = 'accounts:refill-sort-values';
    protected $description = 'go and resaves accounts sort values';

    public function handle(): void
    {
        $accounts = AccountLocator::AccountService()->search(AccountSearcher::make()->setWithoutSntAccount())->getItems();
        foreach ($accounts as $account) {
            AccountLocator::AccountService()->save($account);
        }
    }
} 