<?php

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Counter\Collections\CounterCollection;

readonly class CountersSelectResource extends AbstractResource
{
    public function __construct(
        private CounterCollection $counterCollection,
        private bool              $addEmptyOption,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        if ($this->addEmptyOption) {
            $result[] = new SelectOptionResource(0, 'Без счётчика');
        }
        foreach ($this->counterCollection as $counter) {
            $result[] = new SelectOptionResource($counter->getId(), $counter->getNumber());
        }

        return $result;
    }
}