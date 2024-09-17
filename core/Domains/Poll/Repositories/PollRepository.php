<?php declare(strict_types=1);

namespace Core\Domains\Poll\Repositories;

use App\Models\Polls\Poll;
use Core\Db\RepositoryTrait;
use Core\Domains\Poll\Collections\PollCollection;

class PollRepository
{
    private const TABLE = Poll::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Poll::class;
    }

    public function getById(?int $id): ?Poll
    {
        /** @var ?Poll $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): PollCollection
    {
        return new PollCollection($this->traitGetByIds($ids));
    }

    public function save(Poll $news): Poll
    {
        $news->save();

        return $news;
    }
}
