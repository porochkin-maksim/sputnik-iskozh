<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use Illuminate\Http\JsonResponse;

class CounterHistoryController extends Controller
{
    private CounterHistoryService $counterHistoryService;

    public function __construct()
    {
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
    }

    public function list(int $counterId, DefaultRequest $request): JsonResponse
    {
        $searcher = CounterHistorySearcher::make()
            ->setCounterId($counterId)
            ->setWithClaim()
            ->setWithPrevious()
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