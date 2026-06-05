<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

use IteratorAggregate;

class ArrayDifference extends Difference implements \Countable, IteratorAggregate
{
    /** @var DifferenceInterface[] */
    protected array $diffs = [];

    public function add(DifferenceInterface $diff): void
    {
        $this->diffs[] = $diff;
    }

    /**
     * @param  DifferenceInterface[]  $diffs
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
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->diffs);
    }

    public function count(): int
    {
        return count($this->diffs);
    }
}
