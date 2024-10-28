<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DefaultRequest;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function store(DefaultRequest $request): void
    {
        Session::put($request->get('key'), $request->get('value'));
    }
}
