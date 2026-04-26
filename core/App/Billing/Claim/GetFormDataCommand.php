<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use App\Models\Billing\Claim;
use App\Models\Billing\Service;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Repositories\SearcherInterface;

readonly class GetFormDataCommand
{
    public function __construct(
        private ClaimFactory          $claimFactory,
        private ClaimService          $claimService,
        private InvoiceService        $invoiceService,
        private ServiceCatalogService $serviceService,
        private PeriodService         $periodService,
    )
    {
    }

    public function create(int $invoiceId): ?ClaimFormData
    {
        $invoice = $this->fetchInvoice($invoiceId);
        if ($invoice === null) {
            return null;
        }

        $services = $this->getAvailableServices($invoice);
        $claim    = $this->claimFactory->makeDefault()
            ->setInvoiceId($invoiceId)
            ->setServiceId($services->first()?->getId())
            ->setInvoice($invoice)
        ;

        return new ClaimFormData(
            servicesSelect: $this->makeServicesMap($services),
            claim         : $claim,
            services      : $services,
        );
    }

    public function get(int $invoiceId, int $claimId): ?ClaimFormData
    {
        $invoice = $this->fetchInvoice($invoiceId);
        $claim   = $this->claimService->getById($claimId);

        if ($invoice === null || $claim === null) {
            return null;
        }

        $claimService = $this->serviceService->getById($claim->getServiceId());
        if ($claimService === null) {
            return null;
        }

        $services                               = $this->getAvailableServices($invoice);
        $servicesSelect                         = $this->makeServicesMap($services);
        $servicesSelect[$claimService->getId()] = $claimService->getName();

        $claim
            ->setService($claimService)
            ->setInvoice($invoice)
        ;

        return new ClaimFormData(
            servicesSelect: $servicesSelect,
            claim         : $claim,
            services      : $services,
        );
    }

    private function getAvailableServices(InvoiceEntity $invoice): ServiceCollection
    {
        $claims = $this->claimService->search(
            (new ClaimSearcher())
                ->setInvoiceId($invoice->getId())
                ->setWithService()
                ->setSortOrderProperty(Claim::ID, SearcherInterface::SORT_ORDER_ASC)
                ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC),
        )->getItems()->sortByServiceTypes();

        $existingServiceIds = array_map(
            static fn(ClaimEntity $claim): ?int => $claim->getServiceId(),
            $claims->toArray(),
        );

        $services = $this->serviceService->search(
            (new ServiceSearcher())
                ->setPeriodId($invoice->getPeriodId())
                ->setSortOrderProperty(Service::TYPE, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();

        if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
            return $services->filter(static function (ServiceEntity $service) use ($existingServiceIds) {
                return ! in_array($service->getId(), $existingServiceIds, true)
                       || in_array($service->getType(), [ServiceTypeEnum::ELECTRIC_TARIFF, ServiceTypeEnum::OTHER], true);
            });
        }

        return $services->filter(static function (ServiceEntity $service) {
            return in_array($service->getType(), [
                ServiceTypeEnum::ELECTRIC_TARIFF,
                ServiceTypeEnum::OTHER,
            ], true);
        });
    }

    /**
     * @param ServiceCollection $services
     *
     * @return array<int, string>
     */
    private function makeServicesMap(ServiceCollection $services): array
    {
        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->getId()] = $service->getName();
        }

        return $servicesMap;
    }

    private function fetchInvoice(int $invoiceId): ?InvoiceEntity
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        $period  = $invoice ? $this->periodService->getById($invoice->getPeriodId()) : null;
        $invoice?->setPeriod($period);

        return $invoice;
    }
}
