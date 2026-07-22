<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use App\Models\User;
use Carbon\Carbon;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;
use Core\Domains\Infra\Uid\UidRepositoryInterface;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends AbstractAuthController
{
    use AuthenticatesUsers;

    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UidRepositoryInterface   $uidRepository,
    )
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, mixed $user): void
    {
        if ($user instanceof User) {
            $user->forceFill([User::LOGGED_IN_AT => Carbon::now()])->save();
        }
    }

    public function token(string $token)
    {
        $pin = new DefaultRequest(request()->toArray())->getString('pin');

        $data = $this->tokenRepository->find($token);
        if ($data && Hash::check($pin, $data['pin'])) {
            $uid = $this->uidRepository->find($token);

            if ($uid) {
                $user = User::find($uid->getReferenceId());
                Auth::login($user, true);

                $user?->forceFill([User::LOGGED_IN_AT => Carbon::now()])->save();
            }
        }

        return back()->withErrors(['error' => 'Неверный токен или код']);
    }
}
