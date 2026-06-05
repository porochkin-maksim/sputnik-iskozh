<?php declare(strict_types=1);

namespace App\Events;

use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;
use Core\Contracts\EventDispatcherInterface;

readonly class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private LaravelDispatcher $dispatcher,
    )
    {
    }

    public function dispatch(object|array $events): void
    {
        $this->dispatcher->dispatch($events);
    }
}
