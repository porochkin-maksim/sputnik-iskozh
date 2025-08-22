<?php declare(strict_types=1);

namespace Core\Domains\Option\Services;

use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\Option\Collections\OptionCollection;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\ComparatorFactory;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Models\OptionDTO;
use Core\Domains\Option\Models\OptionSearcher;
use Core\Domains\Option\Repositories\OptionRepository;
use Core\Domains\Option\Responses\SearchResponse;

readonly class OptionService
{
    public function __construct(
        private OptionFactory         $optionFactory,
        private OptionRepository      $optionRepository,
        private HistoryChangesService $historyChangesService,
        private ComparatorFactory     $comparatorFactory,
    )
    {
    }

    public function save(OptionDTO $option): OptionDTO
    {
        $model = $this->optionRepository->getById($option->getId());
        if ($model) {
            $before = $this->optionFactory->makeDtoFromObject($model);
        }
        else {
            $before = new OptionDTO();
        }

        $model   = $this->optionRepository->save($this->optionFactory->makeModelFromDto($option, $model));
        $current = $this->optionFactory->makeDtoFromObject($model);

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

    public function search(OptionSearcher $searcher): SearchResponse
    {
        $response = $this->optionRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new OptionCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->optionFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?OptionDTO
    {
        if (!$id) {
            return null;
        }

        $searcher = new OptionSearcher();
        $searcher->setId($id);
        $result = $this->optionRepository->search($searcher)->getItems()->first();

        // Если опция не найдена в БД, но ID соответствует значению из OptionEnum,
        // создаем новый объект DTO на основе типа
        $type = OptionEnum::tryFrom($id);
        return $result 
            ? $this->optionFactory->makeDtoFromObject($result) 
            : ($type ? $this->optionFactory->makeByType($type) : null);
    }

    public function getByType(OptionEnum $type): OptionDTO
    {
        $option = $this->getById($type->value);

        if ($option === null) {
            $option = $this->optionFactory->makeByType($type);
        }

        return $option;
    }

    public function all(): SearchResponse
    {
        // Получаем все существующие опции из БД
        $searcher = new OptionSearcher();
        $searcher->setSortOrderProperty('id', 'DESC');
        $response = $this->search($searcher);
        
        // Создаем map существующих опций по id
        $existingOptions = [];
        foreach ($response->getItems() as $option) {
            $existingOptions[$option->getId()] = $option;
        }

        // Создаем коллекцию всех возможных опций
        $collection = new OptionCollection();
        foreach (OptionEnum::cases() as $case) {
            if (isset($existingOptions[$case->value])) {
                $collection->add($existingOptions[$case->value]);
            } else {
                $collection->add($this->optionFactory->makeByType($case));
            }
        }

        return (new SearchResponse())
            ->setItems($collection)
            ->setTotal(count($collection));
    }
}
