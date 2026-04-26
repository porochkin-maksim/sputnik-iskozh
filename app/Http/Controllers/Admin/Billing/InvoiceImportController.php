<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\Billing\Invoice\InvoiceImportService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Illuminate\Http\JsonResponse;

class InvoiceImportController extends Controller
{
    public function __construct(
        private readonly PeriodService        $periodService,
        private readonly InvoiceImportService $invoiceImportService,
    )
    {
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

        $result = $this->invoiceImportService->parseFile(
            $fileMain,
            $filePrev,
            $period,
            $colAccrued,
            $colPaid,
            $colDebt,
        );

        return response()->json($result);
    }

    public function save(string $periodId, DefaultRequest $request): void
    {
        $this->invoiceImportService->savePayments($request->getArray('payments'));
    }

    private function fetchPeriod(int $periodId): PeriodEntity
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
