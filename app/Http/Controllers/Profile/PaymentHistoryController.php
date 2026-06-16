<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\Payments\PaymentHistoryResource;
use App\Http\Resources\Shared\ResourseList;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Transaction\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PaymentHistoryController extends Controller
{
    public function __construct(
        private readonly InvoiceService     $invoiceService,
        private readonly TransactionService $transactionService,
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

        $invoices = $this->invoiceService->search(
            new InvoiceSearcher()
                ->setAccountId($accountId)
                ->setWithPayments()
                ->setWithPeriod(),
        )->getItems();

        $allPayments = [];
        foreach ($invoices as $invoice) {
            $accountNumber = $invoice->getAccount()?->getNumber();
            foreach ($invoice->getPayments() ? : [] as $payment) {
                $payment->setInvoice($invoice);
                $payment->setAccountNumber($accountNumber);
                $allPayments[] = $payment;
            }
        }

        usort($allPayments, static function (PaymentEntity $a, PaymentEntity $b): int {
            $tA = $a->getCreatedAt()?->getTimestamp() ?? 0;
            $tB = $b->getCreatedAt()?->getTimestamp() ?? 0;

            return $tB <=> $tA;
        });

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
