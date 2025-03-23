<?php

namespace App\Http\Resources\Admin\Periods;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Domains\Billing\Period\Collections\PeriodCollection;

readonly class PeriodsSelectResource extends AbstractResource
{
    public function __construct(
        private PeriodCollection $periodCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->periodCollection as $period) {
            $result[] = new SelectOptionResource($period->getId(), $period->getName());
        }

        return $result;
    }
}