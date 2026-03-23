<?php declare(strict_types=1);

namespace Core\Queue;

trait UniqueForTrait
{
    public function uniqueFor(): int
    {
        return 60; // блокировка на 60 секунд
    }
}
