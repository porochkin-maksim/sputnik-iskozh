<?php declare(strict_types=1);

namespace Core\Domains\Option\Services;

use Core\Db\Searcher\Models\SearchResponse;
use Core\Domains\Option\Collections\Options;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Factories\TariffFactory;
use Core\Domains\Option\Models\OptionDTO;
use Core\Domains\Option\Models\OptionSearcher;
use Core\Domains\Option\Models\Tariff\Tariff;
use Core\Domains\Option\Repositories\OptionRepository;

readonly class OptionService
{
    public function __construct(
        private OptionRepository $optionRepository,
        private OptionFactory    $optionFactory,
        private TariffFactory    $tariffFactory,
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

    public function getById(int $id, ?OptionFactory $optionFactory = null): ?OptionDTO
    {
        $result = $this->optionRepository->getById($id);

        if ($result) {
            if ($optionFactory) {
                return $optionFactory->makeDtoFromObject($result);
            }
            else {
                return $this->optionFactory->makeDtoFromObject($result);
            }
        }
        return null;
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

    public function getByType(OptionEnum $type, OptionFactory $optionFactory = null): null|OptionDTO|Tariff
    {
        $option = $this->getById($type->value, $optionFactory);

        if ( ! $option) {
            $option = $this->optionFactory->make($type);
            $option = $this->save($option);
        }

        return $option;
    }

    public function getElectricTariff(): Tariff
    {
        $type = OptionEnum::ELECTRIC_TARIFF;
        return $this->getByType($type, $this->tariffFactory);
    }

    public function getSntElectricTariff(): Tariff
    {
        $type = OptionEnum::ELECTRIC_SNT_TARIFF;
        return $this->getByType($type, $this->tariffFactory);
    }

    public function getMembershipFee(): Tariff
    {
        $type = OptionEnum::MEMBERSHIP_FEE;
        return $this->getByType($type, $this->tariffFactory);
    }

    public function getGarbageCollectionFee(): Tariff
    {
        $type = OptionEnum::GARBAGE_COLLECTION_FEE;
        return $this->getByType($type, $this->tariffFactory);
    }

    public function getRoadCollectionFee(): Tariff
    {
        $type = OptionEnum::ROAD_REPAIR_FEE;
        return $this->getByType($type, $this->tariffFactory);
    }
}
