<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\Accounts\AccountResource;
use Core\Domains\Account\AccountService;
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
}
