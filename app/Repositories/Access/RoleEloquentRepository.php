<?php declare(strict_types=1);

namespace App\Repositories\Access;

use App\Models\Access\Role;
use App\Models\Access\RoleToPermissions;
use App\Models\Access\RoleToUser;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Access\RoleCollection;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleRepositoryInterface;
use Core\Domains\Access\RoleSearchResponse;
use Core\Domains\Access\RoleSearcher;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use Illuminate\Support\Facades\DB;
use ReturnTypeWillChange;

class RoleEloquentRepository implements RoleRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly RoleEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Role::class;
    }

    protected function getTable(): string
    {
        return Role::TABLE;
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function getEmptyCollection(): Collection
    {
        return new RoleCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return RoleSearchResponse
     */
    protected function getEmptySearchResponse(): RoleSearchResponse
    {
        return new RoleSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return RoleSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new RoleSearcher();
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
