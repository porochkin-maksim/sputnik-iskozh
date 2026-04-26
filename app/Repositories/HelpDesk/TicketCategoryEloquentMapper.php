<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class TicketCategoryEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : new TicketCategory())->fill([
            TicketCategory::TYPE       => $entity->getType(),
            TicketCategory::NAME       => $entity->getName(),
            TicketCategory::CODE       => $entity->getCode(),
            TicketCategory::SORT_ORDER => $entity->getSortOrder(),
            TicketCategory::IS_ACTIVE  => $entity->getIsActive(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = (new TicketCategoryEntity())
            ->setId($data->{TicketCategory::ID})
            ->setType($data->{TicketCategory::TYPE})
            ->setName($data->{TicketCategory::NAME})
            ->setCode($data->{TicketCategory::CODE})
            ->setSortOrder($data->{TicketCategory::SORT_ORDER})
            ->setIsActive($data->{TicketCategory::IS_ACTIVE})
            ->setCreatedAt($data->{TicketCategory::CREATED_AT})
            ->setUpdatedAt($data->{TicketCategory::UPDATED_AT})
        ;

        if (isset($data->getRelations()[TicketCategory::RELATION_SERVICES])) {
            $result->setServices(
                $this->ticketServiceMapper()->makeEntityFromRepositoryDatas($data->getRelation(TicketCategory::RELATION_SERVICES)),
            );
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new TicketCategoryCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }

    private function ticketServiceMapper(): TicketServiceEloquentMapper
    {
        return app(TicketServiceEloquentMapper::class);
    }
}
