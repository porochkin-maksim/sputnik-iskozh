<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payments\SaveRequest;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Models\Billing\Payment;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\Billing\Payment\PaymentLocator;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private PaymentFactory $paymentFactory;
    private PaymentService $paymentService;
    private InvoiceService $invoiceService;

    public function __construct()
    {
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
        $this->invoiceService = InvoiceLocator::InvoiceService();
    }

    public function create(int $invoiceId): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $payment = $this->paymentFactory->makeDefault()
            ->setInvoiceId($invoiceId)
            ->setInvoice($invoice);

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function get(int $invoiceId, int $paymentId): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        if ( ! $invoiceId || ! $paymentId) {
            abort(412);
        }

        $payment        = $this->paymentService->getById($paymentId);
        $invoice        = $this->invoiceService->getById($invoiceId);
        if ( ! $payment || ! $invoice) {
            abort(412);
        }

        $payment = $payment->setInvoice($invoice);

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function save(int $invoiceId, SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $payment = $request->getId()
            ? $this->paymentService->getById($request->getId())
            : $this->paymentFactory->makeDefault()
                ->setModerated(true)
                ->setVerified(true)
                ->setInvoiceId($invoiceId)
                ->setAccountId($invoice->getAccountId());

        if ( ! $payment) {
            abort(404);
        }

        $payment
            ->setCost($request->getCost())
            ->setComment($request->getComment());

        $payment = $this->paymentService->save($payment);

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function list(int $invoiceId): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $searcher = new PaymentSearcher();
        $searcher
            ->setInvoiceId($invoiceId)
            ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_DESC);

        $payments = $this->paymentService->search($searcher);

        return response()->json(new PaymentsListResource(
            $payments->getItems(),
        ));
    }

    public function delete(int $invoiceId, int $id): bool
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return $this->paymentService->deleteById($id);
    }

    private function canEdit(): bool
    {
        return \app::roleDecorator()->canInvoices();
    }
}
