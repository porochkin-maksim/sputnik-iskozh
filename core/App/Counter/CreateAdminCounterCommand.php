<?php declare(strict_types=1);

namespace Core\App\Counter;

use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Support\Facades\DB;

readonly class CreateAdminCounterCommand
{
    public function __construct(
        private AccountService        $accountService,
        private CounterService        $counterService,
        private CounterFactory        $counterFactory,
        private CounterHistoryService $counterHistoryService,
        private CounterHistoryFactory $counterHistoryFactory,
        private FileService           $fileService,
        private CreateAdminCounterValidator $validator,
    )
    {
    }

    public function execute(
        int           $accountId,
        bool          $isInvoicing,
        string        $number,
        int           $value,
        ?UploadedFile $historyFile,
        ?UploadedFile $passportFile,
    ): void
    {
        $this->validator->validate($number, $value, $historyFile);

        $account = $this->accountService->getById($accountId);

        if ($account === null) {
            abort(404);
        }

        DB::transaction(function () use ($account, $isInvoicing, $number, $value, $historyFile, $passportFile) {
            $counter = $this->counterFactory->makeDefault()
                ->setIsInvoicing($isInvoicing)
                ->setNumber($number)
                ->setAccountId($account->getId())
            ;

            $counter = $this->counterService->save($counter);

            $history = $this->counterHistoryFactory->makeDefault()
                ->setIsVerified(true)
                ->setCounterId($counter->getId())
                ->setValue($value)
            ;

            $history = $this->counterHistoryService->save($history);

            if ($historyFile !== null) {
                $this->fileService->storeHistoryFile($historyFile, $history->getId());
            }

            if ($passportFile !== null) {
                $this->fileService->storePassportFile($passportFile, $counter->getId());
            }
        });
    }
}
