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
use Spatie\LaravelPdf\Facades\Pdf;

/**
 * Контроллер генерации квитанций на оплату для участка
 */
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
     * Сгенерировать шаблон PDF-квитанции
     */
    public function makeForBlank(DefaultRequest $request): mixed
    {
        $periodId = $request->getIntOrNull('period');
        $period   = $periodId ? $this->periodService->getById($periodId) : $this->periodService->getCurrentPeriod();

        if ( ! $period) {
            $period = $this->periodService->getLast();
        }

        if ( ! $period) {
            abort(404, 'Ни один период не найден');
        }

        $services = $this->serviceService->getByPeriodId($period->getId())->sortByTypes();

        return Pdf::view('exports.documents.receipt.receipt', compact('period', 'services'))
            ->format('a4')
            ->portrait()
            ->inline('Бланк квитанция.pdf')
        ;
    }

    /**
     * Сгенерировать PDF-квитанцию для счёта по uid счёта (зашифрованный id)
     */
    public function makeForInvoice(mixed $uid): mixed
    {
        $invoiceUid = $uid ? UidFacade::findReferenceId((string) $uid, UidTypeEnum::INVOICE) : null;

        return $this->makeByInvoiceId($invoiceUid);
    }

    /**
     * Сгенерировать PDF-квитанцию для счёта по uid счёта (зашифрованный id)
     */
    public function makeByInvoiceId(mixed $id): mixed
    {
        $invoice = $this->invoiceService->getById(is_numeric($id) ? (int) $id : null);

        if ( ! $invoice) {
            abort(404, 'Счёт не найден');
        }

        return Pdf::view('exports.documents.receipt.receipt', compact('invoice'))
            ->format('a4')
            ->portrait()
            ->inline('квитанция-' . $invoice->getId() . '.pdf')
        ;
    }
}
