<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Collections\CounterCollection;

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