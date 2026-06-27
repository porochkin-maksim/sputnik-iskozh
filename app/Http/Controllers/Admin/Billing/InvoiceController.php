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
use App\Support\HistoryChangesRoute;
use Core\App\Billing\Invoice\GetListCommand;
use Core\App\Billing\Invoice\SaveCommand;
use Core\App\Billing\Invoice\SyncPeriodServicesCommand;
use Core\App\Billing\Payment\ResetPeriodPaymentsCommand;
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
use App\Jobs\Billing\CreateRegularPeriodInvoicesJob;
use Core\Domains\Billing\Period\PeriodGate;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Http\JsonResponse;
use lc;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{

    public function __construct(
        private readonly InvoiceFactory             $invoiceFactory,
        private readonly InvoiceService             $invoiceService,
        private readonly PeriodService              $periodService,
        private readonly PeriodGate                 $periodGate,
        private readonly AccountService             $accountService,
        private readonly GetListCommand             $getListCommand,
        private readonly SaveCommand                $saveCommand,
        private readonly ResetPeriodPaymentsCommand $resetPeriodPaymentsCommand,
        private readonly SyncPeriodServicesCommand  $syncPeriodServicesCommand,
    )
    {
    }

    // blade: resources/views/admin/pages/invoices.blade.php
    // vue: resources/js/components/admin/invoices/InvoicesBlock.vue
    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            return view('pages.admin.billing.invoices');
        }

        abort(403);
    }

    // blade: resources/views/admin/pages/invoices/view.blade.php
    // vue: resources/js/components/admin/invoices/InvoiceItemView.vue
    // vue: resources/js/components/admin/invoices/InvoiceItemEdit.vue
    public function view(int $id)
    {
        $invoice = $this->getViewInvoice($id);

        $invoice = new InvoiceResource($invoice);

        return view('pages.admin.billing.invoices-view', compact('invoice'));
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

    // vue: resources/js/components/admin/invoices/InvoicesBlock.vue
    public function list(DefaultRequest $request): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $invoices = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSortField(),
            $request->getSortOrder(),
            $request->getStringOrNull('paid_status'),
            $request->getIntOrNull('period_id'),
            $request->getStringOrNull('account'),
            $request->getIntOrNull('type'),
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

        $result = [
            'invoices'      => new InvoicesListResource($invoices->getItems()),
            'total'         => $invoices->getTotal(),
            'historyUrl'    => HistoryChangesRoute::make(type: HistoryType::INVOICE),
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
            $request->getIntOrNull('period'),
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

        $periodId = $request->getInt('period_id');
        if ($periodId) {
            $this->periodGate->assertNotClosed($periodId);
        }

        $invoice = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getInt('period_id'),
            $request->getInt('account_id'),
            $request->getIntOrNull('type'),
            $request->getStringOrNull('name'),
            $request->getFloat('rounding'),
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
        return response()->json($this->invoiceService->recalcInvoice($id, true));
    }

    public function recalcPeriod(int $periodId): JsonResponse
    {
        $this->periodGate->assertCanEditInvoices($periodId);

        $invoices = $this->invoiceService->search(
            new InvoiceSearcher()->setPeriodId($periodId),
        )->getItems();

        $sent    = 0;
        $blocked = 0;
        foreach ($invoices as $invoice) {
            $result = $this->invoiceService->recalcInvoice($invoice->getId());
            if ($result === true) {
                $sent++;
            }
            elseif ($result === false) {
                $blocked++;
            }
        }

        return response()->json([
            'sent'    => $sent,
            'blocked' => $blocked,
        ]);
    }

    public function resetPaymentsPeriod(int $periodId): JsonResponse
    {
        $this->periodGate->assertCanEditInvoices($periodId);

        return response()->json($this->resetPeriodPaymentsCommand->execute($periodId));
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
        $this->periodGate->assertCanEditInvoices($periodId);

        return CreateRegularPeriodInvoicesJob::dispatchSyncIfNeeded($periodId);
    }

    public function syncServices(int $periodId): JsonResponse
    {
        $this->periodGate->assertCanEditInvoices($periodId);

        $result = $this->syncPeriodServicesCommand->execute($periodId);

        return response()->json($result);
    }

    private function getViewInvoice(int $id): ?InvoiceEntity
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }
        $invoice = $this->invoiceService->search(new InvoiceSearcher()
            ->setId($id)
            ->setWithClaims()
            ->setWithAccount()
            ->setWithPeriod()
            ->setWithService()
            ->setLimit(1),
        )->getItems()->first();
        if ( ! $invoice) {
            abort(404);
        }

        return $invoice;
    }
}
