<?php declare(strict_types=1);

namespace App\Providers;

use App\Models\Access\Role;
use App\Models\Account\Account;
use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use App\Models\Files\FileModel;
use App\Models\HelpDesk\Ticket;
use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use App\Models\User;
use App\Observers\Access\RoleObserver;
use App\Observers\Account\AccountObserver;
use App\Observers\Billing\ClaimObserver;
use App\Observers\Billing\InvoiceObserver;
use App\Observers\Billing\PaymentObserver;
use App\Observers\Billing\PeriodObserver;
use App\Observers\Billing\ServiceObserver;
use App\Observers\Counter\CounterHistoryObserver;
use App\Observers\Counter\CounterObserver;
use App\Observers\Files\FileObserver;
use App\Observers\HelpDesk\TicketCategoryObserver;
use App\Observers\HelpDesk\TicketObserver;
use App\Observers\HelpDesk\TicketServiceObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Counter::observe(CounterObserver::class);
        Period::observe(PeriodObserver::class);
        Service::observe(ServiceObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Claim::observe(ClaimObserver::class);
        Payment::observe(PaymentObserver::class);
        CounterHistory::observe(CounterHistoryObserver::class);
        Account::observe(AccountObserver::class);
        Ticket::observe(TicketObserver::class);
        TicketCategory::observe(TicketCategoryObserver::class);
        TicketService::observe(TicketServiceObserver::class);
        FileModel::observe(FileObserver::class);
    }
}
