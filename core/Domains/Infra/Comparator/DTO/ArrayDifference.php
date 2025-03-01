<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

use IteratorAggregate;

class ArrayDifference extends Difference implements IteratorAggregate, \Countable
{
    /** @var DifferenceInterface[] */
    protected array $diffs = [];

    public function add(DifferenceInterface $diff): void
    {
        $this->diffs[] = $diff;
    }

    /**
     * @param DifferenceInterface[] $diffs
     */
    public function addArray(array $diffs): void
    {
        foreach ($diffs as $diff) {
            $this->add($diff);
        }
    }

    /**
     * @return DifferenceInterface[]
     */
    public function getIterator()
    {
        return $this->diffs;
    }

    public function count(): int
    {
        return count($this->diffs);
    }
}