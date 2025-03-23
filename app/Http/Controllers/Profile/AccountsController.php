<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Accounts\SaveRequest;
use App\Http\Resources\Profile\Accounts\AccountResource;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_HOME);
    }

    public function show(int $id): JsonResponse
    {
        $account = $this->accountService->getById($id);

        return response()->json([
            ResponsesEnum::ACCOUNT => new AccountResource($account),
        ]);
    }

    public function register(SaveRequest $request): JsonResponse
    {
        $account = $request->dto();
        $account = $this->accountService->register($account);

        return response()->json($account);
    }
}
