<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use App\Models\HelpDesk\TicketCategory;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Illuminate\Database\Eloquent\Collection;

class TicketCategoryFactory
{
    public function makeDefault(): TicketCategoryDTO
    {
        return new TicketCategoryDTO()
            ->setIsActive(true)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setSortOrder(999)
        ;
    }

    public function makeDtoFromObject(TicketCategory $model): TicketCategoryDTO
    {
        $result = new TicketCategoryDTO()
            ->setId($model->{TicketCategory::ID})
            ->setType($model->{TicketCategory::TYPE})
            ->setName($model->{TicketCategory::NAME})
            ->setCode($model->{TicketCategory::CODE})
            ->setSortOrder($model->{TicketCategory::SORT_ORDER})
            ->setIsActive($model->{TicketCategory::IS_ACTIVE})
            ->setCreatedAt($model->{TicketCategory::CREATED_AT})
            ->setUpdatedAt($model->{TicketCategory::UPDATED_AT})
        ;

        if (isset($model->getRelations()[TicketCategory::RELATION_SERVICES])) {
            $result->setServices(HelpDeskServiceLocator::TicketServiceFactory()->makeDtoFromObjects($model->getRelation(TicketCategory::RELATION_SERVICES)));
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): Collection
    {
        $result = new Collection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }

    public function makeModelFromDto(TicketCategoryDTO $dto, ?TicketCategory $model): TicketCategory
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = new TicketCategory();
        }

        $result->fill([
            TicketCategory::TYPE       => $dto->getType(),
            TicketCategory::NAME       => $dto->getName(),
            TicketCategory::CODE       => $dto->getCode(),
            TicketCategory::SORT_ORDER => $dto->getSortOrder(),
            TicketCategory::IS_ACTIVE  => $dto->getIsActive(),
        ]);

        return $result;
    }
}
