<?php declare(strict_types=1);

namespace Core\Objects\User\Repositories;

use App\Models\User;
use Core\Objects\User\Collections\Users;

interface UserRepositoryInterface
{
    public function getById(?int $id): ?User;

    /**
     * @param int[] $ids
     */
    public function getByIds(array $ids): Users;
}
