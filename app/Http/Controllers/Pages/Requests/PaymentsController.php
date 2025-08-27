<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Requests\PaymentCreateRequest;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
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
    private AccountService $accountService;
    private InvoiceService $invoiceService;

    public function __construct()
    {
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
        $this->fileService    = PaymentLocator::FileService();
        $this->accountService = AccountLocator::AccountService();
        $this->invoiceService = InvoiceLocator::InvoiceService();
    }

    public function create(PaymentCreateRequest $request): void
    {
        DB::beginTransaction();
        try {
            $payment = $this->paymentFactory->makeDefault();

            if ($request->getInvoice()) {
                $invoice = $this->invoiceService->getById($request->getInvoice());
                if ($invoice) {
                    $payment->setInvoiceId($invoice->getId());
                    $payment->setAccountId($invoice->getAccountId());
                }
            }
            elseif ($request->getAccount()) {
                $account = $this->accountService->findByNumber($request->getAccount());
                if ($account) {
                    $payment->setAccountId($account->getId());
                }
            }
            if ( ! isset($invoice)) {
                $payment->setComment($request->getFullText());
            }
            else {
                $payment->setComment($request->getText());
            }
            $payment->setCost($request->getCost());

            $payment = $this->paymentService->save($payment);

            foreach ($request->allFiles() as $file) {
                $this->fileService->store($file, $payment->getId());
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
