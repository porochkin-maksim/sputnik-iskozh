<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\Payments\PaymentHistoryResource;
use App\Http\Resources\Shared\ResourseList;
use App\Models\Billing\Payment;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\Repositories\SearcherInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PaymentHistoryController extends Controller
{
    public function __construct(
        private readonly InvoiceService       $invoiceService,
        private readonly PaymentService       $paymentService,
        private readonly TransactionService   $transactionService,
    )
    {
    }

    public function index(): View
    {
        return view('pages.profile.payments.index');
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $accountId = \lc::account()->getId();

        if ($accountId === null) {
            return response()->json(['payments' => [], 'total' => 0]);
        }

        $result = $this->paymentService->search(
            (new PaymentSearcher())
                ->setAccountId($accountId)
                ->withAccount()
                ->setSortOrderProperty(Payment::PAID_AT, SearcherInterface::SORT_ORDER_DESC),
        );

        /** @var PaymentEntity[] $allPayments */
        $allPayments = iterator_to_array($result->getItems());

        $invoiceIds = array_unique(array_filter(
            array_map(static fn(PaymentEntity $p) => $p->getInvoiceId(), $allPayments),
        ));

        if ($invoiceIds) {
            $invoices = iterator_to_array(
                $this->invoiceService->search(
                    (new InvoiceSearcher())->setIds($invoiceIds)->setWithPeriod(),
                )->getItems()
            );

            $invoicesById = [];
            foreach ($invoices as $invoice) {
                $invoicesById[$invoice->getId()] = $invoice;
            }

            foreach ($allPayments as $payment) {
                $invoice = $invoicesById[$payment->getInvoiceId()] ?? null;
                $payment->setInvoice($invoice);
                $payment->setAccountNumber($invoice?->getAccount()?->getNumber() ?? '');
            }
        }

        $total  = count($allPayments);
        $limit  = $request->getLimit() ?? 20;
        $offset = $request->getOffset() ?? 0;

        $payments = array_slice($allPayments, $offset, $limit);

        $balance = $this->transactionService->getBalanceByAccountId($accountId);

        return response()->json([
            'payments' => new ResourseList($payments, PaymentHistoryResource::class),
            'total'    => $total,
            'limit'    => $limit,
            'balance'  => $balance,
        ]);
    }
}
