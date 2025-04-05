<?php declare(strict_types=1);

namespace Core\Collections;

trait CollectionTrait
{
    abstract public function checkItemInstance(mixed $item): bool;

    public function __construct($items = [])
    {
        foreach ($items as $item) {
            if ( ! $this->checkItemInstance($item)) {
                throw new WrongClassException(get_class($item));
            }
        }
        parent::__construct($items);
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->getId();
        }

        return $result;
    }

    public function add(mixed $item): static
    {
        if ( ! $this->checkItemInstance($item)) {
            throw new WrongClassException(get_class($item));
        }

        return parent::add($item);
    }

    final protected function orderingFunction(mixed $compareWith, $value1, $value2): int
    {
        if ($value1 === $compareWith) {
            return -1;
        }
        if ($value2 === $compareWith) {
            return 1;
        }

        return 0;
    }

    public function getById(?int $id): ?object
    {
        foreach ($this as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }

        return null;
    }
}
