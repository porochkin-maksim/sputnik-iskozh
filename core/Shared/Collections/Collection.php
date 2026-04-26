<?php declare(strict_types=1);

namespace Core\Shared\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template TKey of array-key
 * @template TValue
 * @implements IteratorAggregate<TKey, TValue>
 */
class Collection implements IteratorAggregate, Countable
{
    /** @var array<TKey, TValue> */
    protected array $items = [];

    /**
     * @param array<TKey, TValue> $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $item) {
            $this->offsetSet($key, $item);
        }
    }

    protected function offsetSet(mixed $key, mixed $value): void
    {
        if ($key === null) {
            $this->items[] = $value;
        }
        else {
            $this->items[$key] = $value;
        }
    }

    /**
     * @param TValue $value
     *
     * @return static
     */
    public function add($value): static
    {
        $this->offsetSet(null, $value);

        return $this;
    }

    /**
     * @param TValue $value
     *
     * @return static
     */
    public function push($value): static
    {
        return $this->add($value);
    }

    /**
     * @param TKey $key
     *
     * @return static
     */
    public function remove($key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @param TKey $key
     *
     * @return TValue|null
     */
    public function get($key)
    {
        return $this->items[$key] ?? null;
    }

    /**
     * @return TValue|null
     */
    public function first()
    {
        return $this->items[array_key_first($this->items)] ?? null;
    }

    /**
     * @return TValue|null
     */
    public function last()
    {
        return $this->items[array_key_last($this->items)] ?? null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param callable(TValue, TKey): bool $callback
     *
     * @return static
     */
    public function filter(callable $callback): static
    {
        $filtered = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);

        return new static($filtered);
    }

    /**
     * @template TNewValue
     * @param callable(TValue, TKey): TNewValue $callback
     *
     * @return static<TKey, TNewValue>
     */
    public function map(callable $callback): static
    {
        $mapped = array_map($callback, $this->items, array_keys($this->items));
        /** @var static<TKey, TNewValue> $newCollection */
        $newCollection = new static($mapped);

        return $newCollection;
    }

    /**
     * @param callable(TValue, TKey): bool $callback
     *
     * @return TValue|null
     */
    public function find(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable(TValue, TKey): bool $callback
     */
    public function exists(callable $callback): bool
    {
        return $this->find($callback) !== null;
    }

    /**
     * @param callable(TValue, TKey): void $callback
     */
    public function each(callable $callback): void
    {
        foreach ($this->items as $key => $item) {
            $callback($item, $key);
        }
    }

    /**
     * Возвращает новую коллекцию с элементами в обратном порядке.
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items, false));
    }

    /**
     * @param callable(TValue, TValue): int $callback
     */
    public function sort(callable $callback): static
    {
        $items = $this->items;
        uasort($items, $callback);

        return new static(array_values($items));
    }

    public function merge(?self $collection): static
    {
        $result = new static($this->toArray());

        if ( ! $collection) {
            return $result;
        }

        foreach ($collection as $item) {
            $result->add($item);
        }

        return $result;
    }
}
