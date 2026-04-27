<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class AbstractAuthController extends Controller
{
    /**
     * @return string
     */
    public function username()
    {
        return 'email';
    }
}
