<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use App\Models\File\Folder;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class FolderSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setParentId(?int $parentId): static
    {
        if ($parentId) {
            $this->addWhere(Folder::PARENT_ID, SearcherInterface::EQUALS, $parentId);
        }
        else {
            $this->addWhere(Folder::PARENT_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setUid(string $uid): static
    {
        $this->addWhere(Folder::UID, SearcherInterface::EQUALS, $uid);

        return $this;
    }
}
