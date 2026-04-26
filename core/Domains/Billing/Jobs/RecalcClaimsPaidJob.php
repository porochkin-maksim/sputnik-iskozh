<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use App\Models\Billing\Claim;
use Core\Repositories\SearcherInterface;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use App\Services\Money\MoneyService;
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
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Задержка между попытками (секунды)
     */
    public int $backoff = 10;

    public function __construct(
        private readonly int $invoiceId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::RECALC_CLAIMS_PAID_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->invoiceId;
    }

    protected function process(
        InvoiceService $invoiceService,
        ClaimService $claimService,
        ServiceCatalogService $serviceService,
        ClaimFactory $claimFactory,
    ): void
    {
        $searcher = new InvoiceSearcher();
        $searcher
            ->setId($this->invoiceId)
            ->setWithClaims()
            ->setWithPayments()
        ;

        $invoice = $invoiceService->search($searcher)->getItems()->first();

        if ( ! $invoice) {
            return;
        }

        $totalPaid = ($invoice->getPayments() ? : new PaymentCollection())
            ->getVerified()
            ->getTotalCostMoney()
        ;

        $sortedClaims = $claimService->search((new ClaimSearcher())
            ->setInvoiceId($invoice->getId())
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();
        $sortedClaims = $sortedClaims->sortByServiceTypes();
        $advanceClaim = $sortedClaims->getAdvancePayment();

        // Сбрасываем paid у всех
        foreach ($sortedClaims as $claim) {
            $claim->setPaid(0);
        }

        // Распределяем оплату
        $remaining = $totalPaid;
        foreach ($sortedClaims as $claim) {
            if ($claim->getId() === $advanceClaim?->getId()) {
                continue;
            }

            $claimCost = MoneyService::parse($claim->getCost());

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
            $service = $serviceService->search(
                ServiceSearcher::make()
                    ->setPeriodId($invoice->getPeriodId())
                    ->setType(ServiceTypeEnum::ADVANCE_PAYMENT),
            )->getItems()->first();


            if ($service) {
                if ($advanceClaim) {
                    $advanceClaim
                        ->setTariff(MoneyService::toFloat($remaining))
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                }
                else {
                    $advanceClaim = $claimFactory->makeDefault()
                        ->setInvoiceId($invoice->getId())
                        ->setServiceId($service->getId())
                        ->setCost(MoneyService::toFloat($remaining))
                        ->setPaid(MoneyService::toFloat($remaining))
                        ->setName('Аванс')
                    ;
                    $sortedClaims->push($advanceClaim);
                }
            }
        }
        elseif ($advanceClaim && $advanceClaim->getPaid() > 0) {
            // Если остатка нет, но авансовый claim есть – удаляем его
            $sortedClaims->removeById($advanceClaim->getId());
            $claimService->deleteById($advanceClaim->getId());
        }

        // Сохраняем все claims
        $savedClaims = $claimService->saveCollection($sortedClaims);

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
        $invoice->setDebt((float) $sortedClaims->getDebts()?->getCost());

        $invoiceService->save($invoice);
    }
}
