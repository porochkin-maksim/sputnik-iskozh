<?php declare(strict_types=1);

namespace App\Providers;

use Core\Domains\Billing\Invoice\Subscribers\InvoiceSubscriber;
use Core\Domains\Billing\Payment\Subscribers\PaymentSubscriber;
use Core\Domains\Billing\Period\Subscribers\PeriodSubscriber;
use Core\Domains\Billing\Claim\Subscribers\ClaimSubscriber;
use Core\Domains\Counter\Subscribers\CounterSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::subscribe(InvoiceSubscriber::class);
        Event::subscribe(ClaimSubscriber::class);
        Event::subscribe(PeriodSubscriber::class);
        Event::subscribe(PaymentSubscriber::class);
        Event::subscribe(CounterSubscriber::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
