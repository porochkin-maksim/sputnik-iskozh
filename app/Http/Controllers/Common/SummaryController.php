<?php declare(strict_types=1);

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Models\Billing\Invoice;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Summary\Services\SummaryService;
use Core\Domains\Billing\Summary\SummaryLocator;
use Illuminate\Http\JsonResponse;

class SummaryController extends Controller
{
    private SummaryService $summaryService;
    private InvoiceService $invoiceService;

    public function __construct()
    {
        $this->summaryService = SummaryLocator::SummaryService();
        $this->invoiceService = InvoiceLocator::InvoiceService();
    }

    public function summary(DefaultRequest $request): JsonResponse
    {
        $type      = $request->getIntOrNull('type');
        $periodId  = $request->getIntOrNull('period_id');
        $accountId = $request->getIntOrNull('account_id');

        return response()->json($this->summaryService->getSummaryFor($type, $periodId, $accountId));
    }

    public function summaryDetailing(string $type, DefaultRequest $request): JsonResponse
    {
        $typeCodes = match ($type) {
            'outcome' => [InvoiceTypeEnum::OUTCOME->value],
            default   => [InvoiceTypeEnum::INCOME->value, InvoiceTypeEnum::REGULAR->value],
        };

        $periodId = $request->getIntOrNull('period_id');

        $invoicesSearcher = new InvoiceSearcher();
        $invoicesSearcher->addWhere(Invoice::TYPE, SearcherInterface::IN, $typeCodes);

        if ($periodId) {
            $invoicesSearcher->setPeriodId($periodId);
        }

        $invoiceIds = $this->invoiceService->search($invoicesSearcher)->getItems()->getIds();

        $transactions = $this->summaryService->getTransactionsFor($invoiceIds);

        return response()->json($transactions);
    }
}