<?php declare(strict_types=1);

namespace Core\Domains\Poll\Repositories;

use App\Models\Polls\Question;
use Core\Db\RepositoryTrait;
use Core\Domains\Poll\Collections\QuestionCollection;

class QuestionRepository
{
    private const TABLE = Question::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Question::class;
    }

    public function getById(?int $id): ?Question
    {
        /** @var ?Question $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): QuestionCollection
    {
        return new QuestionCollection($this->traitGetByIds($ids));
    }

    public function save(Question $news): Question
    {
        $news->save();

        return $news;
    }
}
