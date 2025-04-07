<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Claim\Events\ClaimsUpdatedEvent;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

/**
 * Создаёт claims для регулярного счёта - членский и целевые взносы
 */
class CreateClaimsForRegularInvoiceJob implements ShouldQueue
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
        $invoice = InvoiceLocator::InvoiceService()->getById($this->invoiceId);
        if ( ! $invoice) {
            throw new RuntimeException("Счёт не найден #{$this->invoiceId}");
        }

        if ($invoice->getType() !== InvoiceTypeEnum::REGULAR) {
            return;
        }

        if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
            return;
        }

        $claimService   = ClaimLocator::ClaimService();
        $claimFactory   = ClaimLocator::ClaimFactory();
        $accountService = AccountLocator::AccountService();

        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($invoice->getPeriodId())
            ->setActive(true)
        ;

        foreach (ServiceLocator::ServiceService()->search($serviceSearcher)->getItems() as $service) {
            if ( ! in_array($service->getType(), [
                ServiceTypeEnum::MEMBERSHIP_FEE,
                ServiceTypeEnum::TARGET_FEE,
            ], true)) {
                continue;
            }
            if ($service->getType() === ServiceTypeEnum::MEMBERSHIP_FEE) {
                $tariff = MoneyService::parse($service->getCost());
                $size   = (int) $accountService->getById($invoice->getAccountId())?->getSize();
                $cost   = $tariff->multiply($size);
            }
            else {
                $cost = MoneyService::parse($service->getCost());
            }

            $claim = $claimFactory->makeDefault();
            $claim->setInvoiceId($this->invoiceId)
                ->setServiceId($service->getId())
                ->setTariff($service->getCost())
                ->setCost(MoneyService::toFloat($cost))
                ->setPayed(0.00)
            ;

            $claimService->save($claim);
        }

        ClaimsUpdatedEvent::dispatch($invoice->getId());
    }
}
