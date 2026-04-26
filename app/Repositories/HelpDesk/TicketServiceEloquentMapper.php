<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketService;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class TicketServiceEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : new TicketService())->fill([
            TicketService::CATEGORY_ID => $entity->getCategoryId(),
            TicketService::NAME        => $entity->getName(),
            TicketService::CODE        => $entity->getCode(),
            TicketService::SORT_ORDER  => $entity->getSortOrder(),
            TicketService::IS_ACTIVE   => $entity->getIsActive(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $dto = (new TicketServiceEntity())
            ->setId($data->{TicketService::ID})
            ->setCategoryId($data->{TicketService::CATEGORY_ID})
            ->setName($data->{TicketService::NAME})
            ->setCode($data->{TicketService::CODE})
            ->setSortOrder($data->{TicketService::SORT_ORDER})
            ->setIsActive($data->{TicketService::IS_ACTIVE})
            ->setCreatedAt($data->{TicketService::CREATED_AT})
            ->setUpdatedAt($data->{TicketService::UPDATED_AT})
        ;

        if (isset($data->getRelations()[TicketService::RELATION_CATEGORY])) {
            $dto->setCategory(
                $this->ticketCategoryMapper()->makeEntityFromRepositoryData($data->getRelation(TicketService::RELATION_CATEGORY)),
            );
        }

        return $dto;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new TicketServiceCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }

    private function ticketCategoryMapper(): TicketCategoryEloquentMapper
    {
        return app(TicketCategoryEloquentMapper::class);
    }
}
