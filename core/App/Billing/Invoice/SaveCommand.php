<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Jobs\Billing\CreateClaimsAndPaymentsForRegularInvoiceJob;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;

readonly class SaveCommand
{
    public function __construct(
        private InvoiceFactory $invoiceFactory,
        private InvoiceService $invoiceService,
        private SaveValidator $validator,
    )
    {
    }

    public function execute(
        ?int $id,
        int $periodId,
        int $accountId,
        ?int $type,
        ?string $name,
        ?float $rounding = null,
    ): ?InvoiceEntity {
        $this->validator->validate($periodId, $accountId, $type, $name);

        $invoice = $id
            ? $this->invoiceService->getById($id)
            : $this->invoiceFactory->makeDefault()
                ->setType(InvoiceTypeEnum::tryFrom($type))
                ->setPeriodId($periodId)
                ->setAccountId($accountId);

        if (! $invoice) {
            return null;
        }

        $invoice->setName($name);

        if ($rounding !== null) {
            $invoice->setRounding($rounding);
        }

        $invoice = $this->invoiceService->save($invoice);

        if ($id === null) {
            CreateClaimsAndPaymentsForRegularInvoiceJob::dispatchIfNeeded($invoice->getId());
        }

        return $invoice;
    }
}
