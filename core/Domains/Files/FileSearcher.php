<?php declare(strict_types=1);

namespace Core\Domains\Files;

use App\Models\Files\FileModel;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class FileSearcher extends BaseSearcher
{
    public function setType(?FileTypeEnum $type): static
    {
        if ($type) {
            $this->addWhere(FileModel::TYPE, SearcherInterface::EQUALS, $type->value);
        }
        else {
            $this->addWhere(FileModel::TYPE, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setTypes(?FileTypeEnum ...$type): static
    {
        foreach ($type as $t) {
            $this->setType($t);
        }

        return $this;
    }

    public function setRelatedId(?int $relatedId): static
    {
        if ($relatedId) {
            $this->addWhere(FileModel::RELATED_ID, SearcherInterface::EQUALS, $relatedId);
        }
        else {
            $this->addWhere(FileModel::RELATED_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setParentId(?int $parentId): static
    {
        if ($parentId) {
            $this->addWhere(FileModel::PARENT_ID, SearcherInterface::EQUALS, $parentId);
        }
        else {
            $this->addWhere(FileModel::PARENT_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }
}
