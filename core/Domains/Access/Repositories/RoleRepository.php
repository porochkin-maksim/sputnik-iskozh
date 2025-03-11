<?php declare(strict_types=1);

namespace Core\Domains\Access\Repositories;

use App\Models\Access\Role;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\Models\SearchResponse;
use Core\Db\Searcher\SearcherInterface;

class RoleRepository
{
    private const TABLE = Role::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
        search as traitSearch;
    }

    public function __construct(
        private readonly RoleToUserRepository       $roleToUserRepository,
        private readonly RoleToPermissionRepository $roleToPermissionRepository,
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
        if ($result) {
            $result->permissions = $this->roleToPermissionRepository->getPermissionsByRoleId($id);
        }

        return $result;
    }

    public function search(SearcherInterface $searcher): SearchResponse
    {
        $result = $this->traitSearch($searcher);

        $items = $result->getItems();
        $permissions = $this->roleToPermissionRepository->getPermissionsByRoleIds($result->getIds());

        $result->setItems($items->map(static function (Role $role) use ($permissions) {
            $role->permissions = $permissions[$role->id] ?? [];

            return $role;
        }));

        return $result;
    }

    public function save(Role $role): Role
    {
        $role->save();
        $this->roleToPermissionRepository->saveRolesPermissions($role->id, $role->permissions);

        return $role;
    }

    /**
     * @return  int[]
     */
    public function getPermissionsByRoleId(int $id): array
    {
        return $this->roleToPermissionRepository->getPermissionsByRoleId($id);
    }

    public function getByUserId(int $id): ?Role
    {
        $id = $this->roleToUserRepository->getRoleIdByUserId($id);

        return Role::select()->with('users')->where('id', SearcherInterface::EQUALS, $id)->first();
    }
}
