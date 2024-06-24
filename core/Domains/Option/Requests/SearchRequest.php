<?php declare(strict_types=1);

namespace Core\Domains\Option\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\OptionSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const ALL = RequestArgumentsEnum::ALL;

    public function dto(): OptionSearcher
    {
        $result = new OptionSearcher();
        if ($this->searchAll()) {
            $result->setIds(OptionEnum::values());
        }

        return $result;
    }

    public function searchAll(): bool
    {
        return $this->getBool(self::ALL);
    }
}
