<?php declare(strict_types=1);

namespace Core\Domains\Option\Factories;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\Comparators\ChairmanInfoComparator;
use Core\Domains\Option\Models\Comparators\CounterReadingDayComparator;
use Core\Domains\Option\Models\Comparators\DefaultComparator;
use Core\Domains\Option\Models\Comparators\SntAccountingComparator;
use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;

class ComparatorFactory
{
    /**
     * Создает экземпляр сравнителя в зависимости от типа опции
     */
    public function createComparator(OptionDTO $entity): AbstractComparatorDTO
    {
        $type = $entity->getType();
        $data = $entity->getData();

        if ($type === OptionEnum::SNT_ACCOUNTING || $data instanceof SntAccounting) {
            return new SntAccountingComparator($entity);
        }
        
        if ($type === OptionEnum::COUNTER_READING_DAY || $data instanceof CounterReadingDay) {
            return new CounterReadingDayComparator($entity);
        }

        if ($type === OptionEnum::CHAIRMAN_INFO || $data instanceof ChairmanInfo) {
            return new ChairmanInfoComparator($entity);
        }
        
        // Для неизвестных типов или когда тип не указан, возвращаем стандартный сравнитель
        return new DefaultComparator($entity);
    }
} 