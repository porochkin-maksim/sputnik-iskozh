<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Claims\SaveRequest;
use App\Http\Resources\Admin\Claims\ServicesListResource;
use App\Http\Resources\Admin\Claims\ClaimResource;
use App\Http\Resources\Admin\Claims\ClaimsListResource;
use App\Http\Resources\Common\SelectResource;
use App\Models\Billing\Claim;
use App\Models\Billing\Service;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Service\Services\ServiceService;
use Core\Domains\Billing\Claim\Factories\ClaimFactory;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\Responses\SearchResponse;
use Core\Domains\Billing\Claim\Services\ClaimService;
use Core\Domains\Billing\Claim\ClaimLocator;
use Illuminate\Http\JsonResponse;
use lc;

class ClaimController extends Controller
{
    private ClaimFactory   $claimFactory;
    private ClaimService   $claimService;
    private InvoiceService $invoiceService;
    private ServiceService $serviceService;

    public function __construct()
    {
        $this->claimService   = ClaimLocator::ClaimService();
        $this->claimFactory   = ClaimLocator::ClaimFactory();
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->serviceService = ServiceLocator::ServiceService();
    }

    public function create(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_EDIT)) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $claims             = $this->getInvoiceClaims($invoice->getId())->getItems()->sortByServiceTypes();
        $existingServiceIds = array_map(static fn(ClaimDTO $claim) => $claim->getServiceId(), $claims->toArray());

        $services = $this->serviceService->search(
            (new ServiceSearcher())
                ->setPeriodId($invoice->getPeriodId())
                ->setSortOrderProperty(Service::TYPE, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();

        if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
            $services = $services->filter(static function (ServiceDTO $service) use ($existingServiceIds) {
                return ! in_array($service->getId(), $existingServiceIds, true)
                       || in_array($service->getType(), [ServiceTypeEnum::ELECTRIC_TARIFF, ServiceTypeEnum::OTHER], true);
            });
        }
        else {
            $services = $services->filter(static function (ServiceDTO $service) {
                return in_array($service->getType(), [
                    ServiceTypeEnum::ELECTRIC_TARIFF,
                    ServiceTypeEnum::OTHER,
                ], true);
            });
        }

        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->getId()] = $service->getName();
        }
        $claim = $this->claimFactory->makeDefault()->setServiceId($services->first()?->getId());

        return response()->json([
            'servicesSelect' => new SelectResource($servicesMap),
            'claim'          => new ClaimResource($claim),
            'services'       => new ServicesListResource($services),
        ]);
    }

    public function get(int $invoiceId, int $claimId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_VIEW)) {
            abort(403);
        }
        if ( ! $invoiceId || ! $claimId) {
            abort(412);
        }

        $claim        = $this->claimService->getById($claimId);
        $invoice      = $this->invoiceService->getById($invoiceId);
        $claimService = $this->serviceService->getById($claim?->getServiceId());
        if ( ! $claim || ! $invoice || ! $claimService) {
            abort(412);
        }

        $claims             = $this->getInvoiceClaims($invoice->getId())->getItems()->sortByServiceTypes();
        $existingServiceIds = array_map(static fn(ClaimDTO $claim) => $claim->getServiceId(), $claims->toArray());

        $services = $this->serviceService->search(
            (new ServiceSearcher())
                ->setPeriodId($invoice->getPeriodId())
                ->setSortOrderProperty(Service::TYPE, SearcherInterface::SORT_ORDER_ASC),
        )->getItems();

        if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
            $services = $services->filter(static function (ServiceDTO $service) use ($existingServiceIds) {
                return ! in_array($service->getId(), $existingServiceIds, true)
                       || in_array($service->getType(), [ServiceTypeEnum::ELECTRIC_TARIFF, ServiceTypeEnum::OTHER], true);
            });
        }
        else {
            $services = $services->filter(static function (ServiceDTO $service) {
                return in_array($service->getType(), [
                    ServiceTypeEnum::ELECTRIC_TARIFF,
                    ServiceTypeEnum::OTHER,
                ], true);
            });
        }

        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->getId()] = $service->getName();
        }
        $servicesMap[$claimService->getId()] = $claimService->getName();

        $claim = $claim->setService($claimService);

        return response()->json([
            'servicesSelect' => new SelectResource($servicesMap),
            'claim'          => new ClaimResource($claim),
            'services'       => new ServicesListResource($services),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_EDIT)) {
            abort(403);
        }

        $claim = $request->getId()
            ? $this->claimService->getById($request->getId())
            : $this->claimFactory->makeDefault()
                ->setInvoiceId($request->getInvoiceId())
                ->setServiceId($request->getServiceId())
        ;

        if ( ! $claim) {
            abort(404);
        }

        $claim
            ->setName($request->getName())
            ->setTariff($request->getTariff() ? : $request->getCost())
            ->setCost($request->getCost())
        ;

        $claim = $this->claimService->save($claim);

        return response()->json([
            'claim' => new ClaimResource($claim),
        ]);
    }

    public function list(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_VIEW)) {
            abort(403);
        }

        $claims = $this->getInvoiceClaims($invoiceId);

        return response()->json(new ClaimsListResource(
            $claims->getItems()->sortByServiceTypes(),
        ));
    }

    public function delete(int $invoiceId, int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_DROP)) {
            abort(403);
        }

        return $this->claimService->deleteById($id);
    }

    private function getInvoiceClaims(int $invoiceId): SearchResponse
    {
        $searcher = new ClaimSearcher();
        $searcher
            ->setInvoiceId($invoiceId)
            ->setWithService()
            ->setSortOrderProperty(Claim::ID, SearcherInterface::SORT_ORDER_ASC)
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC)
        ;

        return $this->claimService->search($searcher);
    }
}
