<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class DeleteCounterHistoriesCommand
{
    public function __construct(
        private DbServiceInterface               $dbService,
        private CounterHistoryService            $counterHistoryService,
        private ConfirmCounterHistoriesValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(array $ids): void
    {
        $this->validator->validate($ids);

        $this->dbService->transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $this->counterHistoryService->deleteById($id);
            }
        });
    }
}
