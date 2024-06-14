<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Objects\Account\AccountLocator;
use Core\Objects\Account\Requests\RegisterRequest;
use Core\Objects\Account\Services\AccountService;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\UserLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\ViewNames;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    private AccountService $accountService;
    private UserFactory    $userFactory;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->userFactory    = UserLocator::UserFactory();
    }

    public function index(): RedirectResponse|View
    {
        if ($this->accountService->getByUserId(Auth::id())) {
            return redirect()->route(RouteNames::PROFILE);
        }

        return view(ViewNames::PAGES_ACCOUNT_REGISTER);
    }

    public function register(RegisterRequest $request): void
    {
        $account = $request->dto();
        $account->setPrimaryUserId(Auth::id());
        $user = $this->userFactory->makeDtoFromObject(Auth::user());
        $account->addUser($user);

        $this->accountService->register($account);
    }
}
