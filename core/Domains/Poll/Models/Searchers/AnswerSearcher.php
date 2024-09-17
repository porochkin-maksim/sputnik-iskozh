<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models\Searchers;

use App\Models\Polls\Answer;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class AnswerSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithQuestion(): static
    {
        $this->with[] = Answer::QUESTION;

        return $this;
    }
}
