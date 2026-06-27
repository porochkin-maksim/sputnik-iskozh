<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Invoices\InvoicesSelectResource;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Admin\Transactions\TransactionResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Support\HistoryChangesRoute;
use Carbon\Carbon;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\App\Billing\Payment\LinkPaymentCommand;
use Core\App\Billing\Payment\PayCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentFileService;
use Core\Domains\Billing\Payment\PaymentGate;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Payment\PaymentTransactionService;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\Domains\Billing\Period\PeriodGate;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;
use Illuminate\Http\JsonResponse;
use lc;

class PaymentManageController extends Controller
{
    public function __construct(
        private readonly PaymentFactory            $paymentFactory,
        private readonly PaymentService            $paymentService,
        private readonly PaymentFileService        $fileService,
        private readonly PaymentGate               $paymentGate,
        private readonly InvoiceService            $invoiceService,
        private readonly AccountService            $accountService,
        private readonly PeriodService             $periodService,
        private readonly PeriodGate                $periodGate,
        private readonly LinkPaymentCommand        $linkPaymentCommand,
        private readonly TransactionService        $transactionService,
        private readonly PayCommand                $payCommand,
        private readonly RecalcClaimsPaidCommand   $recalcClaimsPaidCommand,
        private readonly PaymentTransactionService $paymentTransactionService,
    )
    {
    }

    public function index()
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        return view('pages.admin.billing.payments');
    }

    public function list(DefaultRequest $request, ?int $invoiceId = null): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        $searcher = new PaymentSearcher()
            ->setWithAccount()
            ->setWithFiles()
            ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        $invoiceId ??= $request->getIntOrNull('invoice_id');
        $accountId = $request->getIntOrNull('account_id');

        if ($invoiceId) {
            $searcher->setInvoiceId($invoiceId);
        }
        elseif ($accountId) {
            $searcher->setAccountId($accountId);
        }
        else {
            $searcher
                ->addOrWhere(Payment::VERIFIED, SearcherInterface::EQUALS, false)
                ->addOrWhere(Payment::MODERATED, SearcherInterface::EQUALS, false)
                ->addOrWhere(Payment::INVOICE_ID, SearcherInterface::EQUALS, null)
            ;
        }

        $payments = $this->paymentService->search($searcher);

        if ($invoiceId) {
            $invoice = $this->fetchInvoice($invoiceId);
            $payments->setItems($payments->getItems()->map(function ($payment) use ($invoice) {
                return $payment->setInvoice($invoice);
            }));
        }

        // рассчитываем распределённые и нераспределённые суммы по каждому платежу
        $paymentItems = $payments->getItems();
        $paymentIds   = array_values(array_filter($paymentItems->map(fn($p) => $p->getId())->toArray()));
        if ($paymentIds !== []) {
            $allTransactions = $this->transactionService->getAllByPaymentIds($paymentIds);

            $allocatedByPayment   = [];
            $unallocatedByPayment = [];
            foreach ($allTransactions as $t) {
                $pid = $t->getPaymentId();
                if ($t->getClaimId() === null) {
                    $unallocatedByPayment[$pid] = ($unallocatedByPayment[$pid] ?? 0.0) + (float) $t->getCost();
                }
                else {
                    $allocatedByPayment[$pid] = ($allocatedByPayment[$pid] ?? 0.0) + (float) $t->getCost();
                }
            }

            $paymentItems = $paymentItems->map(function ($payment) use ($allocatedByPayment, $unallocatedByPayment) {
                $pid = $payment->getId();

                return $payment
                    ->setAllocatedSum($allocatedByPayment[$pid] ?? 0.0)
                    ->setUnallocatedSum($unallocatedByPayment[$pid] ?? 0.0)
                ;
            });
        }

        return response()->json([
            'payments'   => new PaymentsListResource($paymentItems),
            'historyUrl' => HistoryChangesRoute::make(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::PAYMENT,
            ),
            'actions'    => [
                'view' => lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW),
                'edit' => lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT),
                'drop' => lc::roleDecorator()->can(PermissionEnum::PAYMENTS_DROP),
            ],
        ]);
    }

    public function create(DefaultRequest $request, ?int $invoiceId = null): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $accountId = $request->getIntOrNull('account_id');

        $payment = $this->paymentFactory->makeDefault()
            ->setPaidAt(Carbon::now())
            ->setAccountId($accountId)
        ;

        return response()->json([
            'payment'  => new PaymentResource($payment),
            'accounts' => new AccountsSelectResource($this->accountService->getAllSorted(), false),
            'periods'  => new PeriodsSelectResource($this->periodService->getOpenPeriods()),
        ]);
    }

    public function get(int $paymentId, ?int $invoiceId = null): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }
        if ( ! $paymentId) {
            abort(412);
        }

        $payment = $this->paymentService->getById($paymentId);
        if ( ! $payment) {
            abort(412);
        }

        if ($payment->getInvoiceId()) {
            $payment->setInvoice($this->invoiceService->getById($payment->getInvoiceId()));
        }

        $transactions = $this->transactionService->getByPaymentId($paymentId);

        return response()->json([
            'payment'      => new PaymentResource($payment),
            'transactions' => $transactions->map(fn($t) => new TransactionResource($t))->toArray(),
            'accounts'     => new AccountsSelectResource($this->accountService->getAllSorted(), false),
            'periods'      => new PeriodsSelectResource($this->periodService->getOpenPeriods()),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request, ?int $invoiceId = null): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $this->paymentGate->assertCanEdit($request->getIntOrNull('id'));

        $payment = $this->linkPaymentCommand->execute(
            $request->getIntOrNull('id'),
            $request->getStringOrNull('name'),
            $request->getFloat('cost'),
            $request->getStringOrNull('comment'),
            $request->getIntOrNull('account_id'),
        );

        if ($payment === null) {
            abort(404);
        }

        if ($request->getStringOrNull('paidAt')) {
            $payment->setPaidAt($request->getStringOrNull('paidAt'));
            $this->paymentService->save($payment);
        }

        if ($request->allFiles()) {
            $this->fileService->storePaymentFiles($request->allFiles(), $payment->getId());
        }

        if ($payment->getAccountId()) {
            $this->recalcClaimsPaidCommand->executeForAccount($payment->getAccountId());
        }

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_DROP)) {
            abort(403);
        }

        return response()->json($this->paymentTransactionService->delete($id));
    }

    public function canPayAll(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        $invoice = $this->invoiceService->search(
            new InvoiceSearcher()->setId($invoiceId)->setWithClaims(),
        )->getItems()->first();

        if ( ! $invoice) {
            abort(412, 'Счёт не найден');
        }

        $accountId = $invoice->getAccountId();
        $balance   = $this->transactionService->getBalanceByAccountId($accountId);

        $totalClaimsCost = 0;
        $claims          = $invoice->getClaims() ? : [];
        foreach ($claims as $claim) {
            $claimCost = $claim->getCost() - ($claim->getPaid() ?? 0);
            if ($claimCost > 0) {
                $totalClaimsCost += $claimCost;
            }
        }

        return response()->json([
            'invoiceId'     => $invoiceId,
            'totalClaims'   => $totalClaimsCost,
            'totalPayments' => $balance,
            'canCover'      => $balance >= (float) $totalClaimsCost,
            'willPay'       => min($balance, $totalClaimsCost),
        ]);
    }

    public function payAll(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $periodId = $this->fetchInvoice($invoiceId)?->getPeriodId();
        if ($periodId) {
            $this->periodGate->assertNotClosed($periodId);
        }

        $this->payCommand->payAll($invoiceId);

        return response()->json(true);
    }

    public function payClaim(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $this->payCommand->payClaim(
            $request->getInt('claim_id'),
            $request->getFloat('cost'),
        );

        return response()->json(true);
    }

    public function unallocatedTransactions(int $accountId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        $paymentIds = $this->paymentService->getVerifiedByAccount($accountId)->getIds();
        if (empty($paymentIds)) {
            return response()->json(['transactions' => []]);
        }

        return response()->json([
            'transactions' => $this->transactionService->getUnallocatedByPaymentIdsWithClaim($paymentIds)
                ->map(fn($t) => new TransactionResource($t))
                ->toArray(),
        ]);
    }

    public function accountBalance(int $accountId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        $balance = $this->transactionService->getBalanceByAccountId($accountId);

        return response()->json([
            'balance' => $balance,
        ]);
    }

    public function getInvoices(int $accountId, int $periodId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }
        if ( ! $this->accountService->getById($accountId) || ! $this->periodService->getById($periodId)) {
            abort(412);
        }

        $searcher = new InvoiceSearcher()
            ->setAccountId($accountId)
            ->setPeriodId($periodId)
            ->setWithPeriod()
            ->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        $invoices = $this->invoiceService->search($searcher);

        return response()->json([
            'invoices' => new InvoicesSelectResource($invoices->getItems()),
        ]);
    }

    private function fetchInvoice(int $invoiceId): ?InvoiceEntity
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        $period  = $invoice ? $this->periodService->getById($invoice->getPeriodId()) : null;
        $invoice?->setPeriod($period);

        return $invoice;
    }
}
