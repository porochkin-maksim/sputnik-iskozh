<?php declare(strict_types=1);

namespace App\Repositories\HelpDesk;

use App\Models\HelpDesk\Ticket;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use App\Repositories\User\UserEloquentMapper;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class TicketEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly TicketCategoryEloquentMapper $ticketCategoryEloquentMapper,
        private readonly TicketServiceEloquentMapper  $ticketServiceEloquentMapper,
        private readonly AccountEloquentMapper        $accountEloquentMapper,
        private readonly UserEloquentMapper           $userEloquentMapper,
        private readonly FileEloquentMapper           $fileEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : new Ticket())->fill([
            Ticket::USER_ID       => $entity->getUserId(),
            Ticket::ACCOUNT_ID    => $entity->getAccountId(),
            Ticket::TYPE          => $entity->getType(),
            Ticket::CATEGORY_ID   => $entity->getCategoryId(),
            Ticket::SERVICE_ID    => $entity->getServiceId(),
            Ticket::PRIORITY      => $entity->getPriority(),
            Ticket::STATUS        => $entity->getStatus(),
            Ticket::DESCRIPTION   => $entity->getDescription(),
            Ticket::RESULT        => $entity->getResult(),
            Ticket::CONTACT_NAME  => $entity->getContactName(),
            Ticket::CONTACT_PHONE => $entity->getContactPhone(),
            Ticket::CONTACT_EMAIL => $entity->getContactEmail(),
            Ticket::RESOLVED_AT   => $entity->getResolvedAt(),
        ]);
    }

    public function makeEntityFromRepositoryData($model): object
    {
        $result = (new TicketEntity())
            ->setId($model->{Ticket::ID})
            ->setUserId($model->{Ticket::USER_ID})
            ->setAccountId($model->{Ticket::ACCOUNT_ID})
            ->setType($model->{Ticket::TYPE})
            ->setCategoryId($model->{Ticket::CATEGORY_ID})
            ->setServiceId($model->{Ticket::SERVICE_ID})
            ->setPriority($model->{Ticket::PRIORITY})
            ->setStatus($model->{Ticket::STATUS})
            ->setDescription($model->{Ticket::DESCRIPTION})
            ->setResult($model->{Ticket::RESULT})
            ->setContactName($model->{Ticket::CONTACT_NAME})
            ->setContactPhone($model->{Ticket::CONTACT_PHONE})
            ->setContactEmail($model->{Ticket::CONTACT_EMAIL})
            ->setResolvedAt($model->{Ticket::RESOLVED_AT})
            ->setCreatedAt($model->{Ticket::CREATED_AT})
            ->setUpdatedAt($model->{Ticket::UPDATED_AT})
        ;

        if (isset($model->getRelations()[Ticket::RELATION_CATEGORY])) {
            $result->setCategory($this->ticketCategoryEloquentMapper->makeEntityFromRepositoryData($model->getRelation(Ticket::RELATION_CATEGORY)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_SERVICE])) {
            $result->setService($this->ticketServiceEloquentMapper->makeEntityFromRepositoryData($model->getRelation(Ticket::RELATION_SERVICE)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_ACCOUNT])) {
            $result->setAccount($this->accountEloquentMapper->makeEntityFromRepositoryData($model->getRelation(Ticket::RELATION_ACCOUNT)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_USER])) {
            $result->setUser($this->userEloquentMapper->makeEntityFromRepositoryData($model->getRelation(Ticket::RELATION_USER)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_FILES])) {
            $result->setFiles($this->fileEloquentMapper->makeEntityFromRepositoryDatas($model->getRelation(Ticket::RELATION_FILES))->toArray());
        }

        if (isset($model->getRelations()[Ticket::RELATION_RESULT_FILES])) {
            $result->setResultFiles($this->fileEloquentMapper->makeEntityFromRepositoryDatas($model->getRelation(Ticket::RELATION_RESULT_FILES))->toArray());
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new TicketCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
