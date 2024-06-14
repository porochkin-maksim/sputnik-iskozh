<?php declare(strict_types=1);

namespace Core\Domains\Access\Repositories;

use App\Models\Access\Role;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Collections\Roles;
use Core\Domains\Access\Enums\UserToRole;
use Core\Domains\Access\Models\RolesSearcher;
use Illuminate\Support\Facades\DB;

class RoleRepository
{
    private const TABLE = Role::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    public function __construct(
        private readonly RoleToUserRepository $roleToUserRepository
    )
    {
    }

    protected function modelClass(): string
    {
        return Role::class;
    }

    public function getById(?int $id): ?Role
    {
        /** @var ?Role $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): Roles
    {
        return new Roles($this->traitGetByIds($ids));
    }

    public function save(Role $report): Role
    {
        $report->save();

        return $report;
    }

    /**
     * @return Role[]
     */
    public function search(RolesSearcher $searcher): array
    {
        $ids = DB::table(static::TABLE)
            ->pluck('id')
            ->toArray();

        return $this->traitGetByIds($ids, $searcher);
    }

    public function getByUserId(int $id): ?Role
    {
        $id = $this->roleToUserRepository->getRoleIdByUserId($id);

        return Role::select()->with('users')->where('id', SearcherInterface::EQUALS, $id)->first();
    }
}
