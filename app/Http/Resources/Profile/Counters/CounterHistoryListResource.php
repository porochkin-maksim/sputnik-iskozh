<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Collections\CounterHistoryCollection;

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

        if ($this->counterHistoryCollection->isEmpty()) {
            return $result;
        }
        $previous = null;
        foreach ($this->counterHistoryCollection->reverse() as $counterHistory) {
            $result[] = new CounterHistoryResource($counterHistory, $previous);

            $previous = $counterHistory;
        }

        return array_reverse($result);
    }
}