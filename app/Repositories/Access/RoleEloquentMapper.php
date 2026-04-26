<?php declare(strict_types=1);

namespace App\Repositories\Access;

use App\Models\Access\Role;
use App\Repositories\User\UserEloquentMapper;
use Core\Domains\Access\RoleCollection;
use Core\Domains\Access\RoleFactory;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class RoleEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly RoleFactory $roleFactory,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Role::make();

        return $result->fill([
            Role::NAME => $entity->getName(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = $this->roleFactory->makeDefault()
            ->setId($data->{Role::ID})
            ->setName($data->{Role::NAME})
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;

        if (isset($data->getRelations()[Role::USERS])) {
            foreach ($data->getRelation(Role::USERS) as $user) {
                $result->addUser(app(UserEloquentMapper::class)->makeEntityFromRepositoryData($user));
            }
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
