<?php declare(strict_types=1);

namespace Core\Domains\Counter\Repositories;

use App\Models\Counter\Counter;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;

/**
 * @method Counter getById(?int $id, bool $cache = false)
 * @method Counter[] getByIds(array $ids, SearcherInterface $searcher = null)
 */
class CounterRepository
{
    private const TABLE = Counter::TABLE;

    use RepositoryTrait;

    protected function modelClass(): string
    {
        return Counter::class;
    }

    public function save(Counter $object): Counter
    {
        $object->save();

        return $object;
    }
}
