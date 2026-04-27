<?php declare(strict_types=1);

namespace App\Http\Controllers\Common\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Service\Services\ServiceService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReceiptController extends Controller
{
    private InvoiceService $invoiceService;
    private PeriodService  $periodService;
    private ServiceService $serviceService;

    public function __construct()
    {
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->periodService  = PeriodLocator::PeriodService();
        $this->serviceService = ServiceLocator::ServiceService();
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
        $invoice = $this->invoiceService->getById(is_numeric($id) ? (int) $id : null);

        if ( ! $invoice) {
            abort(404, 'Счёт не найден');
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

