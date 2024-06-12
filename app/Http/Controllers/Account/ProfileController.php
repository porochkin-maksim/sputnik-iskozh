<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Objects\Account\AccountLocator;
use Core\Objects\Account\Services\AccountService;
use Core\Objects\User\Requests\SaveProfileRequest;
use Core\Objects\User\Services\UserService;
use Core\Objects\User\UserLocator;
use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private AccountService $accountService;
    private UserService    $userService;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->userService    = UserLocator::UserService();
    }

    public function show(): View
    {
        $account = $this->accountService->getByUserId(Auth::id());
        $user    = $account->getUsers()->getById(Auth::id());

        return view(ViewNames::PAGES_PROFILE, compact('account', 'user'));
    }

    public function save(SaveProfileRequest $request): void
    {
        $user = $request->dto($this->userService->getById(Auth::id()));

        $this->userService->save($user);
    }
}
