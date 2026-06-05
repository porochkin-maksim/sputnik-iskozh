<?php declare(strict_types=1);

namespace Core\Domains\Folders;

use App\Models\File\FolderModel;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class FolderSearcher extends BaseSearcher
{
    public function setParentId(?int $parentId): static
    {
        if ($parentId) {
            $this->addWhere(FolderModel::PARENT_ID, SearcherInterface::EQUALS, $parentId);
        }
        else {
            $this->addWhere(FolderModel::PARENT_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setUid(string $uid): static
    {
        $this->addWhere(FolderModel::UID, SearcherInterface::EQUALS, $uid);

        return $this;
    }
}
