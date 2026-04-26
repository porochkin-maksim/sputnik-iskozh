<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RequestsPagesController extends Controller
{
    public function index(): View
    {
        return view('public.contacts.requests');
    }

    public function payment(): View
    {
        return view('public.contacts.payment');
    }

    public function counter(): View
    {
        return view('public.contacts.counter');
    }
}
