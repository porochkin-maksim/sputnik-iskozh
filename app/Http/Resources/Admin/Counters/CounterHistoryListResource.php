<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\CounterHistory\CounterHistoryCollection;

readonly class CounterHistoryListResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryCollection $counterHistoryCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->counterHistoryCollection as $counterHistory) {
            $result[] = new CounterHistoryResource($counterHistory);
        }

        return $result;
    }
}
