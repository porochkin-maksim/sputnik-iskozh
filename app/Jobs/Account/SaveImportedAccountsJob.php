<?php declare(strict_types=1);

namespace App\Jobs\Account;

use Core\App\Account\Import\SaveImportedAccountsCommand;
use Core\App\Account\Import\SaveImportedAccountsInput;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveImportedAccountsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $accounts,
    )
    {
    }

    public function handle(SaveImportedAccountsCommand $command): void
    {
        $command->execute(new SaveImportedAccountsInput($this->accounts));
    }
}
