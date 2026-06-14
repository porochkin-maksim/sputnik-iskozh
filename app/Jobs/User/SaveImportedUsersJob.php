<?php declare(strict_types=1);

namespace App\Jobs\User;

use Core\App\User\Import\SaveImportedUsersCommand;
use Core\App\User\Import\SaveImportedUsersInput;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveImportedUsersJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $users,
    )
    {
    }

    public function handle(SaveImportedUsersCommand $command): void
    {
        $command->execute(new SaveImportedUsersInput($this->users));
    }
}
