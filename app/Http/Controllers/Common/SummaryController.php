<?php declare(strict_types=1);

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
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
    private AccountService $accountService;

    public function __construct()
    {
        $this->summaryService = SummaryLocator::SummaryService();
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->accountService = AccountLocator::AccountService();
    }

    public function summary(DefaultRequest $request): JsonResponse
    {
        $type       = $request->getIntOrNull('type');
        $periodId   = $request->getIntOrNull('period_id');
        $accountIds = null;

        if ($request->getIntOrNull('account_id')) {
            $accountIds = [$request->getIntOrNull('account_id')];
        }
        elseif ($request->getSearch()) {
            $accountIds = $this->accountService->search(
                AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$request->getSearch()}%"),
            )->getItems()->getIds();
        }

        return response()->json($this->summaryService->getSummaryFor($type, $periodId, $accountIds));
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

        $claims = $invoiceIds ? $this->summaryService->getClaimsFor($invoiceIds) : [];

        return response()->json($claims);
    }
}