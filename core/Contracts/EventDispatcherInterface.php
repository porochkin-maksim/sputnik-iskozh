<?php declare(strict_types=1);

namespace Core\Contracts;

/**
 * Интерфейс для диспетчера доменных событий.
 * Реализация должна быть предоставлена в инфраструктурном слое.
 */
interface EventDispatcherInterface
{
    /**
     * Диспатчит одно или несколько событий.
     *
     * @param object|array<object> $events
     *
     * @return void
     */
    public function dispatch(object|array $events): void;
}
