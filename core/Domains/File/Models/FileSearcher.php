<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use App\Models\File\File;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\File\Enums\TypeEnum;

class FileSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setType(?TypeEnum $type): static
    {
        if ($type) {
            $this->addWhere(File::TYPE, SearcherInterface::EQUALS, $type->value);
        }
        else {
            $this->addWhere(File::TYPE, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setRelatedId(?int $relatedId): static
    {
        if ($relatedId) {
            $this->addWhere(File::RELATED_ID, SearcherInterface::EQUALS, $relatedId);
        }
        else {
            $this->addWhere(File::RELATED_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }

    public function setParentId(?int $parentId): static
    {
        if ($parentId) {
            $this->addWhere(File::PARENT_ID, SearcherInterface::EQUALS, $parentId);
        }
        else {
            $this->addWhere(File::PARENT_ID, SearcherInterface::IS_NULL);
        }

        return $this;
    }
}
