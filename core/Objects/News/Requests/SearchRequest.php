<?php declare(strict_types=1);

namespace Core\Objects\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\News\Models\NewsSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const LIMIT = RequestArgumentsEnum::LIMIT;
    private const SKIP  = RequestArgumentsEnum::SKIP;

    public function getLimit(): ?int
    {
        return $this->getIntOrNull(self::LIMIT);
    }

    public function getSkip(): ?int
    {
        return $this->getIntOrNull(self::SKIP);
    }

    public function dto(): NewsSearcher
    {
        $result = new NewsSearcher();
        $result->setLimit($this->getLimit())
            ->setOffset($this->getSkip());

        return $result;
    }
}
