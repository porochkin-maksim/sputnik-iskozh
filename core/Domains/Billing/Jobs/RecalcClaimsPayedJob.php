<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Claim;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Пересчитывает оплату claims в счёте по платежам
 */
class RecalcClaimsPayedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $searcher = new InvoiceSearcher();
        $searcher
            ->setId($this->invoiceId)
            ->setWithClaims()
            ->setWithPayments()
        ;

        $invoice = InvoiceLocator::InvoiceService()->search($searcher)->getItems()->first();

        if ( ! $invoice) {
            return;
        }

        $claims = $invoice->getClaims() ? : new ClaimCollection();
        $totalPayed   = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney()
        ;

        $claimSearcher = new ClaimSearcher();
        $claimSearcher
            ->setIds($claims->getIds())
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $claims = ClaimLocator::ClaimService()->search($claimSearcher)->getItems();
        $claims = $claims->sortByServiceTypes([
            ServiceTypeEnum::MEMBERSHIP_FEE,
            ServiceTypeEnum::TARGET_FEE,
            ServiceTypeEnum::ELECTRIC_TARIFF,
            ServiceTypeEnum::OTHER,
        ]);

        foreach ($claims as $claim) {
            $claim->setPayed(0);
        }

        foreach ($claims as $claim) {
            $claimCost  = MoneyService::parse($claim->getCost());
            $claimPayed = MoneyService::parse(0);

            // если оплачено больше чем стоимость claim
            if ($totalPayed->subtract($claimCost)->isPositive()) {
                // фиксируем, что claim оплачена полностью
                $claimPayed = $claimPayed->add($claimCost);
            }
            else {
                // фиксируем, что claim оплачена частично
                $claimPayed = $claimPayed->add($totalPayed);
            }

            // вычитаем из платежей стоимость claim
            $totalPayed = $totalPayed->subtract($claimPayed);

            $claim->setPayed(MoneyService::toFloat($claimPayed));
            if ($totalPayed->isZero()) {
                break;
            }
        }

        // если платежей больше чем в счёте
        if ($totalPayed->isPositive()) {
            dispatch_sync(new CreateOtherServiceJob($invoice->getPeriodId()));

            $serviceSearcher = new ServiceSearcher();
            $serviceSearcher
                ->setPeriodId($invoice->getPeriodId())
                ->setActive(true)
                ->setType(ServiceTypeEnum::OTHER)
            ;
            $service = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems()->first();

            $claim = ClaimLocator::ClaimFactory()->makeDefault()
                ->setInvoiceId($invoice->getId())
                ->setServiceId($service->getId())
                ->setTariff(MoneyService::toFloat($totalPayed))
                ->setCost(MoneyService::toFloat($totalPayed))
                ->setPayed(MoneyService::toFloat($totalPayed))
                ->setName('Переплата')
            ;

            $claims->push($claim);
        }

        ClaimLocator::ClaimService()->saveCollection($claims);
    }
}
