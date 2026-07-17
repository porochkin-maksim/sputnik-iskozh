<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Accounts\AccountSearchResource;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Repositories\SearcherInterface;
use Illuminate\Http\JsonResponse;

class AccountsController extends Controller
{

    public function __construct(
        private readonly AccountService $accountService,
    )
    {
    }

    public function show(int $id): JsonResponse
    {
        $account = $this->accountService->getById($id);

        return response()->json([
            'account' => new AccountResource($account),
        ]);
    }

    public function search(DefaultRequest $request): JsonResponse
    {
        $query    = $request->getString('q');
        $searcher = (new AccountSearcher())
            ->setLimit(20)
            ->setNumberLike($query)
            ->setSortOrderProperty('sort_value', SearcherInterface::SORT_ORDER_ASC);

        $accounts = $this->accountService->search($searcher)->getItems();

        return response()->json([
            'accounts' => new AccountSearchResource($accounts),
        ]);
    }
}
