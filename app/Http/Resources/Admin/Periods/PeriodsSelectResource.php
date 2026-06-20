<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Periods;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Period\PeriodCollection;

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
            $result[] = [
                'value'    => (string) $period->getId(),
                'label'    => (string) $period->getName(),
                'isClosed' => $period->isClosed(),
            ];
        }

        return $result;
    }
}
