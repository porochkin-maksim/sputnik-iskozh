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
class RecalcClaimsPaidJob implements ShouldQueue
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

        $claims    = $invoice->getClaims() ? : new ClaimCollection();
        $totalPaid = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney()
        ;

        // Находим авансовый claim (если есть)
        $advanceClaim = $claims->findByServiceType(ServiceTypeEnum::ADVANCE_PAYMENT);

        // Вычитаем из общей суммы уже оплаченное через авансовый claim
        if ($advanceClaim && $advanceClaim->getPaid() > 0) {
            $totalPaid = $totalPaid->subtract(MoneyService::parse($advanceClaim->getPaid()));
        }

        // Загружаем claims с сервисами для сортировки
        $claimSearcher = new ClaimSearcher();
        $claimSearcher
            ->setIds($claims->getIds())
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        $sortedClaims = ClaimLocator::ClaimService()->search($claimSearcher)->getItems();
        $sortedClaims = $sortedClaims->sortByServiceTypes();

        // Сбрасываем paid у всех, кроме авансового
        foreach ($sortedClaims as $claim) {
            if ($claim->getId() !== $advanceClaim?->getId()) {
                $claim->setPaid(0);
            }
        }

        // Распределяем оплату
        $remaining = $totalPaid;
        foreach ($sortedClaims as $claim) {
            if ($claim->getId() === $advanceClaim?->getId()) {
                continue;
            }

            $claimCost = MoneyService::parse($claim->getCost());
            $claimPaid = MoneyService::parse(0);

            if ($remaining->subtract($claimCost)->isPositive()) {
                $claimPaid = $claimCost;
            }
            else {
                $claimPaid = $remaining;
            }

            $remaining = $remaining->subtract($claimPaid);
            $claim->setPaid(MoneyService::toFloat($claimPaid));

            if ($remaining->isZero()) {
                break;
            }
        }

        // Обновляем или создаём авансовый claim
        if ($remaining->isPositive()) {
            $service = ServiceLocator::ServiceService()->search(
                ServiceSearcher::make()
                    ->setPeriodId($invoice->getPeriodId())
                    ->setActive(true)
                    ->setType(ServiceTypeEnum::ADVANCE_PAYMENT),
            )->getItems()->first();

            if ($service) {
                if ($advanceClaim) {
                    $advanceClaim
                        ->setTariff(MoneyService::toFloat($remaining))
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                    ;
                }
                else {
                    $advanceClaim = ClaimLocator::ClaimFactory()->makeDefault()
                        ->setInvoiceId($invoice->getId())
                        ->setServiceId($service->getId())
                        ->setTariff(MoneyService::toFloat($remaining))
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                    ;
                    $sortedClaims->push($advanceClaim);
                }
            }
        }
        elseif ($advanceClaim && $advanceClaim->getPaid() > 0) {
            // Если остатка нет, но авансовый claim есть – обнуляем его
            $advanceClaim
                ->setTariff(0)
                ->setCost(0)
                ->setPaid(0)
            ;
        }

        // Сохраняем все claims
        $savedClaims = ClaimLocator::ClaimService()->saveCollection($sortedClaims);

        // Пересчитываем счёт
        $totalCost    = MoneyService::parse(0);
        $totalPaidSum = MoneyService::parse(0);
        foreach ($savedClaims as $claim) {
            $totalCost    = $totalCost->add(MoneyService::parse($claim->getCost()));
            $totalPaidSum = $totalPaidSum->add(MoneyService::parse($claim->getPaid()));
        }

        $invoice->setCost(MoneyService::toFloat($totalCost));
        $invoice->setPaid(MoneyService::toFloat($totalPaidSum));
        $invoice->setAdvance((float) $advanceClaim?->getCost());
        $invoice->setDebt((float) $sortedClaims->getDebt()?->getCost());

        InvoiceLocator::InvoiceService()->save($invoice);
    }
}
