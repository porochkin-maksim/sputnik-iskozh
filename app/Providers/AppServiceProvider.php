<?php declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use env;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        env::init();
        Carbon::setLocale('ru');
    }
}
