<?php declare(strict_types=1);

namespace Core\Domains\Counter\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Counter\Enums\TypeEnum;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Requests\RequestArgumentsEnum;

class SearchRequest extends AbstractRequest
{
    private const ALL = RequestArgumentsEnum::ALL;

    public function dto(): CounterSearcher
    {
        $result = new CounterSearcher();
        if ($this->searchAll()) {
            $result->setIds(TypeEnum::values());
        }

        return $result;
    }

    public function searchAll(): bool
    {
        return $this->getBool(self::ALL);
    }
}
