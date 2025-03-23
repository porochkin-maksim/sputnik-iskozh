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
        return view(ViewNames::PAGES_PROPOSAL);
    }

    public function payment(): View
    {
        return view(ViewNames::PAGES_PAYMENT);
    }

    public function counter(): View
    {
        return view(ViewNames::PAGES_COUNTER);
    }
}
