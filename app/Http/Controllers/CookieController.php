<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Session\CookieNames;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CookieController extends Controller
{
    /** 10 лет в минутах */
    public const int LIVETIME_MINUTES = 5256000;

    public function cookieAgreement(): ResponseFactory|Response
    {
        $cookie  = cookie(CookieNames::COOKIE_AGREEMENT, true, self::LIVETIME_MINUTES);

        return response(true)->cookie($cookie);
    }
}
