<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceImportService;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Illuminate\Http\JsonResponse;

class PaymentImportController extends Controller
{
    private PeriodService        $periodService;
    private InvoiceImportService $paymentImportService;

    public function __construct()
    {
        $this->periodService        = PeriodLocator::PeriodService();
        $this->paymentImportService = InvoiceLocator::InvoiceImportService();
    }

    public function index(int $periodId)
    {
        $period = $this->fetchPeriod($periodId);

        return view('admin.pages.invoices.payments.import', compact('period'));
    }

    public function parseFile(int $periodId, DefaultRequest $request): JsonResponse
    {
        $period = $this->fetchPeriod($periodId);

        $fileMain   = $request->file('file_main');
        $filePrev   = $request->file('file_prev');
        $mode       = $request->getString('mode');
        $colAccrued = $request->input('col_accrued');
        $colPaid    = $request->input('col_paid');
        $colDebt    = $request->input('col_debt');

        if ( ! $fileMain) {
            abort(412, 'Не передан основной файл');
        }

        if ($mode === 'diff' && ! $filePrev) {
            abort(412, 'Не передан предыдущий файл');
        }

        try {
            $result = $this->paymentImportService->parseFile(
                $fileMain,
                $filePrev,
                $period,
                $colAccrued,
                $colPaid,
                $colDebt,
            );

            return response()->json($result);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function save(string $periodId, DefaultRequest $request): void
    {
        $this->paymentImportService->savePayments($request->getArray('payments'));
    }

    private function fetchPeriod(int $periodId): PeriodDTO
    {
        $period = $this->periodService->getById($periodId);
        if ( ! $period) {
            abort(404, 'Период не найден');
        }

        if ($period->isClosed()) {
            abort(403, 'Период закрыт');
        }

        return $period;
    }
}
