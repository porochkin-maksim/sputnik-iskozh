<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Periods\PeriodResource;
use App\Http\Resources\Admin\Periods\PeriodsListResource;
use App\Support\HistoryChangesRoute;
use Core\App\Billing\Period\GetListCommand;
use Core\App\Billing\Period\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Billing\Period\PeriodFactory;
use Core\Domains\Billing\Period\PeriodService;
use Illuminate\Http\JsonResponse;
use lc;

class PeriodController extends Controller
{

    public function __construct(
        private readonly PeriodFactory  $periodFactory,
        private readonly PeriodService  $periodService,
        private readonly GetListCommand $getListCommand,
        private readonly SaveCommand    $saveCommand,
    )
    {
    }

    // blade: resources/views/admin/pages/periods.blade.php
    // vue: resources/js/components/admin/periods/PeriodsBlock.vue
    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::PERIODS_VIEW)) {
            return view('pages.admin.billing.periods');
        }

        abort(403);
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PERIODS_EDIT)) {
            abort(403);
        }

        return response()->json(new PeriodResource($this->periodFactory->makeDefault()));
    }

    // vue: resources/js/components/admin/periods/PeriodsBlock.vue
    public function list(): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::PERIODS_VIEW)) {
            abort(403);
        }

        $periods     = $this->getListCommand->execute()->getItems();
        $hasUnclosed = false;
        foreach ($periods as $period) {
            $hasUnclosed = $hasUnclosed || ! $period->isClosed();
        }

        return response()->json([
            'periods'    => new PeriodsListResource($periods),
            'historyUrl' => HistoryChangesRoute::make(type: HistoryType::PERIOD),
        ]);
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PERIODS_EDIT)) {
            abort(403);
        }

        $period = $this->saveCommand->execute(
            id      : $request->getIntOrNull('id'),
            name    : $request->getStringOrNull('name'),
            startAt : $request->getDateOrNull('start_at'),
            endAt   : $request->getDateOrNull('end_at'),
            isClosed: $request->getBool('is_closed'),
        );

        if ($period === null) {
            abort(404);
        }

        return response()->json([
            'period' => new PeriodResource($period),
        ]);
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PERIODS_DROP)) {
            abort(403);
        }

        return $this->periodService->deleteById($id);
    }
}
