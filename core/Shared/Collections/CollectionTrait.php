<?php declare(strict_types=1);

namespace Core\Shared\Collections;

trait CollectionTrait
{
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

    public function removeById(?int $id): ?object
    {
        return $this->reject(fn($item) => $item->getId() === $id);
    }
}
