<?php declare(strict_types=1);

namespace Core\Objects\File\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\File\Models\FileSearcher;
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
