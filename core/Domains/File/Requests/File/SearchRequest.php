<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use Core\Domains\File\Models\FileSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const LIMIT = RequestArgumentsEnum::LIMIT;

    public function dto(): FileSearcher
    {
        $result = new FileSearcher();
        $result->setLimit($this->getIntOrNull(self::LIMIT));

        return $result;
    }
}
