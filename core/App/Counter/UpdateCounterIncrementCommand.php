<?php declare(strict_types=1);

namespace Core\App\Counter;

use Core\Domains\Counter\CounterService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class UpdateCounterIncrementCommand
{
    public function __construct(
        private CounterService $counterService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $counterId, int $increment, int $accountId): void
    {
        $counter = $this->counterService->getById($counterId);

        if ($counter === null || $counter->getAccountId() !== $accountId) {
            return;
        }

        DB::transaction(function () use ($counter, $increment) {
            $counter->setIncrement($increment);
            $this->counterService->save($counter);
        });
    }
}
