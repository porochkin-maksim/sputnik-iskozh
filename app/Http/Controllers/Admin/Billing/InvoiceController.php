<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Exports\InvoicesExport\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoices\ListRequest;
use App\Http\Requests\Admin\Invoices\SaveRequest;
use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Admin\Invoices\InvoiceResource;
use App\Http\Resources\Admin\Invoices\InvoicesListResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\SelectResource;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\Factories\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Jobs\CreateRegularPeriodInvoicesJob;
use Core\Domains\Billing\Jobs\RecalcClaimsPaidJob;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Core\Resources\Views\ViewNames;
use Illuminate\Http\JsonResponse;
use lc;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    private InvoiceFactory $invoiceFactory;
    private InvoiceService $invoiceService;
    private PeriodService  $periodService;
    private AccountService $accountService;

    public function __construct()
    {
        $this->invoiceFactory = InvoiceLocator::InvoiceFactory();
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->periodService  = PeriodLocator::PeriodService();
        $this->accountService = AccountLocator::AccountService();
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_INVOICES);
        }

        abort(403);
    }

    public function view(int $id)
    {
        $invoice = $this->getViewInvoice($id);

        $invoice = new InvoiceResource($invoice);

        return view('admin.pages.invoices.view', compact('invoice'));
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_EDIT)) {
            abort(403);
        }

        return response()->json(new InvoiceResource($this->invoiceFactory->makeDefault()));
    }

    public function get(int $id): JsonResponse
    {
        $invoice = $this->getViewInvoice($id);

        return response()->json(new InvoiceResource($invoice));
    }

    public function list(ListRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $searcher = new InvoiceSearcher();
        $searcher
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setWithPeriod()
            ->setWithAccount()
            ->setWithClaims()
        ;

        $this->extracted($request, $searcher);

        $invoices = $this->invoiceService->search($searcher);

        $periodSearcher = new PeriodSearcher();
        $periodSearcher
            ->setSortOrderProperty(Period::START_AT, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Period::END_AT, SearcherInterface::SORT_ORDER_DESC)
        ;
        $periods = $this->periodService->search($periodSearcher)->getItems();

        $accountSearcher = new AccountSearcher();
        $accountSearcher->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC);
        $accounts = $this->accountService->search($accountSearcher)->getItems();

        $result = new InvoicesListResource(
            $invoices->getItems(),
            $invoices->getTotal(),
        )->jsonSerialize();

        $result += [
            'periods'       => new PeriodsSelectResource($periods),
            'activePeriods' => new PeriodsSelectResource($periods->getActive()),
            'accounts'      => new AccountsSelectResource($accounts, false),
            'types'         => new SelectResource(InvoiceTypeEnum::array()),
        ];

        $activeTypes = InvoiceTypeEnum::array();
        unset($activeTypes[InvoiceTypeEnum::REGULAR->value]);
        $result['activeTypes'] = new SelectResource($activeTypes);

        return response()->json($result);
    }

    public function export(ListRequest $request)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $searcher = new InvoiceSearcher();
        $searcher
            ->setWithPeriod()
            ->setWithAccount()
            ->setWithClaims()
            ->setWithPayments()
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $this->extracted($request, $searcher);

        $invoices = $this->invoiceService->search($searcher)->getItems();

        return Excel::download(new InvoicesExport($invoices), sprintf('счета-%s.xlsx', now()->format('Y-m-d-hi')));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_EDIT)) {
            abort(403);
        }

        $invoice = $request->getId()
            ? $this->invoiceService->getById($request->getId())
            : $this->invoiceFactory->makeDefault()
                ->setType(InvoiceTypeEnum::tryFrom($request->getType()))
                ->setPeriodId($request->getPeriodId())
                ->setAccountId($request->getAccountId())
        ;

        if ( ! $invoice) {
            abort(404);
        }

        $invoice->setName($request->getName());

        $invoice = $this->invoiceService->save($invoice);

        return response()->json([
            'invoice' => new InvoiceResource($invoice),
        ]);
    }

    public function recalc(int $id): JsonResponse
    {
        return response()->json(RecalcClaimsPaidJob::dispatchSyncIfNeeded($id));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_DROP)) {
            abort(403);
        }

        return $this->invoiceService->deleteById($id);
    }

    public function getAccountCountWithoutRegular(int $periodId): int
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        return $this->invoiceService->getAccountsWithoutRegularInvoice($periodId)->count();
    }

    public function createRegularInvoices(int $periodId): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        return CreateRegularPeriodInvoicesJob::dispatchIfNeeded($periodId);
    }

    /**
     * @param ListRequest     $request
     * @param InvoiceSearcher $searcher
     *
     * @return void
     */
    public function extracted(ListRequest $request, InvoiceSearcher $searcher): void
    {
        if ($request->getSortField() && $request->getSortOrder()) {
            $searcher->setSortOrderProperty(
                $request->getSortField(),
                $request->getSortOrder() === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        if ($request->getPaidStatus()) {
            if ($request->getPaidStatus() === 'unpaid') {
                $searcher->addWhere(Invoice::PAID, SearcherInterface::EQUALS, 0);
            }
            elseif ($request->getPaidStatus() === 'paid') {
                $searcher->addWhereColumn(Invoice::PAID, SearcherInterface::GTE, Invoice::COST);
            }
            elseif ($request->getPaidStatus() === 'partial') {
                $searcher->addWhereColumn(Invoice::PAID, SearcherInterface::LT, Invoice::COST)
                    ->addWhere(Invoice::PAID, SearcherInterface::GT, 0)
                ;
            }
        }

        if ($request->getPeriodId()) {
            $searcher->setPeriodId($request->getPeriodId());
        }
        if ($request->getAccount()) {
            $accountIds = $this->accountService->search(
                AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$request->getAccount()}%"),
            )->getItems()->getIds();
            $searcher->setAccountIds($accountIds);
        }
        if ($request->getType()) {
            $searcher->setType($request->getType());
        }
    }

    private function getViewInvoice(int $id): ?InvoiceDTO
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }
        $invoice = $this->invoiceService->search(new InvoiceSearcher()
            ->setId($id)
            ->setWithClaims()
            ->setWithAccount()
            ->setWithPeriod()
            ->setLimit(1),
        )->getItems()->first();
        if ( ! $invoice) {
            abort(404);
        }

        return $invoice;
    }
}
