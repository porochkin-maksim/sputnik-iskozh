<?php declare(strict_types=1);

namespace Core\Domains\Option\Models;

use App\Models\Infra\Option;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Option\Enums\OptionEnum;

class OptionSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setType(OptionEnum $enum): static
    {
        $this->addWhere(Option::ID, SearcherInterface::EQUALS, $enum->value);

        return $this;
    }
}
