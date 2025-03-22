<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use Carbon\Carbon;
use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\User\UserLocator;
use Core\Resources\RouteNames;
use Exception;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class SetPasswordController extends AbstractAuthController
{
    use ResetsPasswords;

    public function index(DefaultRequest $request)
    {
        try {
            $token = $request->get('token');
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

        $request->validate($this->rules(), $this->validationErrorMessages());

        $user = UserLocator::UserService()->getByEmail($email);

        if ( ! $user) {
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        $user->setPassword($request->get('password'));

        UserLocator::UserService()->save($user);

        TokenFacade::drop($request->get('token'));

        Auth::login($user->getModel());

        return redirect()->route(RouteNames::PROFILE);
    }

    private function getEmail(DefaultRequest $request): ?string
    {
        $token = $request->get('token');
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
