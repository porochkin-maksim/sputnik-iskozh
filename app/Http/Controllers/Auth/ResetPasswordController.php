<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends AbstractAuthController
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    public function showResetForm(DefaultRequest $request)
    {
        $token = $request->route()?->parameter('token');

        return view('pages.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
