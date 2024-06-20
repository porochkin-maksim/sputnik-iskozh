<?php declare(strict_types=1);

namespace Core\Domains\Option\Services;

use Core\Db\Searcher\Models\SearchResponse;
use Core\Domains\Option\Collections\Options;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Models\OptionDTO;
use Core\Domains\Option\Models\OptionSearcher;
use Core\Domains\Option\Repositories\OptionRepository;

readonly class OptionService
{
    public function __construct(
        private OptionFactory    $optionFactory,
        private OptionRepository $optionRepository,
    )
    {
    }

    public function search(OptionSearcher $searcher): SearchResponse
    {
        $response = $this->optionRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new Options();
        foreach ($response->getItems() as $item) {
            $collection->add($this->optionFactory->makeDtoFromObject($item));
        }

        foreach (array_diff($searcher->getIds(), $collection->getIds()) as $id) {
            $option = OptionEnum::tryFrom($id);
            if ($option) {
                $collection->add($this->optionFactory->make($option));
            }
        }

        $result->setTotal(max($response->getTotal(), $collection->count()));

        return $result->setItems($collection);
    }

    public function getById(int $id): ?OptionDTO
    {
        $result = $this->optionRepository->getById($id);

        return $result ? $this->optionFactory->makeDtoFromObject($result) : null;
    }

    public function save(OptionDTO $dto): OptionDTO
    {
        $option = null;

        if ($dto->getId()) {
            $option = $this->optionRepository->getById($dto->getId());
        }

        $option = $this->optionFactory->makeModelFromDto($dto, $option);
        $option = $this->optionRepository->save($option);

        return $this->optionFactory->makeDtoFromObject($option);
    }

    public function getByType(OptionEnum $type): ?OptionDTO
    {
        $option = $this->getById($type->value);

        if ( ! $option) {
            $option = $this->optionFactory->make($type);
            $this->save($option);
        }

        return $this->getById($type->value);
    }

    public function getElectricTariff(): OptionDTO
    {
        $type = OptionEnum::ELECTRIC_TARIFF;
        return $this->getByType($type);
    }

    public function getSntElectricTariff(): OptionDTO
    {
        $type = OptionEnum::ELECTRIC_SNT_TARIFF;
        return $this->getByType($type);
    }

    public function getMembershipFee(): OptionDTO
    {
        $type = OptionEnum::MEMBERSHIP_FEE;
        return $this->getByType($type);
    }

    public function getGarbageCollectionFee(): OptionDTO
    {
        $type = OptionEnum::GARBAGE_COLLECTION_FEE;
        return $this->getByType($type);
    }

    public function getRoadCollectionFee(): OptionDTO
    {
        $type = OptionEnum::ROAD_REPAIR_FEE;
        return $this->getByType($type);
    }
}
