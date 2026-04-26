<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Models\Billing\Payment;
use Carbon\Carbon;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodService;
use Core\App\Billing\Payment\SaveInvoicePaymentCommand;
use Illuminate\Http\JsonResponse;
use lc;

class PaymentController extends Controller
{

    public function __construct(
        private readonly PaymentFactory            $paymentFactory,
        private readonly PaymentService            $paymentService,
        private readonly InvoiceService            $invoiceService,
        private readonly PeriodService             $periodService,
        private readonly ClaimService              $claimService,
        private readonly SaveInvoicePaymentCommand $saveInvoicePaymentCommand,
    )
    {
    }

    public function create(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $payment = $this->paymentFactory->makeDefault()
            ->setInvoiceId($invoiceId)
            ->setAccountId($invoice->getAccountId())
            ->setPaidAt(Carbon::now())
            ->setCost($invoice->getDelta())
        ;

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function autoCreate(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $claims = $this->claimService->search(
            ClaimSearcher::make()
                ->setInvoiceId($invoiceId)
                ->setWithService(),
        )->getItems();

        foreach ($claims as $claim) {
            if ( ! $claim->getCost() || ! $claim->getDelta()) {
                continue;
            }

            $payment = $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setInvoice($invoice)
                ->setCost($claim->getDelta())
                ->setModerated(true)
                ->setVerified(true)
                ->setName($claim->getName() ? : $claim->getService()->getName())
            ;

            $this->paymentService->save($payment);
        }

        return response()->json(true);

    }

    public function get(int $invoiceId, int $paymentId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }
        if ( ! $invoiceId || ! $paymentId) {
            abort(412);
        }

        $payment = $this->paymentService->getById($paymentId);
        $invoice = $this->fetchInvoice($invoiceId);

        if ( ! $payment || ! $invoice) {
            abort(412);
        }

        $payment = $payment->setInvoice($invoice);

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(int $invoiceId, DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $payment = $this->saveInvoicePaymentCommand->execute(
            $invoiceId,
            $request->getIntOrNull('id'),
            $request->getFloat('cost'),
            $request->getStringOrNull('name'),
            $request->getStringOrNull('comment'),
            $request->getStringOrNull('paidAt'),
            $request->allFiles(),
        );

        if ($payment === null) {
            abort(404);
        }

        return response()->json([
            'payment' => new PaymentResource($payment),
        ]);
    }

    public function list(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        if ( ! $invoiceId) {
            abort(412);
        }

        $searcher = new PaymentSearcher();
        $searcher
            ->setWithFiles()
            ->setInvoiceId($invoiceId)
            ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $payments = $this->paymentService->search($searcher);

        $invoice = $this->fetchInvoice($invoiceId);
        $payments->setItems($payments->getItems()->map(function (PaymentEntity $payment) use ($invoice) {
            return $payment->setInvoice($invoice);
        }));

        return response()->json(new PaymentsListResource(
            $payments->getItems(),
        ));
    }

    public function delete(int $invoiceId, int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_DROP)) {
            abort(403);
        }

        return $this->paymentService->deleteById($id);
    }

    private function fetchInvoice(int $invoiceId): ?InvoiceEntity
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        $period  = $invoice ? $this->periodService->getById($invoice->getPeriodId()) : null;
        $invoice?->setPeriod($period);

        return $invoice;
    }
}
