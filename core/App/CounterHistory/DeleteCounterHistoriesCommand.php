<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class DeleteCounterHistoriesCommand
{
    public function __construct(
        private CounterHistoryService $counterHistoryService,
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

        DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $this->counterHistoryService->deleteById($id);
            }
        });
    }
}
