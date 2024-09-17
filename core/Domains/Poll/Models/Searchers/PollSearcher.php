<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models\Searchers;

use App\Models\Polls\Poll;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class PollSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setWithQuestions(): static
    {
        $this->with[] = Poll::QUESTIONS;

        return $this;
    }
}
