<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Services\SaveRequest;
use App\Http\Resources\Admin\Services\ServiceResource;
use App\Http\Resources\Admin\Services\ServicesListResource;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Factories\ServiceFactory;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Service\Services\ServiceService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use lc;

class ServicesController extends Controller
{
    private ServiceFactory $serviceFactory;
    private ServiceService $serviceService;
    private PeriodService  $periodService;

    public function __construct()
    {
        $this->serviceFactory = ServiceLocator::ServiceFactory();
        $this->serviceService = ServiceLocator::ServiceService();
        $this->periodService  = PeriodLocator::PeriodService();
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_EDIT)) {
            abort(403);
        }

        return response()->json([
            ResponsesEnum::SERVICE => new ServiceResource($this->serviceFactory->makeDefault()),
        ]);
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_VIEW)) {
            abort(403);
        }

        $searcher = ServiceSearcher::make()
            ->exludeType(ServiceTypeEnum::OTHER)
            ->exludeType(ServiceTypeEnum::DEBT)
            ->exludeType(ServiceTypeEnum::ADVANCE_PAYMENT)
            ->withPeriods()
            ->setSortOrderProperty(Service::PERIOD_ID, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Service::ACTIVE, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Service::ID, SearcherInterface::SORT_ORDER_ASC);
        $services = $this->serviceService->search($searcher);

        $periodSearcher = PeriodSearcher::make()
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC);

        $periods = $this->periodService->search($periodSearcher);

        return response()->json(new ServicesListResource(
            $services->getItems(),
            $periods->getItems(),
        ));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_EDIT)) {
            abort(403);
        }

        $service = $request->getId()
            ? $this->serviceService->getById($request->getId())
            : $this->serviceFactory->makeDefault()
                ->setPeriodId($request->getPeriodId())
                ->setType(ServiceTypeEnum::tryFrom($request->getType()));

        if ( ! $service) {
            abort(404);
        }

        $service
            ->setName($request->getName())
            ->setIsActive($request->getIsActive())
            ->setCost($request->getCost());

        $service = $this->serviceService->save($service);

        return response()->json([
            ResponsesEnum::SERVICE => new ServiceResource($service),
        ]);
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_DROP)) {
            abort(403);
        }

        return $this->serviceService->deleteById($id);
    }
}
