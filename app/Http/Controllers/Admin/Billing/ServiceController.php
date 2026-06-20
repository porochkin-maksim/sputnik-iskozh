<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Periods\PeriodsListResource;
use App\Http\Resources\Admin\Services\ServiceResource;
use App\Http\Resources\Admin\Services\ServicesListResource;
use App\Http\Resources\Common\SelectResource;
use App\Support\HistoryChangesRoute;
use Core\App\Billing\Service\GetListCommand;
use Core\App\Billing\Service\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Billing\Period\PeriodGate;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Illuminate\Http\JsonResponse;
use lc;

class ServiceController extends Controller
{

    public function __construct(
        private readonly ServiceFactory        $serviceFactory,
        private readonly ServiceCatalogService $serviceService,
        private readonly PeriodService         $periodService,
        private readonly PeriodGate            $periodGate,
        private readonly GetListCommand        $getListCommand,
        private readonly SaveCommand           $saveCommand,
    )
    {
    }

    // blade: resources/views/admin/pages/services.blade.php
    // vue: resources/js/components/admin/services/ServicesBlock.vue
    // vue: resources/js/components/admin/services/ServiceEditDialog.vue
    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::SERVICES_VIEW)) {
            return view('pages.admin.billing.services');
        }

        abort(403);
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_EDIT)) {
            abort(403);
        }

        $period = $this->periodService->getActive();

        $service = $this->serviceFactory->makeDefault()
            ->setPeriodId($period?->getId())
            ->setPeriod($period)
        ;

        return response()->json([
            'service' => new ServiceResource($service),
        ]);
    }

    // vue: resources/js/components/admin/services/ServicesBlock.vue
    // vue: resources/js/components/admin/services/ServiceEditDialog.vue
    public function list(): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::SERVICES_VIEW)) {
            abort(403);
        }

        $result   = $this->getListCommand->execute();
        $services = $result['services']->getItems();
        $periods  = $result['periods']->getItems();
        $types    = array_filter(
            ServiceTypeEnum::array(),
            static fn(string $name) => $name !== ServiceTypeEnum::OTHER->name(),
        );
        $periodOptions = [];
        foreach ($periods as $period) {
            if ($period->isClosed()) {
                continue;
            }

            $periodOptions[$period->getId()] = $period->getName();
        }

        return response()->json([
            'services'    => new ServicesListResource($services),
            'periods'     => new SelectResource($periodOptions),
            'periodsInfo' => new PeriodsListResource($periods),
            'types'       => new SelectResource($types),
            'historyUrl'  => HistoryChangesRoute::make(type: HistoryType::SERVICE),
        ]);
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        $periodId = $request->getIntOrNull('period_id');
        $this->periodGate->assertCanEditServices($periodId ?? 0);

        $service = $this->saveCommand->execute(
            id        : $request->getIntOrNull('id'),
            periodId  : $request->getIntOrNull('period_id'),
            type      : ServiceTypeEnum::tryFrom($request->getInt('type')),
            name      : $request->getStringOrNull('name'),
            cost      : $request->getFloat('cost'),
            isActive  : $request->getBool('is_active'),
            periodFrom: $request->getDateOrNull('period_from'),
            periodTo  : $request->getDateOrNull('period_to'),
        );

        if ($service === null) {
            abort(404);
        }

        return response()->json([
            'service' => new ServiceResource($service),
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
