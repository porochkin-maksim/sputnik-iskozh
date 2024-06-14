<?php declare(strict_types=1);

namespace Core\Objects\Access\Services;

use Core\Objects\Access\Collections\Roles;
use Core\Objects\Access\Factories\RoleFactory;
use Core\Objects\Access\Models\RoleDTO;
use Core\Objects\Access\Models\RolesSearcher;
use Core\Objects\Access\Repositories\RoleRepository;

readonly class RoleService
{
    public function __construct(
        private RoleFactory    $roleFactory,
        private RoleRepository $roleRepository,
    )
    {
    }

    public function getByUserId(int|string|null $id): ?RoleDTO
    {
        $result = $this->roleFactory->makeForUserId($id);

        if ($result) {
            return $result;
        }

        $result = $this->roleRepository->getByUserId((int) $id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }

    public function search(RolesSearcher $searcher): Roles
    {
        $roles = $this->roleRepository->search($searcher);

        $result  = new Roles();
        foreach ($roles as $role) {
            $result->add($this->roleFactory->makeDtoFromObject($role));
        }

        return $result;
    }

    public function getById(int $id): ?RoleDTO
    {
        $result = $this->roleRepository->getById($id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }
}
