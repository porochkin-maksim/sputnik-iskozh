<?php declare(strict_types=1);

namespace Core\Domains\Poll\Repositories;

use App\Models\Polls\Answer;
use Core\Db\RepositoryTrait;
use Core\Domains\Poll\Collections\AnswerCollection;

class AnswerRepository
{
    private const TABLE = Answer::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Answer::class;
    }

    public function getById(?int $id): ?Answer
    {
        /** @var ?Answer $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): AnswerCollection
    {
        return new AnswerCollection($this->traitGetByIds($ids));
    }

    public function save(Answer $news): Answer
    {
        $news->save();

        return $news;
    }
}
