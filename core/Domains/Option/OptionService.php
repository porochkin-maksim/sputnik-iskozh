<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Option\Enums\OptionEnum;

readonly class OptionService
{
    public function __construct(
        private OptionRepositoryInterface $optionRepository,
        private HistoryChangesService $historyChangesService,
        private ComparatorFactory $comparatorFactory,
        private OptionFactory $optionFactory,
    )
    {
    }

    public function save(OptionEntity $option): OptionEntity
    {
        $before = $option->getId() ? $this->optionRepository->getById($option->getId()) : null;
        $before ??= new OptionEntity();

        $current = $this->optionRepository->save($option);

        $this->historyChangesService->writeToHistory(
            $option->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::OPTION,
            $current->getId(),
            null,
            null,
            $this->comparatorFactory->createComparator($current),
            $this->comparatorFactory->createComparator($before),
        );

        return $current;
    }

    public function search(OptionSearcher $searcher): OptionSearchResponse
    {
        return $this->optionRepository->search($searcher);
    }

    public function getById(?int $id): ?OptionEntity
    {
        if ( ! $id) {
            return null;
        }

        $option = $this->optionRepository->getById($id);
        if ($option) {
            return $option;
        }

        $type = OptionEnum::tryFrom($id);

        return $type ? $this->optionFactory->makeByType($type) : null;
    }

    public function getByType(OptionEnum $type): OptionEntity
    {
        return $this->getById($type->value) ?? $this->optionFactory->makeByType($type);
    }

    public function all(): OptionSearchResponse
    {
        $response = $this->search(
            (new OptionSearcher())
                ->setSortOrderProperty('id', 'DESC'),
        );

        $existingOptions = [];
        foreach ($response->getItems() as $option) {
            $existingOptions[$option->getId()] = $option;
        }

        $collection = new OptionCollection();
        foreach (OptionEnum::cases() as $case) {
            $collection->add($existingOptions[$case->value] ?? $this->optionFactory->makeByType($case));
        }

        return (new OptionSearchResponse())
            ->setItems($collection)
            ->setTotal($collection->count());
    }
}
