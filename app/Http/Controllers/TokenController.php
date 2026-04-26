<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use App\Resources\RouteNames;

class TokenController extends Controller
{
    public function token(?string $token)
    {
        if (\lc::isAuth()) {
            return redirect()->route(RouteNames::HOME);
        }

        $uid = $token ? UidFacade::find($token) : null;
        if ( ! $uid) {
            abort(404);
        }

        if ($uid->getType() === UidTypeEnum::LOGIN) {

            if ( ! User::find($uid->getReferenceId())) {
                return redirect()->route(RouteNames::HOME);
            }

            return view('public.system.token-auth', compact('uid'));
        }

        abort(404);
    }
}
