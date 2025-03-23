<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\Services;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\Comparator\DTO\ArrayDifference;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\Comparator\DTO\Difference;
use Core\Domains\Infra\Comparator\DTO\DifferenceCollection;
use Core\Domains\Infra\Comparator\Exception\InvalidArgumentException;
use Core\Domains\Infra\Comparator\Exception\NotSupportedException;
use Core\Domains\Infra\Comparator\Factories\HistoryChangesFactory;

readonly class Comparator
{
    public function __construct(
        private HistoryChangesFactory $historyChangesFactory,
    )
    {

    }

    public function makeChanges(
        AbstractComparatorDTO $before,
        AbstractComparatorDTO $current,
    ): ChangesCollection
    {
        $differenceCollection = $this->historyChangesFactory->expandChangesValues(
            $this->compare(
                $before,
                $current,
            ),
            $before,
            $current,
        );

        return $this->historyChangesFactory->make(
            $before::getKeysToTitles(),
            $differenceCollection,
        );
    }

    /**
     * @param mixed           $value1
     * @param mixed           $value2
     * @param int|string|null $index - property name or array index
     * @param int             $maxDepth
     *
     * @return DifferenceCollection
     * @throws InvalidArgumentException
     * @throws NotSupportedException
     */
    private function compare(mixed $value1, mixed $value2, mixed $index = null, $maxDepth = 255): DifferenceCollection
    {
        $valueType1 = gettype($value1);
        $valueType2 = gettype($value2);

        if ($valueType1 === 'resource' || $valueType2 === 'resource') {
            throw new NotSupportedException('Resource values not supported.');
        }

        if (
            ($value1 !== null && $value2 !== null)
            && $valueType1 !== $valueType2
        ) {
            throw new InvalidArgumentException("Property {$index} values must be the same type or null. Property type 1 is a {$valueType1}, but property 2 is a {$valueType2}");
        }

        $collection = new DifferenceCollection();

        if (
            ($value1 === null || is_scalar($value1))
            && ($value2 === null || is_scalar($value2))
        ) {

            if ($value1 !== $value2) {
                $collection->add(new Difference($value1, $value2, $index));
            }

            return $collection;
        }

        if ($maxDepth === 0) {
            return $collection;
        }

        if ($value1 === null || is_object($value1)) {
            $arrayValue1 = $value1 === null ? [] : (array) $value1;
            $arrayValue2 = $value2 === null ? [] : (array) $value2;
        }
        else {
            $arrayValue1 = $value1;
            $arrayValue2 = $value2 ?? [];
        }

        $props = array_unique(array_merge(array_keys($arrayValue1), array_keys($arrayValue2)));

        if (count($props) !== 0) {
            $diff = new ArrayDifference($value1, $value2, $index);

            foreach ($props as $prop) {
                $propertyValue1 = $arrayValue1[$prop] ?? null;
                $propertyValue2 = $arrayValue2[$prop] ?? null;

                $innerCollection = $this->compare($propertyValue1, $propertyValue2, $prop, $maxDepth - 1);

                if ($innerCollection->count() > 0) {
                    $diff->addArray($innerCollection->getIterator());
                }
            }

            if ($diff->count() > 0) {
                $collection->add($diff);
            }
        }
        elseif ($arrayValue1 !== $arrayValue2) {
            $collection->add(new Difference($value1, $value2));
        }

        return $collection;
    }
}