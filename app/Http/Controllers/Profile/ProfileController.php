<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Requests\Users\SaveProfileEmailRequest;
use App\Http\Requests\Users\SaveProfilePasswordRequest;
use App\Http\Requests\Users\SaveProfileRequest;
use Core\Domains\User\Services\UserService;
use Core\Domains\User\UserLocator;
use Core\Resources\Views\ViewNames;
use Core\Session\SessionNames;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    private UserService    $userService;

    public function __construct()
    {
        $this->userService    = UserLocator::UserService();
    }

    public function show(): View
    {
        return view(ViewNames::PAGES_PROFILE);
    }

    public function save(SaveProfileRequest $request): void
    {
        $user = $request->dto(\lc::user());

        $this->userService->save($user);
    }

    public function saveEmail(SaveProfileEmailRequest $request): void
    {
        DB::beginTransaction();

        try {
            $user = \lc::user();
            $user->setEmail($request->getEmail());

            $this->userService->save($user);

            $user->getModel()->sendEmailVerificationNotification();

            DB::commit();
        }
        catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    public function savePassword(SaveProfilePasswordRequest $request): void
    {
        DB::beginTransaction();

        try {
            $user = \lc::user();
            $user->setPassword($request->getPassword());

            $this->userService->save($user);

            DB::commit();
        }
        catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    public function switchAccount(DefaultRequest $request): bool
    {
        $accountId = $request->getIntOrNull('accountId');

        $account = \lc::user()->getAccounts()->searchById($accountId);

        if ($account === null) {
            return false;
        }

        Session::put(SessionNames::ACCOUNT_ID, $account->getId());

        return true;
    }
}
