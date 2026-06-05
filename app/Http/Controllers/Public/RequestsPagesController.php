<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Illuminate\Contracts\View\View;
use lc;

class RequestsPagesController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly PeriodService  $periodService,
        private readonly AccountService $accountService,
    )
    {
    }

    public function index(): View
    {
        return view('pages.public.contacts.requests');
    }

    public function payment(DefaultRequest $request): View
    {
        $invoiceUid = $request->getStringOrNull('invoice')
            ? UidFacade::findReferenceId($request->getString('invoice'), UidTypeEnum::INVOICE)
            : null;

        $invoice = null;
        if ($invoiceUid) {
            $invoice = $this->invoiceService->getById($invoiceUid);
            if ($invoice) {
                $period = $this->periodService->getById($invoice->getPeriodId());
                $invoice->setPeriod($period);
                $account = $invoice->getAccountId() === lc::account()->getId()
                    ? lc::account()
                    : $this->accountService->getById($invoice->getAccountId());
                $invoice->setAccount($account);
            }
        }

        return view('pages.public.contacts.payment', compact('invoice'));
    }

    public function counter(): View
    {
        return view('pages.public.contacts.counter');
    }
}
