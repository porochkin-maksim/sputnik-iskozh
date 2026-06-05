<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Invoices\InvoicesListResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Http\JsonResponse;
use lc;

class InvoiceController extends Controller
{

    public function __construct(
        private readonly InvoiceService $invoiceService,
    )
    {
    }

    public function list(int $accountId, DefaultRequest $request): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::INVOICES_VIEW)) {
            abort(403);
        }

        $searcher = InvoiceSearcher::make()
            ->setAccountId($accountId)
            ->setWithPeriod()
        ;

        $invoices = $this->invoiceService->search($searcher)->getItems();
        $invoices = $invoices->sort(function (InvoiceEntity $a, InvoiceEntity $b) {
            return $a->getPeriodId() < $b->getPeriodId();
        });

        return response()->json([
            'invoices'   => new InvoicesListResource($invoices),
            'historyUrl' => HistoryChangesRoute::make(type: HistoryType::INVOICE),
        ]);
    }
}
