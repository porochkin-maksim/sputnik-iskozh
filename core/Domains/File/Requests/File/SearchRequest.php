<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use App\Models\File\File;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Models\FileSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const string PARENT_ID = 'parent_id';
    private const string LIMIT     = RequestArgumentsEnum::LIMIT;
    private const string SORT_BY   = RequestArgumentsEnum::SORT_BY;
    private const string SORT_DESC = RequestArgumentsEnum::SORT_DESC;

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

        if ($this->get(self::SORT_BY)) {
            $result->setSortOrderProperty($this->get(self::SORT_BY), $this->getBool(self::SORT_DESC) ? SearcherInterface::SORT_ORDER_DESC : SearcherInterface::SORT_ORDER_ASC);
        }

        return $result;
    }
}
