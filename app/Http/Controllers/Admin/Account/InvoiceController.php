<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Invoices\InvoicesListResource;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;

    public function __construct()
    {
        $this->invoiceService = InvoiceLocator::InvoiceService();
    }

    public function list(int $accountId, DefaultRequest $request): JsonResponse
    {
        $searcher = InvoiceSearcher::make()
            ->setAccountId($accountId)
            ->setWithPeriod()
        ;

        $invoices = $this->invoiceService->search($searcher)->getItems();
        $invoices = $invoices->sort(function (InvoiceDTO $a, InvoiceDTO $b) {
            return $a->getPeriodId() < $b->getPeriodId();
        });

        return response()->json(new InvoicesListResource($invoices));
    }
}