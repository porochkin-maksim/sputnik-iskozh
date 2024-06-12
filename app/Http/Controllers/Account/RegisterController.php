<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Objects\Account\AccountLocator;
use Core\Objects\Account\Requests\RegisterRequest;
use Core\Objects\Account\Services\AccountService;
use Core\Objects\User\Factories\UserFactory;
use Core\Objects\User\UserLocator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private AccountService $accountService;
    private UserFactory    $userFactory;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->userFactory    = UserLocator::UserFactory();
    }

    public function __invoke(RegisterRequest $request): void
    {
        $account = $request->dto();
        $account->setPrimaryUserId(Auth::id());
        $user = $this->userFactory->makeDtoFromObject(Auth::user());
        $account->addUser($user);

        $this->accountService->register($account);
    }
}
