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

        $claims     = $invoice->getClaims() ? : new ClaimCollection();
        $totalPayed = ($invoice->getPayments() ? : new PaymentCollection())
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
        $claims = $claims->sortByServiceTypes();

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

        $overpayedClaim = $claims->getByServiceType(ServiceTypeEnum::ADVANCE_PAYMENT);
        // если платежей больше чем в счёте
        if ($totalPayed->isPositive()) {
            // фиксируем переплату как аванс
            $service = $overpayedClaim
                ? : ServiceLocator::ServiceService()->search(
                    ServiceSearcher::make()
                        ->setPeriodId($invoice->getPeriodId())
                        ->setActive(true)
                        ->setType(ServiceTypeEnum::ADVANCE_PAYMENT),
                )->getItems()->first();

            $claim = $overpayedClaim
                ? : ClaimLocator::ClaimFactory()->makeDefault()
                    ->setInvoiceId($invoice->getId())
                    ->setServiceId($service->getId())
            ;

            $claim
                ->setTariff(MoneyService::toFloat($totalPayed))
                ->setCost(MoneyService::toFloat($totalPayed))
                ->setPayed(MoneyService::toFloat($totalPayed))
            ;

            if ( ! $overpayedClaim) {
                $claims->push($claim);
            }
        }
        elseif ($overpayedClaim) {
            $overpayedClaim
                ->setCost(0)
                ->setTariff(0)
                ->setPayed(0)
            ;
        }

        $claims = ClaimLocator::ClaimService()->saveCollection($claims);

        // пересчитываем счёт
        $totalCost  = MoneyService::parse(0);
        $totalPayed = MoneyService::parse(0);
        foreach ($claims as $claim) {
            $cost      = MoneyService::parse($claim->getCost());
            $totalCost = $totalCost->add($cost);

            $payed      = MoneyService::parse($claim->getPayed());
            $totalPayed = $totalPayed->add($payed);
        }
        $invoice->setCost(MoneyService::toFloat($totalCost));
        $invoice->setPayed(MoneyService::toFloat($totalPayed));

        InvoiceLocator::InvoiceService()->save($invoice);
    }
}
