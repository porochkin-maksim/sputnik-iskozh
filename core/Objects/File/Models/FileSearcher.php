<?php declare(strict_types=1);

namespace Core\Objects\File\Models;

use App\Models\File;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Objects\File\Enums\TypeEnum;

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
}
