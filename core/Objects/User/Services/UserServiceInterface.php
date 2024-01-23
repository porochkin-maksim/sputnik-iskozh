<?php declare(strict_types=1);

namespace Core\Objects\User\Services;

use App\Models\User;
use Core\Objects\User\Collections\Users;
use Core\Objects\User\Models\UserDTO;

interface UserServiceInterface
{
    public function save(UserDTO $dto): User;

    public function getById(?int $id): ?User;

    /**
     * @param int[] $ids
     */
    public function getByIds(array $ids, bool $cache = false): Users;
}
