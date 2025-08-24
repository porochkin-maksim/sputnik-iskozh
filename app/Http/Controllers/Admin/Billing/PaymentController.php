<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Payments\SaveRequest;
use App\Http\Resources\Admin\Payments\PaymentResource;
use App\Http\Resources\Admin\Payments\PaymentsListResource;
use App\Models\Billing\Payment;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\Services\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\FileService;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Illuminate\Http\JsonResponse;
use lc;

class PaymentController extends Controller
{
    private PaymentFactory $paymentFactory;
    private PaymentService $paymentService;
    private InvoiceService $invoiceService;
    private FileService    $fileService;
    private PeriodService  $periodService;
    private ClaimService   $claimService;

    public function __construct()
    {
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
        $this->fileService    = PaymentLocator::FileService();
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->periodService  = PeriodLocator::PeriodService();
        $this->claimService   = ClaimLocator::ClaimService();
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
            ->setInvoice($invoice)
            ->setPayedAt(Carbon::now())
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
            if (! $claim->getCost() || ! $claim->getDelta()) {
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

    public function save(int $invoiceId, SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $payment = $request->getId()
            ? $this->paymentService->getById($request->getId())
            : $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setAccountId($invoice->getAccountId())
        ;

        if ( ! $payment) {
            abort(404);
        }

        $payment
            ->setModerated(true)
            ->setVerified(true)
            ->setCost($request->getCost())
            ->setName($request->getName())
            ->setComment($request->getComment())
            ->setPayedAt($request->getPayedAt())
        ;

        $payment = $this->paymentService->save($payment);

        foreach ($request->allFiles() as $file) {
            $this->fileService->store($file, $payment->getId());
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

        $searcher = new PaymentSearcher();
        $searcher
            ->setWithFiles()
            ->setInvoiceId($invoiceId)
            ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $payments = $this->paymentService->search($searcher);

        $invoice = $this->fetchInvoice($invoiceId);
        $payments->setItems($payments->getItems()->map(function (PaymentDTO $paymentDTO) use ($invoice) {
            return $paymentDTO->setInvoice($invoice);
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

    private function fetchInvoice(int $invoiceId): ?InvoiceDTO
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        $period  = $invoice ? $this->periodService->getById($invoice->getPeriodId()) : null;
        $invoice?->setPeriod($period);

        return $invoice;
    }
}
