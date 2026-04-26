<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\TicketComment;
use App\Repositories\User\UserEloquentMapper;
use Core\Domains\HelpDesk\Collection\TicketCommentCollection;
use Core\Domains\HelpDesk\Models\TicketCommentEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class TicketCommentEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly TicketEloquentMapper $ticketEloquentMapper,
        private readonly UserEloquentMapper   $userEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : new TicketComment())->fill([
            TicketComment::TICKET_ID   => $entity->getTicketId(),
            TicketComment::USER_ID     => $entity->getUserId(),
            TicketComment::COMMENT     => $entity->getComment(),
            TicketComment::IS_INTERNAL => $entity->getIsInternal(),
        ]);
    }

    public function makeEntityFromRepositoryData($model): object
    {
        $dto = (new TicketCommentEntity())
            ->setId($model->{TicketComment::ID})
            ->setTicketId($model->{TicketComment::TICKET_ID})
            ->setUserId($model->{TicketComment::USER_ID})
            ->setComment($model->{TicketComment::COMMENT})
            ->setIsInternal($model->{TicketComment::IS_INTERNAL})
            ->setCreatedAt($model->{TicketComment::CREATED_AT})
            ->setUpdatedAt($model->{TicketComment::UPDATED_AT})
        ;

        if (isset($model->getRelations()[TicketComment::RELATION_USER])) {
            $dto->setUser($this->userEloquentMapper->makeEntityFromRepositoryData($model->getRelation(TicketComment::RELATION_USER)));
        }

        if (isset($model->getRelations()[TicketComment::RELATION_TICKET])) {
            $dto->setTicket($this->ticketEloquentMapper->makeEntityFromRepositoryData($model->getRelation(TicketComment::RELATION_TICKET)));
        }

        return $dto;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new TicketCommentCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
