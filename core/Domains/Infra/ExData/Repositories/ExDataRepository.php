<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Repositories;

use App\Models\Infra\ExData;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;

/**
 * @method ExData getById(?int $id, bool $cache = false)
 * @method ExData[] getByIds(array $ids, SearcherInterface $searcher = null)
 */
class ExDataRepository
{
    private const TABLE = ExData::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return ExData::class;
    }

    public function save(ExData $model): ExData
    {
        $model->save();

        return $model;
    }
} 