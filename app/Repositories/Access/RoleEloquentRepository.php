<?php declare(strict_types=1);

namespace App\Repositories\Access;

use App\Models\Access\Role;
use App\Models\Access\RoleToPermissions;
use App\Models\Access\RoleToUser;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Access\RoleCollection;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleRepositoryInterface;
use Core\Domains\Access\RoleSearcher;
use Core\Domains\Access\RoleSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;
use Illuminate\Support\Facades\DB;

class RoleEloquentRepository implements RoleRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly RoleEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Role::class,
            table              : Role::TABLE,
            collectionClass    : RoleCollection::class,
            searchResponseClass: RoleSearchResponse::class,
            searcherClass      : RoleSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): RoleSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(RoleEntity $role): RoleEntity
    {
        /** @var Role|null $model */
        $model = $this->getModelById($role->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($role, $model);
        $model->save();

        DB::table(RoleToPermissions::TABLE)->where(RoleToPermissions::ROLE, $model->id)->delete();

        $permissions = array_map(
            static fn($permission) => [
                RoleToPermissions::ROLE => $model->id,
                RoleToPermissions::PERMISSION => $permission->value,
            ],
            $role->getPermissions(),
        );

        if ($permissions) {
            DB::table(RoleToPermissions::TABLE)->insert($permissions);
        }

        return $this->getById($model->id);
    }

    public function getById(?int $id): ?RoleEntity
    {
        /** @var Role|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): RoleSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function getByUserId(int $id): ?RoleEntity
    {
        $roleId = DB::table(RoleToUser::TABLE)
            ->select(RoleToUser::ROLE)
            ->where(RoleToUser::USER, SearcherInterface::EQUALS, $id)
            ->groupBy(RoleToUser::ROLE)
            ->pluck(RoleToUser::ROLE)
            ->first();

        return $this->getById($roleId);
    }
}
