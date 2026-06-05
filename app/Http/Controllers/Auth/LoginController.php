<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use App\Models\User;
use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\Infra\Uid\UidFacade;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends AbstractAuthController
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function token(string $token)
    {
        $pin = (new DefaultRequest(request()->toArray()))->getString('pin');

        $data = TokenFacade::find($token);
        if ($data && Hash::check($pin, $data['pin'])) {
            $uid = UidFacade::find($token);

            if ($uid) {
                $user = User::find($uid->getReferenceId());
                Auth::login($user, true);
            }
        }

        return back()->withErrors(['error' => 'Неверный токен или код']);
    }
}
