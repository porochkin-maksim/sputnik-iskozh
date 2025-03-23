<?php declare(strict_types=1);

namespace Core\Domains\Counter\Repositories;

use App\Models\Counter\CounterHistory;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;

/**
 * @method CounterHistory getById(?int $id, bool $cache = false)
 * @method CounterHistory[] getByIds(array $ids, SearcherInterface $searcher = null)
 */
class CounterHistoryRepository
{
    private const TABLE = CounterHistory::TABLE;

    use RepositoryTrait;

    protected function modelClass(): string
    {
        return CounterHistory::class;
    }

    public function save(CounterHistory $object): CounterHistory
    {
        $object->save();

        return $object;
    }
}
