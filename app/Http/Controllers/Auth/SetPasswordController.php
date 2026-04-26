<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use App\Models\User;
use App\Resources\RouteNames;
use Carbon\Carbon;
use Core\App\User\SetPasswordByTokenCommand;
use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\User\UserService;
use Exception;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class SetPasswordController extends AbstractAuthController
{
    use ResetsPasswords;

    public function __construct(
        private readonly UserService               $userService,
        private readonly SetPasswordByTokenCommand $setPasswordByTokenCommand,
    )
    {
    }

    public function index(DefaultRequest $request)
    {
        try {
            $token = $request->getStringOrNull('token');
            $email = $this->getEmail($request);

            if ( ! $email) {
                return redirect()->route(RouteNames::INDEX);
            }

            return view('auth.passwords.set', compact('email', 'token'));

        }
        catch (Exception) {
            return redirect()->route(RouteNames::INDEX);
        }
    }

    public function set(DefaultRequest $request)
    {
        $email = $this->getEmail($request);

        $user = $this->userService->getByEmail($email);

        if ( ! $user) {
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        $result = $this->setPasswordByTokenCommand->execute(
            $email,
            $request->getStringOrNull('password'),
            $request->getStringOrNull('password_confirmation'),
            $request->getString('token'),
        );

        if ($result) {
            Auth::login(User::findOrFail($user->getId()));
        }

        return redirect()->route(RouteNames::HOME);
    }

    private function getEmail(DefaultRequest $request): ?string
    {
        $token = $request->getStringOrNull('token');
        $data  = TokenFacade::find($token);

        if (empty($data)) {
            return null;
        }

        $email   = $data['email'];
        $expires = Carbon::parse($data['expires']);
        if (now()->gt($expires)) {
            return null;
        }

        return $email;
    }
}
