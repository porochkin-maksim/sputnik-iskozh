<?php declare(strict_types=1);

namespace Core\Objects\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\News\Models\NewsSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const LIMIT = RequestArgumentsEnum::LIMIT;

    public function getLimit(): ?int
    {
        return $this->getIntOrNull(self::LIMIT);
    }

    public function dto(): NewsSearcher
    {
        $result = new NewsSearcher();
        $result->setLimit($this->getLimit());

        return $result;
    }
}
