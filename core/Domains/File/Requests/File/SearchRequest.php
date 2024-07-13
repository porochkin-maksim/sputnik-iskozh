<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use App\Models\File\File;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Models\FileSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const LIMIT     = RequestArgumentsEnum::LIMIT;
    private const PARENT_ID = RequestArgumentsEnum::PARENT_ID;
    private const SORT_BY   = RequestArgumentsEnum::SORT_BY;
    private const SORT_DESC = RequestArgumentsEnum::SORT_DESC;

    public function dto(): FileSearcher
    {
        $result = new FileSearcher();
        $result->setLimit($this->getIntOrNull(self::LIMIT));

        if ($this->has(self::PARENT_ID)) {
            if ($this->getIntOrNull(self::PARENT_ID)) {
                $result->addWhere(File::PARENT_ID, SearcherInterface::EQUALS, $this->getIntOrNull(self::PARENT_ID));
            }
            else {
                $result->addWhere(File::PARENT_ID, SearcherInterface::IS_NULL);
            }
        }

        if ($this->getBool(self::SORT_DESC)) {
            $result->setSortOrderDesc();
        }

        if ($this->get(self::SORT_BY)) {
            $result->setSortOrderProperty($this->get(self::SORT_BY));
        }

        return $result;
    }
}
