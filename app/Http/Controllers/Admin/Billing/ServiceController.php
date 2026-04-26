<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Services\ServiceResource;
use App\Http\Resources\Admin\Services\ServicesListResource;
use Core\App\Billing\Service\GetListCommand;
use Core\App\Billing\Service\SaveCommand;
use Core\Domains\Access\PermissionEnum;
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
        private readonly GetListCommand        $getListCommand,
        private readonly SaveCommand           $saveCommand,
    )
    {
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::SERVICES_VIEW)) {
            return view('admin.pages.services');
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

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_VIEW)) {
            abort(403);
        }

        $result = $this->getListCommand->execute();

        return response()->json(new ServicesListResource(
            $result['services']->getItems(),
            $result['periods']->getItems(),
        ));
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_EDIT)) {
            abort(403);
        }

        $service = $this->saveCommand->execute(
            id      : $request->getIntOrNull('id'),
            periodId: $request->getIntOrNull('period_id'),
            type    : ServiceTypeEnum::tryFrom($request->getInt('type')),
            name    : $request->getStringOrNull('name'),
            cost    : $request->getFloat('cost'),
            isActive: $request->getBool('is_active'),
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
