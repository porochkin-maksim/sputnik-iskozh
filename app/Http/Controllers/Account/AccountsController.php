<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Requests\SaveRequest;
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
        $account = $this->accountService->getByUserId(Auth::id());

        if ($account) {
            return view(ViewNames::PAGES_HOME);
        }

        return view(ViewNames::PAGES_PROFILE);
    }

    public function show(int $id): JsonResponse
    {
        $account = $this->accountService->getById($id);

        return response()->json([
            ResponsesEnum::ACCOUNT => $account,
        ]);
    }

    public function register(SaveRequest $request): JsonResponse
    {
        $account = $request->dto();
        $account = $this->accountService->register($account);

        return response()->json($account);
    }

    public function info(): JsonResponse
    {
        $account = $this->accountService->getByUserId(Auth::id());
        if ($account) {
            $info = $this->accountService->getAccountInfo($account);

            return response()->json($info);
        }

        return response()->json($account);
    }
}
