<?php

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\Models\CounterDTO;

readonly class CounterListResource extends AbstractResource
{
    public function __construct(
        private CounterCollection $counterCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->counterCollection as $counter) {
            $result[] = new CounterResource($counter);
        }

        return $result;
    }
}