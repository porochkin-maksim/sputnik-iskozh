<?php declare(strict_types=1);

namespace App\Console\Commands\Accounts;

use Core\Domains\Account\AccountService;
use Core\Domains\Account\AccountSearcher;
use Illuminate\Console\Command;

class RefillAccountsSortValues extends Command
{
    protected $signature   = 'accounts:refill-sort-values';
    protected $description = 'go and resaves accounts sort values';

    public function __construct(
        private readonly AccountService $accountService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $accounts = $this->accountService->search(AccountSearcher::make()->setWithoutSntAccount())->getItems();
        foreach ($accounts as $account) {
            $this->accountService->save($account);
        }
    }
} 
