<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\Payments\PaymentHistoryResource;
use App\Http\Resources\Shared\ResourseList;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentHistoryController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly InvoiceService $invoiceService,
    )
    {
    }

    public function index(): View
    {
        return view('pages.profile.payments.index');
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $accountIds = $this->accountService->getByUserId(Auth::id())->getIds();

        if (empty($accountIds)) {
            return response()->json(['payments' => [], 'total' => 0]);
        }

        $invoices = $this->invoiceService->search(
            new InvoiceSearcher()
                ->setAccountIds($accountIds)
                ->setWithPayments()
                ->setWithPeriod()
        )->getItems();

        $allPayments = [];
        foreach ($invoices as $invoice) {
            $accountNumber = $invoice->getAccount()?->getNumber();
            foreach ($invoice->getPayments() ?: [] as $payment) {
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

        return response()->json([
            'payments' => new ResourseList($payments, PaymentHistoryResource::class),
            'total'    => $total,
            'limit'    => $limit,
        ]);
    }
}
