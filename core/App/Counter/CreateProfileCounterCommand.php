<?php declare(strict_types=1);

namespace Core\App\Counter;

use Carbon\Carbon;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class CreateProfileCounterCommand
{
    public function __construct(
        private CounterService        $counterService,
        private CounterFactory        $counterFactory,
        private CounterHistoryService $counterHistoryService,
        private CounterHistoryFactory $counterHistoryFactory,
        private FileService           $fileService,
        private CreateProfileCounterValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function execute(
        int           $accountId,
        string        $number,
        int           $increment,
        ?Carbon       $expireAt,
        int           $value,
        ?UploadedFile $historyFile,
        ?UploadedFile $passportFile,
    ): void
    {
        $this->validator->validate($number, $value, $historyFile);

        if ( ! $historyFile) {
            throw new ValidationException(['file' => ['Не передана фотография счётчика']]);
        }

        DB::transaction(function () use (
            $accountId,
            $number,
            $increment,
            $expireAt,
            $value,
            $historyFile,
            $passportFile,
        ) {
            $counter = $this->counterFactory->makeDefault()
                ->setNumber($number)
                ->setAccountId($accountId)
                ->setIncrement($increment)
                ->setExpireAt($expireAt)
            ;

            $counter = $this->counterService->save($counter);

            $history = $this->counterHistoryFactory->makeDefault()
                ->setCounterId($counter->getId())
                ->setValue($value)
            ;

            $history = $this->counterHistoryService->save($history);

            $this->fileService->storeHistoryFile($historyFile, $history->getId());

            if ($passportFile !== null) {
                $this->fileService->storePassportFile($passportFile, $counter->getId());
            }
        });
    }
}
