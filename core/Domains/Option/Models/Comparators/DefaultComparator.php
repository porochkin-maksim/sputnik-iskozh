<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\Comparators;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Option\OptionEntity;

class DefaultComparator extends AbstractComparatorDTO
{
    public const string TITLE_DATA = 'Данные';

    protected const array KEYS_TO_TITLES = [
        'data' => self::TITLE_DATA,
    ];

    public function __construct(OptionEntity $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            'data' => $entity->getData()?->jsonSerialize(),
        ];
    }
} 
