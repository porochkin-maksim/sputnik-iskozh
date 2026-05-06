<?php declare(strict_types=1);

namespace App\Providers;

use App\Listeners\Billing\DispatchImportPaymentsSaveListener;
use App\Listeners\CounterHistory\DispatchCounterHistoriesLinkedCheckClaimListener;
use App\Listeners\CounterHistory\DispatchCheckClaimForCounterChangeListener;
use App\Listeners\HelpDesk\SendTicketCreatedNotificationListener;
use App\Listeners\HistoryChanges\DispatchCreateHistoryJobListener;
use App\Listeners\LogSentEmailListener;
use App\Listeners\Proposal\DispatchProposalCreatedJobListener;
use Core\Domains\Billing\Events\ImportPaymentsSaveRequested;
use Core\Domains\CounterHistory\Events\CounterHistoryConfirmed;
use Core\Domains\CounterHistory\Events\CounterHistoriesLinked;
use Core\Domains\HelpDesk\Events\TicketCreated;
use Core\Domains\HistoryChanges\Events\HistoryChangesSaveRequested;
use Core\Domains\Proposal\Events\ProposalCreated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;
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
        Registered::class                  => [
            SendEmailVerificationNotification::class,
        ],
        Login::class                       => [
            LogSuccessfulLogin::class,
        ],
        MessageSent::class                 => [
            LogSentEmailListener::class,
        ],
        TicketCreated::class               => [
            SendTicketCreatedNotificationListener::class,
        ],
        CounterHistoryConfirmed::class     => [
            DispatchCheckClaimForCounterChangeListener::class,
        ],
        CounterHistoriesLinked::class      => [
            DispatchCounterHistoriesLinkedCheckClaimListener::class,
        ],
        ImportPaymentsSaveRequested::class => [
            DispatchImportPaymentsSaveListener::class,
        ],
        ProposalCreated::class             => [
            DispatchProposalCreatedJobListener::class,
        ],
        HistoryChangesSaveRequested::class => [
            DispatchCreateHistoryJobListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
