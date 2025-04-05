<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Repositories;

use App\Models\Billing\Claim;
use Core\Db\RepositoryTrait;

class ClaimRepository
{
    private const TABLE = Claim::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Claim::class;
    }

    public function getById(?int $id): ?Claim
    {
        /** @var ?Claim $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Claim $claim): Claim
    {
        $claim->save();

        return $claim;
    }
}
