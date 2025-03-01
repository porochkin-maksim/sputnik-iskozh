<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\Factories;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\Comparator\DTO\ArrayDifference;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\Comparator\DTO\Difference;
use Core\Domains\Infra\Comparator\DTO\DifferenceCollection;
use Core\Domains\Infra\Comparator\DTO\DifferenceInterface;
use UnitEnum;

class HistoryChangesFactory
{
    public function __construct()
    {
    }

    /**
     * @param array<string, string> $fieldMap
     * @param DifferenceCollection  $differenceCollection
     *
     * @return ChangesCollection
     */
    public function make(array $fieldMap, DifferenceCollection $differenceCollection): ChangesCollection
    {
        $changes         = new ChangesCollection();
        $differenceArray = $differenceCollection->getIterator();
        $differences     = array_shift($differenceArray);

        if ( ! $differences) {
            return $changes;
        }

        foreach ($differences->getIterator() as $difference) {
            if (isset($fieldMap[$difference->getIndex()])) {
                $changes->add($this->makeChange(
                    $fieldMap[$difference->getIndex()],
                    $difference,
                ));
            }
        }

        return $changes;
    }

    public function expandChangesValues(
        DifferenceCollection  $differenceCollection,
        AbstractComparatorDTO $before,
        AbstractComparatorDTO $current,
    ): DifferenceCollection
    {
        $differenceArray = $differenceCollection->getIterator();
        $differences     = array_shift($differenceArray);

        if ( ! $differences) {
            return $differenceCollection;
        }

        $diffs = new ArrayDifference($before, $current, null);
        /** @var DifferenceInterface $difference */
        foreach ($differences->getIterator() as $difference) {
            if (in_array($difference->getIndex(), $before->getPropertiesKeys())) {
                $diffs->add(new Difference(
                    $before->getProperty($difference->getIndex()),
                    $current->getProperty($difference->getIndex()),
                    $difference->getIndex(),
                ));
            }
        }

        $result = new DifferenceCollection();
        $result->add($diffs);

        return $result;
    }

    private function makeChange(string $title, Difference $difference): Changes
    {
        $oldValue = $difference->getValue1();
        $newValue = $difference->getValue2();
        $oldValue = $oldValue instanceof UnitEnum ? $oldValue->value : $oldValue;
        $newValue = $newValue instanceof UnitEnum ? $newValue->value : $newValue;

        return new Changes(
            $title,
            (string) ($oldValue ?? 'не указано'),
            (string) ($newValue ?? 'не указано'),
        );
    }
}
