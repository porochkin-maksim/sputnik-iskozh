<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Exports\InvoicesExport\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Invoices\InvoiceResource;
use App\Http\Resources\Admin\Invoices\InvoicesListResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Http\Resources\Common\SelectResource;
use Core\App\Billing\Invoice\GetListCommand;
use Core\App\Billing\Invoice\SaveCommand;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use Core\Repositories\SearcherInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Jobs\CreateRegularPeriodInvoicesJob;
use Core\Domains\Billing\Jobs\RecalcClaimsPaidJob;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Illuminate\Http\JsonResponse;
use lc;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{

    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly InvoiceService $invoiceService,
        private readonly PeriodService  $periodService,
        private readonly AccountService $accountService,
        private readonly GetListCommand $getListCommand,
        private readonly SaveCommand    $saveCommand,
    )
    {
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            return view('admin.pages.invoices');
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

    public function list(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $invoices = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSortField(),
            $request->getSortOrder(),
            $request->getStringOrNull('status'),
            $request->getStringOrNull('period'),
            $request->getStringOrNull('account'),
            $request->getStringOrNull('type'),
        );

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

    public function export(DefaultRequest $request)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $invoices = $this->getListCommand->execute(
            null,
            null,
            Invoice::ID,
            'asc',
            $request->getStringOrNull('status'),
            $request->getStringOrNull('period'),
            $request->getStringOrNull('account'),
            $request->getStringOrNull('type'),
            true,
        )->getItems();

        return Excel::download(new InvoicesExport($invoices), sprintf('счета-%s.xlsx', now()->format('Y-m-d-hi')));
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_EDIT)) {
            abort(403);
        }

        $invoice = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getInt('period_id'),
            $request->getInt('account_id'),
            $request->getIntOrNull('type'),
            $request->getStringOrNull('name'),
        );

        if ( ! $invoice) {
            abort(404);
        }

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

    private function getViewInvoice(int $id): ?InvoiceEntity
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }
        $invoice = $this->invoiceService->search((new InvoiceSearcher())
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
