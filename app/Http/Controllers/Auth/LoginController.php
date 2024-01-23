<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class LoginController extends AbstractAuthController
{
    use ThrottlesLogins,
        AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
