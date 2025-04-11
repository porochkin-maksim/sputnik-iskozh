<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Models;

use App\Models\Infra\ExData;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;

class ExDataSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setType(ExDataTypeEnum $type): static
    {
        $this->addWhere(ExData::TYPE, SearcherInterface::EQUALS, $type->value);

        return $this;
    }

    public function setReferenceId(int $referenceId): static
    {
        $this->addWhere(ExData::REFERENCE_ID, SearcherInterface::EQUALS, $referenceId);

        return $this;
    }
} 