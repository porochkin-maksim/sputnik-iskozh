<?php declare(strict_types=1);

namespace App\Http\Controllers\Common\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReceiptController extends Controller
{

    public function __construct(
        private readonly InvoiceService        $invoiceService,
        private readonly ClaimService          $claimService,
        private readonly AccountService        $accountService,
        private readonly PeriodService         $periodService,
        private readonly ServiceCatalogService $serviceService,
    )
    {
    }

    /**
     * Сгенерировать шаблон PDF-квитанции (открывается в браузере)
     */
    public function makeForBlank(DefaultRequest $request): BinaryFileResponse
    {
        $periodId = $request->getIntOrNull('period');
        $period   = $periodId ? $this->periodService->getById($periodId) : $this->periodService->getActive();

        if ( ! $period) {
            $period = $this->periodService->getActive();
        }

        if ( ! $period) {
            abort(404, 'Ни один период не найден');
        }

        $services = $this->serviceService->getByPeriodId($period->getId())->sortByTypes();

        $tempFile = sys_get_temp_dir() . '/receipt_blank_' . Str::uuid() . '.pdf';

        Pdf::view('exports.documents.receipt.receipt', compact('period', 'services'))
            ->format('a4')
            ->portrait()
            ->save($tempFile)
        ;

        return $this->makeResponse($tempFile, 'Бланк_квитанции.pdf');
    }

    /**
     * Сгенерировать PDF-квитанцию для счёта по uid счёта (зашифрованный id)
     */
    public function makeForInvoice(mixed $uid): BinaryFileResponse
    {
        $invoiceUid = $uid ? UidFacade::findReferenceId((string) $uid, UidTypeEnum::INVOICE) : null;

        return $this->makeByInvoiceId($invoiceUid);
    }

    /**
     * Сгенерировать PDF-квитанцию для счёта по ID (открывается в браузере)
     */
    public function makeByInvoiceId(mixed $id): BinaryFileResponse
    {
        $invoiceId = is_numeric($id) ? (int) $id : null;
        $invoice   = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setId($invoiceId)
                ->setWithAccount()
                ->setWithPeriod()
                ->setLimit(1),
        )->getItems()->first();

        if ( ! $invoice) {
            abort(404, 'Счёт не найден');
        }

        $invoice->setClaims($this->claimService->search(
            ClaimSearcher::make()
                ->setInvoiceId($invoice->getId())
                ->setWithService(),
        )->getItems());

        if ( ! $invoice->getAccount()) {
            $invoice->setAccount($this->accountService->getById($invoice->getAccountId()));
        }

        if ( ! $invoice->getPeriod()) {
            $invoice->setPeriod($this->periodService->getById($invoice->getPeriodId()));
        }

        $tempFile = sys_get_temp_dir() . '/receipt_' . $invoice->getId() . '_' . Str::uuid() . '.pdf';

        Pdf::view('exports.documents.receipt.receipt', compact('invoice'))
            ->format('a4')
            ->portrait()
            ->save($tempFile)
        ;

        return $this->makeResponse($tempFile, 'квитанция_' . $invoice->getId() . '.pdf');
    }

    private function makeResponse(string $tempFile, string $fileName): BinaryFileResponse
    {
        return response()->file($tempFile, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $fileName),
        ])->deleteFileAfterSend();
    }
}
