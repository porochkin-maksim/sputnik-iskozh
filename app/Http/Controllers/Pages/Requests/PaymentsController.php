<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Requests\ProposalCreateRequest;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\FileService;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    private PaymentService $paymentService;
    private PaymentFactory $paymentFactory;
    private FileService    $fileService;

    public function __construct()
    {
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
        $this->fileService    = PaymentLocator::FileService();
    }

    public function create(ProposalCreateRequest $request): void
    {
        DB::beginTransaction();
        $payment = $this->paymentFactory->makeDefault();
        $payment->setComment($request->getFullText());

        $payment = $this->paymentService->save($payment);

        foreach ($request->allFiles() as $file) {
            $this->fileService->store($file, $payment->getId());
        }

        DB::commit();
    }
}
