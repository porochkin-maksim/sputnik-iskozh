<?php declare(strict_types=1);

namespace Core\Domains\Counter\Services;

use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Counter\Repositories\CounterHistoryRepository;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class CounterHistoryService
{
    public function __construct(
        private CounterHistoryFactory    $counterHistoryFactory,
        private CounterHistoryRepository $counterHistoryRepository,
        private HistoryChangesService    $historyChangesService,
    )
    {
    }

    public function getById(?int $id): ?CounterHistoryDTO
    {
        if ($id) {
            $model = $this->counterHistoryRepository->getById($id);
            if ($model) {
                return $this->counterHistoryFactory->makeDtoFromObject($model);
            }
        }

        return null;
    }

    public function save(CounterHistoryDTO $counterHistory): CounterHistoryDTO
    {
        $model          = $this->counterHistoryRepository->save($this->counterHistoryFactory->makeModelFromDto($counterHistory));
        $counterHistory = $this->counterHistoryFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            Event::CREATE,
            HistoryType::COUNTER,
            $counterHistory->getCounterId(),
            HistoryType::COUNTER_HISTORY,
            $counterHistory->getId(),
            text: sprintf("Добавлено показание: %s", $counterHistory->getValue()),
        );

        return $counterHistory;
    }
}
