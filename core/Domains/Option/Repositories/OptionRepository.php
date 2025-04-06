<?php declare(strict_types=1);

namespace Core\Domains\Option\Repositories;

use App\Models\Infra\Option;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;

/**
 * @method Option getById(?int $id, bool $cache = false)
 * @method Option[] getByIds(array $ids, SearcherInterface $searcher = null)
 */
class OptionRepository
{
    private const TABLE = Option::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Option::class;
    }

    public function save(Option $object): Option
    {
        $object->save();

        return $object;
    }
}
