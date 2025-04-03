<?php declare(strict_types=1);

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

        foreach ($this->counterHistoryCollection as $counterHistory) {
            $result[] = new CounterHistoryResource($counterHistory, $this->counterHistoryCollection->getById($counterHistory->getPreviousId()));
        }

        return $result;
    }
}