<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

use Countable;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

class ChangesCollection implements IteratorAggregate, Countable, Arrayable
{
    /** @var Changes[] */
    private array $changes;

    public function __construct(Changes ...$changes)
    {
        $this->changes = $changes;
    }

    /**
     * @return Changes[]
     */
    public function getIterator()
    {
        return $this->changes;
    }

    public function add(Changes $changes)
    {
        $this->changes[] = $changes;
    }

    public function count(): int
    {
        return count($this->changes);
    }

    public static function makeFromArray(array $data): static
    {
        $result = [];
        foreach ($data as $datum) {
            $result[] = new Changes($datum[0], $datum[1], $datum[2]);
        }

        return new static(... $result);
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->changes as $changes) {
            $result[] = [
                $changes->getTitle(),
                $changes->getOldValue(),
                $changes->getNewValue(),
            ];
        }

        return $result;
    }
}