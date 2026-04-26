<?php declare(strict_types=1);

namespace Core\Domains\Option;

use App\Models\Infra\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Repositories\SearcherInterface;
use Core\Repositories\SearcherTrait;

class OptionSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setType(OptionEnum $enum): static
    {
        $this->addWhere(Option::ID, SearcherInterface::EQUALS, $enum->value);

        return $this;
    }
}
