<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

class DifferenceCollection implements \Countable, \IteratorAggregate
{
    /** @var DifferenceInterface[] */
    private $differences = [];

    public function __construct(DifferenceInterface ...$differences)
    {
        $this->differences = $differences;
    }

    public function add(DifferenceInterface $difference)
    {
        $this->differences[] = $difference;
    }

    public function merge(DifferenceCollection $collection): void
    {
        $this->differences = array_merge($this->differences, $collection->getIterator());
    }

    /**
     * @return DifferenceInterface[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->differences);
    }

    public function count(): int
    {
        return count($this->differences);
    }
}
