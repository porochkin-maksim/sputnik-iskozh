<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use App\Jobs\CounterHistory\NotifyAboutNewUnverifiedCounterHistoryJob;
use App\Jobs\CounterHistory\RewatchCounterHistoryChainJob;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\HistoryChanges\LogData;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class CreatePublicCounterHistoryCommand
{
    public function __construct(
        private DbServiceInterface                  $dbService,
        private CounterService                      $counterService,
        private CounterFactory                      $counterFactory,
        private CounterHistoryService               $counterHistoryService,
        private CounterHistoryFactory               $counterHistoryFactory,
        private FileService                         $fileService,
        private HistoryChangesService               $historyChangesService,
        private AccountService                      $accountService,
        private CreatePublicCounterHistoryValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function execute(
        string        $accountNumber,
        ?int          $counterId,
        ?string       $counterNumber,
        int           $value,
        ?UploadedFile $file,
        string        $fullText,
    ): void
    {
        $this->validator->validate($accountNumber, $counterNumber, $value);

        if ( ! $file) {
            throw new ValidationException(['file' => ['Не передана фотография счётчика']]);
        }

        $history = null;

        $this->dbService->transaction(function () use (
            $accountNumber,
            $counterId,
            $counterNumber,
            $value,
            $file,
            $fullText,
            &$history,
        ) {
            $history = $this->counterHistoryFactory->makeDefault()->setValue($value);

            $account = $this->accountService->findByNumber($accountNumber);

            if ($account !== null) {
                $counters = $this->counterService->getByAccountId($account->getId());
                $counter  = null;

                if ($counterId) {
                    $counter = $counters->getById($counterId);
                }
                elseif ($counterNumber) {
                    $counter = $counters->filter(function (CounterEntity $counter) use ($counterNumber) {
                        return $counter->getNumber() === $counterNumber;
                    })->first();
                }

                $counter = $counter ?? $counters->getInvoicing()->first() ?? $counters->first();

                if ($counter === null) {
                    $counter = $this->counterFactory->makeDefault()
                        ->setAccountId($account->getId())
                        ->setNumber($counterNumber ?? $account->getNumber())
                    ;

                    $counter = $this->counterService->save($counter);
                }

                $history->setCounterId($counter->getId());
            }

            $history = $this->counterHistoryService->save($history);
            $this->fileService->storeHistoryFile($file, $history->getId());

            $historyChanges = $this->historyChangesService->makeHistory()
                ->setType(HistoryType::COUNTER)
                ->setReferenceType(HistoryType::COUNTER_HISTORY)
                ->setReferenceId($history->getId())
                ->setLog(new LogData(Event::COMMON, null, $fullText))
            ;

            $this->historyChangesService->save($historyChanges);
        });

        if ($history) {
            NotifyAboutNewUnverifiedCounterHistoryJob::dispatchIfNeeded($history->getId());
            if ($history->getCounterId()) {
                RewatchCounterHistoryChainJob::dispatchIfNeeded($history->getCounterId());
            }
        }
    }
}
