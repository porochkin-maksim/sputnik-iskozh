<?php declare(strict_types=1);

use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call('billing:period:create-services');

        $claimService = ClaimLocator::ClaimService();
        $claimIds     = $claimService->search(ClaimSearcher::make()->setName('Переплата'))->getItems()->getIds();
        foreach ($claimIds as $claimId) {
            $claimService->deleteById($claimId);
        }

        $debtServices = ServiceLocator::ServiceService()->search(
            ServiceSearcher::make()->setType(ServiceTypeEnum::DEBT),
        )->getItems();

        $invoices = InvoiceLocator::InvoiceService()->search(InvoiceSearcher::make())->getItems();
        foreach ($invoices as $invoice) {
            $debtServiceId = $debtServices->getByPeriodId($invoice->getPeriodId())->first()->getId();
            $debtClaim     = ClaimLocator::ClaimService()->search(
                ClaimSearcher::make()
                    ->setLimit(1)
                    ->setInvoiceId($invoice->getId())
                    ->setServiceId($debtServiceId),
            )->getItems()->first();

            $debtClaim = $debtClaim ?: ClaimLocator::ClaimFactory()->makeDefault()
                    ->setInvoiceId($invoice->getId())
                    ->setServiceId($debtServiceId)
            ;

            ClaimLocator::ClaimService()->save($debtClaim);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
