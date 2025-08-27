<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Periods;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Period\Models\PeriodDTO;

readonly class PeriodResource extends AbstractResource
{
    public function __construct(
        private PeriodDTO $period,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'   => $this->period->getId(),
            'name' => $this->period->getName(),
        ];
    }
}