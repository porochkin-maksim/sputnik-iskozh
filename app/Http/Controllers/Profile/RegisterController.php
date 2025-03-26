<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Accounts\RegisterRequest;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\UserLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\ViewNames;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    private AccountService $accountService;
    private AccountFactory $accountFactory;
    private UserFactory    $userFactory;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->accountFactory = AccountLocator::AccountFactory();
        $this->userFactory    = UserLocator::UserFactory();
    }

    public function index(): RedirectResponse|View
    {
        if ($this->accountService->getByUserId(Auth::id())) {
            return redirect()->route(RouteNames::HOME);
        }

        return view(ViewNames::PAGES_ACCOUNT_REGISTER);
    }

    public function register(RegisterRequest $request): void
    {
        $account = $this->accountFactory->makeDefault();
        $account
            ->setSize($request->getSize())
            ->setNumber($request->getNumber())
            ->setPrimaryUserId(Auth::id())
        ;

        $account->addUser(\lc::user());

        $this->accountService->register($account);
    }
}
