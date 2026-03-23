<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use App\Models\User;
use App\Observers\Billing\ClaimObserver;
use App\Observers\Billing\InvoiceObserver;
use App\Observers\Billing\PeriodObserver;
use App\Observers\Billing\ServiceObserver;
use App\Observers\Counter\CounterObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Counter::observe(CounterObserver::class);
        Period::observe(PeriodObserver::class);
        Service::observe(ServiceObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Claim::observe(ClaimObserver::class);
    }
}
