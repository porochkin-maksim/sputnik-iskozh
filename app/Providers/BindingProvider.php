<?php declare(strict_types=1);

namespace App\Providers;

use App\Events\EventDispatcher;
use App\Helpers\FileStorage;
use App\Helpers\StringService;
use App\Repositories\Access\RoleEloquentRepository;
use App\Repositories\Account\AccountEloquentRepository;
use App\Repositories\Billing\AcquiringEloquentRepository;
use App\Repositories\Billing\ClaimEloquentRepository;
use App\Repositories\Billing\ClaimToObjectEloquentRepository;
use App\Repositories\Billing\InvoiceEloquentRepository;
use App\Repositories\Billing\PaymentEloquentRepository;
use App\Repositories\Billing\PeriodEloquentRepository;
use App\Repositories\Billing\ServiceEloquentRepository;
use App\Repositories\Billing\TransactionEloquentRepository;
use App\Repositories\Counter\CounterEloquentRepository;
use App\Repositories\CounterHistory\CounterHistoryEloquentRepository;
use App\Repositories\Files\FileEloquentRepository;
use App\Repositories\Infra\ExDataRepository;
use App\Repositories\Infra\Token\TokenEloquentRepository;
use App\Repositories\Infra\Uid\UidEloquentRepository;
use App\Repositories\Folders\FolderEloquentRepository;
use App\Repositories\HelpDesk\TicketCategoryEloquentRepository;
use App\Repositories\HelpDesk\TicketCommentEloquentRepository;
use App\Repositories\HelpDesk\TicketEloquentRepository;
use App\Repositories\HelpDesk\TicketServiceEloquentRepository;
use App\Repositories\HistoryChanges\HistoryChangesEloquentRepository;
use App\Repositories\News\NewsEloquentRepository;
use App\Repositories\Option\OptionEloquentRepository;
use App\Repositories\User\UserEloquentRepository;
use App\Services\Mail\MailSender;
use Core\Contracts\DbServiceInterface;
use Core\Contracts\EventDispatcherInterface;
use Core\Contracts\FileStorageInterface;
use Core\Contracts\MailSenderInterface;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Access\RoleRepositoryInterface;
use Core\Domains\Account\AccountRepositoryInterface;
use Core\Domains\Billing\Acquiring\Contracts\AcquiringRepositoryInterface;
use Core\Domains\Billing\Claim\ClaimRepositoryInterface;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectRepositoryInterface;
use Core\Domains\Billing\Invoice\InvoiceRepositoryInterface;
use Core\Domains\Billing\Payment\PaymentRepositoryInterface;
use Core\Domains\Billing\Period\PeriodRepositoryInterface;
use Core\Domains\Billing\Service\ServiceRepositoryInterface;
use Core\Domains\Billing\Transaction\TransactionRepositoryInterface;
use Core\Domains\Counter\CounterRepositoryInterface;
use Core\Domains\CounterHistory\CounterHistoryRepositoryInterface;
use Core\Domains\Infra\ExData\Repositories\ExDataRepositoryInterface;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;
use Core\Domains\Infra\Uid\UidRepositoryInterface;
use Core\Domains\Files\FileRepositoryInterface;
use Core\Domains\Folders\FolderRepositoryInterface;
use Core\Domains\HelpDesk\TicketCategoryRepositoryInterface;
use Core\Domains\HelpDesk\TicketCommentRepositoryInterface;
use Core\Domains\HelpDesk\TicketRepositoryInterface;
use Core\Domains\HelpDesk\TicketServiceRepositoryInterface;
use Core\Domains\HistoryChanges\HistoryChangesRepositoryInterface;
use Core\Domains\News\NewsRepositoryInterface;
use Core\Domains\Option\OptionRepositoryInterface;
use Core\Domains\User\UserRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;
use Illuminate\Support\ServiceProvider;
use App\Services\Db\DbService;

class BindingProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(EventDispatcherInterface::class, function ($app) {
            return new EventDispatcher($app->make(LaravelDispatcher::class));
        });
        $this->app->bind(MailSenderInterface::class, MailSender::class);
        $this->app->bind(DbServiceInterface::class, DbService::class);
        $this->app->bind(StringServiceInterface::class, StringService::class);
        $this->app->bind(FileStorageInterface::class, FileStorage::class);

        $this->app->bind(FileRepositoryInterface::class, FileEloquentRepository::class);
        $this->app->bind(FolderRepositoryInterface::class, FolderEloquentRepository::class);
        $this->app->bind(HistoryChangesRepositoryInterface::class, HistoryChangesEloquentRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketEloquentRepository::class);
        $this->app->bind(TicketCategoryRepositoryInterface::class, TicketCategoryEloquentRepository::class);
        $this->app->bind(TicketServiceRepositoryInterface::class, TicketServiceEloquentRepository::class);
        $this->app->bind(TicketCommentRepositoryInterface::class, TicketCommentEloquentRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, NewsEloquentRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountEloquentRepository::class);
        $this->app->bind(ClaimRepositoryInterface::class, ClaimEloquentRepository::class);
        $this->app->bind(ClaimToObjectRepositoryInterface::class, ClaimToObjectEloquentRepository::class);
        $this->app->bind(AcquiringRepositoryInterface::class, AcquiringEloquentRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceEloquentRepository::class);
        $this->app->bind(PeriodRepositoryInterface::class, PeriodEloquentRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceEloquentRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionEloquentRepository::class);
        $this->app->bind(CounterRepositoryInterface::class, CounterEloquentRepository::class);
        $this->app->bind(CounterHistoryRepositoryInterface::class, CounterHistoryEloquentRepository::class);
        $this->app->bind(OptionRepositoryInterface::class, OptionEloquentRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentEloquentRepository::class);
        $this->app->bind(ExDataRepositoryInterface::class, ExDataRepository::class);
        $this->app->bind(TokenRepositoryInterface::class, TokenEloquentRepository::class);
        $this->app->bind(UidRepositoryInterface::class, UidEloquentRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleEloquentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserEloquentRepository::class);
    }
}
