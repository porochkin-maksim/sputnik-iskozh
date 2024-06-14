<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Objects\User\Requests\SaveProfileEmailRequest;
use Core\Objects\User\Requests\SaveProfilePasswordRequest;
use Core\Objects\User\Requests\SaveProfileRequest;
use Core\Objects\User\Services\UserService;
use Core\Objects\User\UserLocator;
use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $user = $request->dto($this->userService->getById(Auth::id()));

        $this->userService->save($user);
    }

    public function saveEmail(SaveProfileEmailRequest $request): void
    {
        DB::beginTransaction();

        try {
            $user = $request->dto($this->userService->getById(Auth::id()));

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
            $user = $request->dto($this->userService->getById(Auth::id()));

            $this->userService->save($user);

            DB::commit();
        }
        catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }
}
