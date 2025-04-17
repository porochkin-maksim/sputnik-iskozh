<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Services;

use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Events\ClaimDeletedEvent;
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;
use Core\Domains\Billing\Claim\Factories\ClaimFactory;
use Core\Domains\Billing\Claim\Models\ClaimComparator;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\Repositories\ClaimRepository;
use Core\Domains\Billing\Claim\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Exception;
use Illuminate\Support\Facades\DB;

readonly class ClaimService
{
    public function __construct(
        private ClaimFactory    $claimFactory,
        private ClaimRepository $claimRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(ClaimDTO $claim): ClaimDTO
    {
        $model = $this->claimRepository->getById($claim->getId());
        if ($model) {
            $before = $this->claimFactory->makeDtoFromObject($model);
        }
        else {
            $before = new ClaimDTO();
        }

        $model   = $this->claimRepository->save($this->claimFactory->makeModelFromDto($claim, $model));
        $current = $this->claimFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $claim->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::INVOICE,
            $current->getInvoiceId(),
            HistoryType::CLAIM,
            $current->getId(),
            new ClaimComparator($current),
            new ClaimComparator($before),
        );

        if (
            ! $claim->getId()
            || $current->getCost() !== $before->getCost()
            || $current->getPayed() !== $before->getPayed()
        ) {
            ClaimsUpdatedEvent::dispatch($current->getInvoiceId());
        }

        return $current;
    }

    public function saveQuietly(ClaimDTO $claim): ClaimDTO
    {
        return $this->claimFactory->makeDtoFromObject(
            $this->claimRepository->save($this->claimFactory->makeModelFromDto(
                $claim, $this->claimRepository->getById($claim->getId())
            ))
        );
    }

    /**
     * @param ClaimCollection $claims
     */
    public function saveCollection(ClaimCollection $claims): ClaimCollection
    {
        $result = new ClaimCollection();
        foreach ($claims as $claim) {
            $result->add($this->saveQuietly($claim));
        }

        return $result;
    }

    public function search(ClaimSearcher $searcher): SearchResponse
    {
        $response = $this->claimRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new ClaimCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->claimFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?ClaimDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new ClaimSearcher();
        $searcher->setId($id);
        $result = $this->claimRepository->search($searcher)->getItems()->first();

        return $result ? $this->claimFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        DB::beginTransaction();
        try {
            $claim = $this->getById($id);

            if ( ! $claim) {
                return false;
            }

            $this->historyChangesService->writeToHistory(
                Event::DELETE,
                HistoryType::INVOICE,
                $claim->getInvoiceId(),
                HistoryType::CLAIM,
                $claim->getId(),
            );

            $result = $this->claimRepository->deleteById($id);

            ClaimDeletedEvent::dispatch($claim);

            DB::commit();

            return $result;
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
