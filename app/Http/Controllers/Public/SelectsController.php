<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Http\Resources\Common\SelectOptionResource;
use App\Models\Account\Account;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterService;
use Core\Repositories\SearcherInterface;
use Illuminate\Http\JsonResponse;

class SelectsController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly CounterService $counterService,
    )
    {
    }

    public function accounts(): JsonResponse
    {
        $searcher = new AccountSearcher()
            ->setWithoutSntAccount()
            ->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC)
        ;
        $items    = $this->accountService->search($searcher);

        return response()->json(new AccountsSelectResource(
            $items->getItems(),
            false,
        ));
    }

    public function counters(int $accountId): JsonResponse
    {
        $counters = $this->counterService->getByAccountId($accountId);

        $result = [];
        foreach ($counters as $counter) {
            $result[] = new SelectOptionResource($counter->getId(), $counter->getNumber());
        }

        return response()->json($result);
    }
}
