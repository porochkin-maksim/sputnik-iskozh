<?php declare(strict_types=1);

namespace App\Providers;

use Core\Objects\User\Services\UserServiceInterface;
use Core\Objects\User\UserLocator;
use Illuminate\Support\ServiceProvider;

class BindingProvider extends ServiceProvider
{
    public function boot(): void
    {
//        $this->app->singleton(UserServiceInterface::class, fn() => UserLocator::UserService());
    }
}
