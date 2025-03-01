<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Models;

use App\Models\Billing\Period;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class PeriodComparator extends AbstractComparatorDTO
{
    public const TITLE_NAME     = 'Название';
    public const TITLE_START_AT = 'Начало';
    public const TITLE_END_AT   = 'Окончание';

    protected const KEYS_TO_TITLES = [
        Period::NAME     => self::TITLE_NAME,
        Period::START_AT => self::TITLE_START_AT,
        Period::END_AT   => self::TITLE_END_AT,
    ];

    public function __construct(PeriodDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            Period::START_AT => $entity->getStartAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FULL),
            Period::END_AT   => $entity->getEndAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FULL),
        ];
    }
}
