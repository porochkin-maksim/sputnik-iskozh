<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use App\Models\Counter\CounterHistory;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Repositories\SearcherInterface;
use Illuminate\Http\JsonResponse;
use lc;

class CounterHistoryController extends Controller
{

    public function __construct(
        private readonly CounterHistoryService $counterHistoryService,
    )
    {
    }

    // vue: resources/js/components/admin/accounts/counters/CounterItemView.vue
    // vue: resources/js/components/admin/accounts/counters/CounterHistoryTablePanel.vue
    public function list(int $counterId, DefaultRequest $request): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $searcher = CounterHistorySearcher::make()
            ->setCounterId($counterId)
            ->setWithClaim()
            ->setWithPrevious()
            ->setWithFile()
            ->descSort()
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
        ;

        if ($request->getDateOrNull('lastDate')) {
            $searcher->addWhere(CounterHistory::DATE, SearcherInterface::LT, $request->getDateOrNull('lastDate')->toDateString());
        }

        $histories = $this->counterHistoryService->search($searcher);

        return response()->json([
            'histories' => new CounterHistoryListResource($histories->getItems()),
            'total'     => $histories->getTotal(),
        ]);
    }
}
