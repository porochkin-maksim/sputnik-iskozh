<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use App\Jobs\Files\DeleteFilesJob;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectService;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Files\FileTypeEnum;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class DeleteCounterHistoriesCommand
{
    public function __construct(
        private DbServiceInterface               $dbService,
        private CounterHistoryService            $counterHistoryService,
        private ClaimToObjectService             $claimToObjectService,
        private ClaimService                     $claimService,
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

        $deleteFileIds = [];
        $this->dbService->transaction(function () use ($ids, &$deleteFileIds) {
            foreach ($ids as $id) {
                $claim = $this->claimToObjectService->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $id);
                if ($claim) {
                    $this->claimService->deleteById($claim->getId());
                }

                $deleteFileIds[] = $id;

                $this->counterHistoryService->deleteById($id);
            }
        });

        foreach ($deleteFileIds as $id) {
            DeleteFilesJob::dispatchIfNeeded($id, FileTypeEnum::COUNTER_HISTORY);
        }
    }
}
