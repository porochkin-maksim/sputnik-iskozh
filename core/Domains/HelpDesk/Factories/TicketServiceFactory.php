<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use App\Models\HelpDesk\TicketService;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Illuminate\Database\Eloquent\Collection;

class TicketServiceFactory
{
    public function makeDefault(): TicketServiceDTO
    {
        return new TicketServiceDTO();
    }

    public function makeDtoFromObject(TicketService $model): TicketServiceDTO
    {
        $dto = new TicketServiceDTO()
            ->setId($model->{TicketService::ID})
            ->setCategoryId($model->{TicketService::CATEGORY_ID})
            ->setName($model->{TicketService::NAME})
            ->setCode($model->{TicketService::CODE})
            ->setSortOrder($model->{TicketService::SORT_ORDER})
            ->setIsActive($model->{TicketService::IS_ACTIVE})
            ->setCreatedAt($model->{TicketService::CREATED_AT})
            ->setUpdatedAt($model->{TicketService::UPDATED_AT})
        ;

        if (isset($model->getRelations()[TicketService::RELATION_CATEGORY])) {
            $categoryFactory = new TicketCategoryFactory();
            $dto->setCategory($categoryFactory->makeDtoFromObject($model->getRelation(TicketService::RELATION_CATEGORY)));
        }

        return $dto;
    }

    public function makeDtoFromObjects(array|Collection $models): TicketServiceCollection
    {
        $result = new TicketServiceCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }

    public function makeModelFromDto(TicketServiceDTO $dto, ?TicketService $model): TicketService
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = new TicketService();
        }

        $result->fill([
            TicketService::CATEGORY_ID => $dto->getCategoryId(),
            TicketService::NAME        => $dto->getName(),
            TicketService::CODE        => $dto->getCode(),
            TicketService::SORT_ORDER  => $dto->getSortOrder(),
            TicketService::IS_ACTIVE   => $dto->getIsActive(),
        ]);

        return $result;
    }
}
