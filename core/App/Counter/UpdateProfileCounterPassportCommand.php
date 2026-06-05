<?php declare(strict_types=1);

namespace Core\App\Counter;

use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Throwable;

readonly class UpdateProfileCounterPassportCommand
{
    public function __construct(
        private DbServiceInterface $dbService,
        private CounterService     $counterService,
        private FileService        $fileService,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function execute(int $counterId, int $accountId, ?UploadedFile $passportFile): void
    {
        if ($passportFile === null) {
            throw new ValidationException(['passportFile' => ['Загрузите скан паспорта счётчика']]);
        }

        $counter = $this->counterService->getById($counterId);

        if ($counter === null || $counter->getAccountId() !== $accountId) {
            abort(403);
        }

        $this->dbService->transaction(function () use ($counter, $passportFile) {
            $this->fileService->deleteRelatedFileForCounterPassport($counter->getId());
            $this->fileService->storePassportFile($passportFile, $counter->getId());
        });
    }
}
