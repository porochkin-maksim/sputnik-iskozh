<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;

class PagesController extends Controller
{
    public function index(): View
    {
        return view(ViewNames::PAGES_INDEX);
    }

    public function contacts(): View
    {
        return view(ViewNames::PAGES_CONTACTS);
    }

    public function privacy(): View
    {
        return view(ViewNames::PAGES_PRIVACY);
    }
}
