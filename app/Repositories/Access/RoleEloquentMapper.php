<?php declare(strict_types=1);

namespace App\Repositories\Access;

use App\Models\Access\Role;
use App\Repositories\Shared\Relations\RoleUserRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Access\RoleCollection;
use Core\Domains\Access\RoleFactory;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class RoleEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly RoleFactory $roleFactory,
        private readonly RoleUserRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : Role::make())->fill([
            Role::NAME => $entity->getName(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = $this->roleFactory->makeDefault()
            ->setId($data->{Role::ID})
            ->setName($data->{Role::NAME})
            ->setCreatedAt($data->{Role::CREATED_AT})
            ->setUpdatedAt($data->{Role::UPDATED_AT})
        ;

        if (isset($data->getRelations()[Role::USERS])) {
            $result->setUsers($this->relationAssembler->makeUsers($data->getRelation(Role::USERS)));
        }

        if (isset($data->getRelations()[Role::PERMISSIONS])) {
            $result->setPermissions(array_map(
                static fn($permission) => $permission->permission,
                $data->getRelation(Role::PERMISSIONS)->all(),
            ));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new RoleCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
