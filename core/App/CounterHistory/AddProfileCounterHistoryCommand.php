<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Counter\CounterService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class AddProfileCounterHistoryCommand
{
    public function __construct(
        private CounterService        $counterService,
        private CounterHistoryService $counterHistoryService,
        private CounterHistoryFactory $counterHistoryFactory,
        private FileService           $fileService,
        private AddProfileCounterHistoryValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $counterId, int $value, ?UploadedFile $file, int $accountId): bool
    {
        $this->validator->validate($counterId, $value, $file);

        $counter = $this->counterService->getById($counterId);

        if ($counter === null) {
            abort(404);
        }

        if ($counter->getAccountId() !== $accountId) {
            abort(403);
        }

        DB::transaction(function () use ($counter, $value, $file) {
            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());

            $history = $this->counterHistoryFactory->makeDefault()
                ->setPreviousId($lastHistory?->getId())
                ->setPreviousValue($lastHistory?->getValue())
                ->setCounterId($counter->getId())
                ->setValue($value)
            ;

            $history = $this->counterHistoryService->save($history);
            $this->fileService->storeHistoryFile($file, $history->getId());
        });

        return true;
    }
}
