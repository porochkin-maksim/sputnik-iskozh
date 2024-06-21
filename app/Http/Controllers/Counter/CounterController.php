<?php declare(strict_types=1);

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Requests\SaveRequest;
use Core\Domains\Counter\Requests\SearchRequest;
use Core\Domains\Counter\Services\CounterService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    private CounterService $counterService;
    private AccountService $accountService;

    public function __construct()
    {
        $this->counterService = CounterLocator::CounterService();
        $this->accountService = AccountLocator::AccountService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_COUNTERS_INDEX);
    }

    public function show(int $id): View
    {
        $counter = $this->counterService->getById($id);

        if ( ! $counter) {
            abort(404);
        }

        return view(ViewNames::PAGES_NEWS_SHOW, compact('counter'));
    }

    public function edit(int $id): JsonResponse
    {
        $counter = $this->counterService->getById($id);

        return response()->json([
            ResponsesEnum::COUNTER => $counter,
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $account = $this->accountService->getByUserId(Auth::id());

        $searcher = $request->dto();
        $searcher
            ->setSortOrderDesc()
            ->setAccountId($account->getId());

        $counter = $this->counterService->search($searcher);

        return response()->json([
            ResponsesEnum::COUNTERS => $counter->getItems(),
            ResponsesEnum::TOTAL    => $counter->getTotal(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        $counter = $request->dto();
        $counter = $this->counterService->save($counter);

        return response()->json($counter);
    }
}
