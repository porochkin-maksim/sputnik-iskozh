<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\Comparators;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\OptionDTO;

class CounterReadingDayComparator extends AbstractComparatorDTO
{
    public const string TITLE_DAY = 'День снятия показаний счетчиков';

    protected const array KEYS_TO_TITLES = [
        'day' => self::TITLE_DAY,
    ];

    public function __construct(OptionDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        if ($entity->getData() instanceof CounterReadingDay) {
            $data = $entity->getData();
            $this->expandedProperties = [
                'day' => $data->getDay(),
            ];
        } else {
            $this->expandedProperties = [];
        }
    }
} 