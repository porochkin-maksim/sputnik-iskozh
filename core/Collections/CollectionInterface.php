<?php declare(strict_types=1);

namespace Core\Collections;

interface CollectionInterface
{
    public function checkItemInstance(mixed $item): bool;
}
