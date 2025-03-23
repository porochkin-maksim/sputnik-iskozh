<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Repositories;

use App\Models\Infra\HistoryChanges;
use Core\Db\RepositoryTrait;
use Core\Domains\Infra\HistoryChanges\Collections\HistoryChangesCollection;

class HistoryChangesRepository
{
    private const TABLE = HistoryChanges::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return HistoryChanges::class;
    }

    public function getById(?int $id): ?HistoryChanges
    {
        /** @var ?HistoryChanges $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): HistoryChangesCollection
    {
        return new HistoryChangesCollection($this->traitGetByIds($ids));
    }

    public function save(HistoryChanges $historyChanges): HistoryChanges
    {
        $historyChanges->save();

        return $historyChanges;
    }
}
