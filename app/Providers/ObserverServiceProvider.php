<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Counter\Counter;
use App\Models\User;
use App\Observers\Counter\CounterObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Counter::observe(CounterObserver::class);
    }
}
