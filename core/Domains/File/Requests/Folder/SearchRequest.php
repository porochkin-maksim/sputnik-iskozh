<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\Folder;

use App\Http\Requests\AbstractRequest;
use Core\Domains\File\Models\FolderSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const string LIMIT     = RequestArgumentsEnum::LIMIT;
    private const string PARENT_ID = 'parent_id';
    private const string UID       = 'uid';

    public function dto(): FolderSearcher
    {
        $result = new FolderSearcher();
        $result->setLimit($this->getIntOrNull(self::LIMIT));
        $result->setParentId($this->getIntOrNull(self::PARENT_ID));

        return $result;
    }

    public function getUid(): ?string
    {
        return $this->get(self::UID);
    }
}
