<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Carbon\Carbon;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class AddAdminCounterHistoryCommand
{
    public function __construct(
        private DbServiceInterface              $dbService,
        private CounterService                  $counterService,
        private CounterHistoryService           $counterHistoryService,
        private CounterHistoryFactory           $counterHistoryFactory,
        private FileService                     $fileService,
        private AddAdminCounterHistoryValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(
        ?int          $historyId,
        int           $counterId,
        Carbon        $date,
        int           $value,
        ?UploadedFile $file,
    ): void
    {
        $this->validator->validate($counterId, $value);

        $counter = $this->counterService->getById($counterId);
        $history = $historyId
            ? $this->counterHistoryService->getById($historyId)
            : $this->counterHistoryFactory->makeDefault();

        if ($counter === null || $history === null) {
            abort(404);
        }

        $this->dbService->transaction(function () use ($counter, $history, $date, $value, $file) {
            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());
            $history
                ->setPreviousId($lastHistory?->getId())
                ->setCounterId($counter->getId())
            ;

            $history = $history
                ->setDate($date)
                ->setIsVerified(true)
                ->setValue($value)
            ;

            $history = $this->counterHistoryService->save($history);

            if ($file !== null) {
                $this->fileService->deleteHistoryById($history->getId());
            }

            if ($file !== null) {
                $this->fileService->storeHistoryFile($file, $history->getId());
            }
        });
    }
}
