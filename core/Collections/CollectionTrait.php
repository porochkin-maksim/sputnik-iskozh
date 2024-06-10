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

    public function add(mixed $item): static
    {
        if ( ! $this->checkItemInstance($item)) {
            throw new WrongClassException(get_class($item));
        }

        return parent::add($item);
    }
}
