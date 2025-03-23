<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Repositories;

use App\Models\Billing\Period;
use Core\Db\RepositoryTrait;
use Core\Domains\Billing\Period\Collections\PeriodCollection;

class PeriodRepository
{
    private const TABLE = Period::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Period::class;
    }

    public function getById(?int $id): ?Period
    {
        /** @var ?Period $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Period $period): Period
    {
        $period->save();

        return $period;
    }
}
