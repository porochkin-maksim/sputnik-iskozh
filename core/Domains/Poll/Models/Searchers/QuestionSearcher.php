<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models\Searchers;

use App\Models\Polls\Question;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class QuestionSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithPoll(): static
    {
        $this->with[] = Question::POLL;

        return $this;
    }
}
