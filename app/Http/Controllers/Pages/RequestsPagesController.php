<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;

class RequestsPagesController extends Controller
{
    public function index(): View
    {
        return view('pages.contacts.requests');
    }

    public function proposal(): View
    {
        return view('pages.contacts.proposal');
    }

    public function payment(): View
    {
        return view('pages.contacts.payment');
    }

    public function counter(): View
    {
        return view('pages.contacts.counter');
    }
}
