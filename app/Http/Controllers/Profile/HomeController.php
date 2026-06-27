<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Models\Billing\Period;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Repositories\SearcherInterface;
use Illuminate\Contracts\View\View;
use lc;

class HomeController extends Controller
{
    public function __construct(
        private readonly PeriodService         $periodService,
        private readonly ServiceCatalogService $serviceCatalogService,
        private readonly InvoiceService        $invoiceService,
        private readonly PaymentService        $paymentService,
        private readonly AcquiringService      $acquiringService,
    )
    {
    }

    public function index(): View
    {
        return view('pages.profile.index');
    }

    public function invoices(DefaultRequest $request): View
    {
        $periods = $this->periodService->search(
            PeriodSearcher::make()->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC),
        )->getItems();

        $periodId = $request->getInt('period', null);
        $period   = $this->periodService->getById($periodId) ? : $periods->first();

        $invoices  = new InvoiceCollection();
        $payments  = new PaymentCollection();
        $services  = null;
        $totalDebt = 0.0;

        $accountId = lc::account()->getId();

        if ($accountId && $period) {
            $services = $this->serviceCatalogService->search(
                new ServiceSearcher()->setPeriodId($period->getId()),
            )->getItems();

            $invoices = $this->invoiceService->search(
                new InvoiceSearcher()
                    ->setWithPayments()
                    ->setWithClaims()
                    ->setPeriodId($period->getId())
                    ->setAccountId($accountId),
            )->getItems();

            $payments = $this->paymentService->search(
                new PaymentSearcher()->setInvoiceIds($invoices->getIds()),
            )->getItems();

            $openPeriods = $this->periodService->search(
                PeriodSearcher::make()->setIsClosed(false),
            )->getItems();

            foreach ($openPeriods as $openPeriod) {
                $openInvoices = $this->invoiceService->search(
                    new InvoiceSearcher()
                        ->setPeriodId($openPeriod->getId())
                        ->setAccountId($accountId),
                )->getItems();

                foreach ($openInvoices as $inv) {
                    $totalDebt += $inv->getDelta();
                }
            }
        }

        $acquiringAvailable = $this->acquiringService->isAvailable();
        $account            = lc::account();

        return view('pages.profile.invoices', compact(
            'periods',
            'period',
            'services',
            'invoices',
            'payments',
            'acquiringAvailable',
            'account',
            'totalDebt',
        ));
    }
}
