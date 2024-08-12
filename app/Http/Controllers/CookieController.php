<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Core\Session\CookieNames;

class CookieController extends Controller
{
    public function cookieAgreement(): mixed
    {
        $cookie = cookie(CookieNames::COOKIE_AGREEMENT, true, 10080);

        return response(true)->cookie($cookie);
    }
}
