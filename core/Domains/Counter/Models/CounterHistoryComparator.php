<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use App\Models\Counter\CounterHistory;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class CounterHistoryComparator extends AbstractComparatorDTO
{
    public const string TITLE_COUNTER_ID  = 'Счётчик';
    public const string TITLE_VALUE       = 'Показание';
    public const string TITLE_DATE        = 'Дата показания';
    public const string TITLE_IS_VERIFIED = 'Подтверждён';

    protected const array KEYS_TO_TITLES = [
        CounterHistory::COUNTER_ID  => self::TITLE_COUNTER_ID,
        CounterHistory::VALUE       => self::TITLE_VALUE,
        CounterHistory::DATE        => self::TITLE_DATE,
        CounterHistory::IS_VERIFIED => self::TITLE_IS_VERIFIED,
    ];

    public function __construct(CounterHistoryDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            CounterHistory::IS_VERIFIED => $this->getYesNoText($entity->isVerified()),
            CounterHistory::DATE        => $entity->getDate()?->format(DateTimeFormat::DATE_TIME_VIEW_FULL),
        ];
    }
}
