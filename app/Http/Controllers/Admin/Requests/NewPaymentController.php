<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Requests;

use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Invoices\InvoicesSelectResource;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\App\Billing\Payment\LinkPaymentCommand;
use Illuminate\Http\JsonResponse;
use lc;

class NewPaymentController
{

    public function __construct(
        private readonly PaymentService     $paymentService,
        private readonly InvoiceService     $invoiceService,
        private readonly AccountService     $accountService,
        private readonly PeriodService      $periodService,
        private readonly LinkPaymentCommand $linkPaymentCommand,
    )
    {
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            return view('admin.pages.payments');
        }

        abort(403);
    }

    public function get(int $paymentId): JsonResponse
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

        $accountSearcher = new AccountSearcher();
        $accountSearcher->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC);
        $accounts = $this->accountService->search($accountSearcher);

        $periodSearcher = new PeriodSearcher();
        $periodSearcher
            ->setIsClosed(false)
            ->setSortOrderProperty(Period::START_AT, SearcherInterface::SORT_ORDER_DESC)
        ;
        $periods = $this->periodService->search($periodSearcher);

        return response()->json([
            'payment'  => new PaymentResource($payment),
            'accounts' => new AccountsSelectResource($accounts->getItems(), false),
            'periods'  => new PeriodsSelectResource($periods->getItems()),
        ]);
    }

    public function getInvoices(int $accountId, int $periodId): JsonResponse
    {
        if ( ! $this->accountService->getById($accountId) || ! $this->periodService->getById($periodId)) {
            abort(412);
        }

        $searcher = new InvoiceSearcher();
        $searcher
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

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $payment = $this->linkPaymentCommand->execute(
            $request->getIntOrNull('id'),
            $request->getStringOrNull('name'),
            $request->getFloat('cost'),
            $request->getStringOrNull('comment'),
            $request->getIntOrNull('account_id'),
            $request->getInt('invoice_id') ? : null,
        );

        if ($payment === null) {
            abort(404);
        }

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        $searcher = new PaymentSearcher();
        $searcher
            ->addOrWhere(Payment::VERIFIED, SearcherInterface::EQUALS, false)
            ->addOrWhere(Payment::MODERATED, SearcherInterface::EQUALS, false)
            ->addOrWhere(Payment::INVOICE_ID, SearcherInterface::EQUALS, null)
            ->setWithFiles()
            ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $payments = $this->paymentService->search($searcher);

        return response()->json(new PaymentsListResource(
            $payments->getItems(),
        ));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_DROP)) {
            abort(403);
        }

        return $this->paymentService->deleteById($id);
    }
}
