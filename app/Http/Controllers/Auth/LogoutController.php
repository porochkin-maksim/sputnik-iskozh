<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends AbstractAuthController
{
    use AuthenticatesUsers;

    public function __invoke(Request $request): RedirectResponse|JsonResponse
    {
        return $this->logout($request);
    }
}
