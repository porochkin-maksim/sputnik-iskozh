<?php declare(strict_types=1);

namespace Core\App\Account\Import;

use Core\App\Account\SaveCommand;

readonly class SaveImportedAccountsCommand
{
    public function __construct(
        private SaveCommand $saveCommand,
    )
    {
    }

    public function execute(SaveImportedAccountsInput $input): void
    {
        foreach ($input->getAccounts() as $data) {
            $this->saveCommand->execute(
                id             : $data->id,
                number         : $data->number ?: null,
                isInvoicing    : false,
                size           : $data->size,
                cadastreNumber : $data->cadastreNumber,
            );
        }
    }
}
