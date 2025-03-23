<?php

namespace App\Http\Controllers\Admin\Requests;

use App\Http\Requests\Admin\Payments\LinkRequest;
use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Admin\Invoices\InvoicesSelectResource;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Illuminate\Http\JsonResponse;
use lc;

class NewPaymentController
{
    private PaymentFactory $paymentFactory;
    private PaymentService $paymentService;
    private InvoiceService $invoiceService;
    private AccountService $accountService;
    private PeriodService  $periodService;

    public function __construct()
    {
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->accountService = AccountLocator::AccountService();
        $this->periodService  = PeriodLocator::PeriodService();
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

        $accountSearcher = new AccountSearcher();
        $accountSearcher->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC);
        $accounts = $this->accountService->search($accountSearcher);

        $periodSearcher = new PeriodSearcher();
        $periodSearcher->setSortOrderProperty(Period::START_AT, SearcherInterface::SORT_ORDER_DESC);
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

    public function save(LinkRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $payment = $this->paymentService->getById($request->getId());

        if ( ! $payment) {
            abort(404);
        }

        $payment
            ->setVerified(true)
            ->setModerated(true)
            ->setCost($request->getCost())
            ->setAccountId($request->getAccountId())
            ->setInvoiceId($request->getInvoiceId())
        ;

        $payment = $this->paymentService->save($payment);

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
            ->setInvoiceId(null)
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