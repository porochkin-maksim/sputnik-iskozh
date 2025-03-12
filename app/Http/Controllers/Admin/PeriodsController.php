<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Periods\SaveRequest;
use App\Http\Resources\Admin\Periods\PeriodResource;
use App\Http\Resources\Admin\Periods\PeriodsListResource;
use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Period\Factories\PeriodFactory;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;

class PeriodsController extends Controller
{
    private PeriodFactory $periodFactory;
    private PeriodService $periodService;

    public function __construct()
    {
        $this->periodFactory = PeriodLocator::PeriodFactory();
        $this->periodService = PeriodLocator::PeriodService();
    }

    public function create(): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json(new PeriodResource($this->periodFactory->makeDefault()));
    }

    public function list(): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $searcher = new PeriodSearcher();
        $searcher
            ->setSortOrderProperty(Period::START_AT, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Period::END_AT, SearcherInterface::SORT_ORDER_DESC);
        $periods = $this->periodService->search($searcher);

        return response()->json(new PeriodsListResource($periods->getItems()));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $period = $request->getId()
            ? $this->periodService->getById($request->getId())
            : $this->periodFactory->makeDefault();

        if ( ! $period) {
            abort(404);
        }

        $period
            ->setName($request->getName())
            ->setStartAt($request->getStartAt())
            ->setEndAt($request->getEndAt());

        $period = $this->periodService->save($period);

        return response()->json([
            ResponsesEnum::PERIOD => new PeriodResource($period),
        ]);
    }

    public function delete(int $id): bool
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return $this->periodService->deleteById($id);
    }

    private function canEdit(): bool
    {
        return \app::roleDecorator()->canPeriods();
    }
}
