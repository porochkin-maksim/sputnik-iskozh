<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Counter\CounterHistory;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\Infra\DbLock\Service\LockService;
use Core\Domains\User\UserSearcher;
use Core\Domains\User\UserService;
use Core\Domains\Access\RoleService;
use Core\Repositories\SearcherInterface;
use Illuminate\Http\JsonResponse;
use lc;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AccountService        $accountService,
        private readonly UserService           $userService,
        private readonly RoleService           $roleService,
        private readonly InvoiceService        $invoiceService,
        private readonly PaymentService        $paymentService,
        private readonly PeriodService         $periodService,
        private readonly ServiceCatalogService $serviceCatalogService,
        private readonly CounterService        $counterService,
        private readonly CounterHistoryService $counterHistoryService,
        private readonly TicketService         $ticketService,
        private readonly LockService           $lockService,
    )
    {
    }

    public function index()
    {
        $roleDecorator = lc::roleDecorator();
        $alerts        = [];

        if ($roleDecorator->can(PermissionEnum::PAYMENTS_VIEW)) {
            $unlinkedPayments = $this->paymentService->search(
                (new PaymentSearcher())->setInvoiceId(null),
            )->getTotal();

            $alerts['unlinkedPayments'] = $unlinkedPayments;
        }

        if ($roleDecorator->can(PermissionEnum::COUNTERS_VIEW)) {
            $unlinkedHistories = $this->counterHistoryService->search(
                new CounterHistorySearcher()->addWhere(
                    CounterHistory::COUNTER_ID, SearcherInterface::IS_NULL,
                ),
            )->getTotal();

            $unverifiedHistories = $this->counterHistoryService->search(
                (new CounterHistorySearcher())->setVerified(false),
            )->getTotal();

            $alerts['counterHistories'] = [
                'unlinked'   => $unlinkedHistories,
                'unverified' => $unverifiedHistories,
            ];
        }

        return view('pages.admin.index', compact('alerts'));
    }

    public function cards(): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        $stats = [
            'userCount'    => $roleDecorator->can(PermissionEnum::USERS_VIEW) ? $this->userService->search(new UserSearcher())->getTotal() : null,
            'accountCount' => $roleDecorator->can(PermissionEnum::ACCOUNTS_VIEW) ? $this->accountService->search(new AccountSearcher())->getTotal() : null,
            'roleCount'    => $roleDecorator->can(PermissionEnum::ROLES_VIEW) ? $this->roleService->all()->count() : null,
            'invoiceCount' => $roleDecorator->can(PermissionEnum::INVOICES_VIEW) ? $this->invoiceService->search(new InvoiceSearcher())->getTotal() : null,
            'paymentCount' => $roleDecorator->can(PermissionEnum::PAYMENTS_VIEW) ? $this->paymentService->search(new PaymentSearcher())->getTotal() : null,
            'periodCount'  => $roleDecorator->can(PermissionEnum::PERIODS_VIEW) ? $this->periodService->search(new PeriodSearcher())->getTotal() : null,
            'serviceCount' => $roleDecorator->can(PermissionEnum::SERVICES_VIEW) ? $this->serviceCatalogService->search(new ServiceSearcher())->getTotal() : null,
            'counterCount' => $roleDecorator->can(PermissionEnum::COUNTERS_VIEW) ? $this->counterService->search(new CounterSearcher())->getTotal() : null,
            'ticketCount'  => $roleDecorator->can(PermissionEnum::HELP_DESK_VIEW) ? $this->ticketService->search(new TicketSearcher())->getTotal() : null,
        ];

        $details = [];

        $activePeriod = $roleDecorator->can(PermissionEnum::PERIODS_VIEW)
            ? $this->periodService->getActive()
            : null;

        $details['activePeriod'] = $activePeriod ? [
            'id'   => $activePeriod->getId(),
            'name' => $activePeriod->getName(),
        ] : null;

        if ($roleDecorator->can(PermissionEnum::ACCOUNTS_VIEW)) {
            $all                 = $this->accountService->search(new AccountSearcher())->getTotal();
            $withoutSnt          = $this->accountService->search(
                new AccountSearcher()->addWhere(Account::ID, SearcherInterface::IS_NOT, 1),
            )->getTotal();
            $details['accounts'] = [
                'all'        => $all,
                'withoutSnt' => $withoutSnt,
                'snt'        => $all - $withoutSnt,
            ];
        }

        if ($roleDecorator->can(PermissionEnum::INVOICES_VIEW) && $activePeriod) {
            $periodId            = $activePeriod->getId();
            $details['invoices'] = [
                'regular' => $this->invoiceService->search(new InvoiceSearcher()->setPeriodId($periodId)->setType(InvoiceTypeEnum::REGULAR))->getTotal(),
                'income'  => $this->invoiceService->search(new InvoiceSearcher()->setPeriodId($periodId)->setType(InvoiceTypeEnum::INCOME))->getTotal(),
                'outcome' => $this->invoiceService->search(new InvoiceSearcher()->setPeriodId($periodId)->setType(InvoiceTypeEnum::OUTCOME))->getTotal(),
            ];

            $invoiceIds = $this->invoiceService->search(
                new InvoiceSearcher()->setPeriodId($periodId),
            )->getItems()->map(static fn($i) => $i->getId())->toArray();

            $details['periodInvoiceIds'] = $invoiceIds;
        }

        if ($roleDecorator->can(PermissionEnum::PAYMENTS_VIEW)) {
            $periodPayments = ! empty($details['periodInvoiceIds'])
                ? $this->paymentService->search(
                    new PaymentSearcher()->setInvoiceIds($details['periodInvoiceIds']),
                )->getTotal()
                : 0;

            $details['payments'] = [
                'period' => $periodPayments,
            ];
        }

        if ($roleDecorator->can(PermissionEnum::HELP_DESK_VIEW)) {
            $details['tickets'] = [
                'new'        => $this->ticketService->search(new TicketSearcher()->setStatus(TicketStatusEnum::NEW))->getTotal(),
                'inProgress' => $this->ticketService->search(new TicketSearcher()->setStatus(TicketStatusEnum::IN_PROGRESS))->getTotal(),
                'waiting'    => $this->ticketService->search(new TicketSearcher()->setStatus(TicketStatusEnum::WAITING_FOR_CUSTOMER))->getTotal(),
                'closed'     => $this->ticketService->search(new TicketSearcher()->setStatus(TicketStatusEnum::CLOSED))->getTotal(),
                'rejected'   => $this->ticketService->search(new TicketSearcher()->setStatus(TicketStatusEnum::REJECTED))->getTotal(),
            ];
        }

        return response()->json([
            'stats'   => $stats,
            'details' => $details,
        ]);
    }
}
