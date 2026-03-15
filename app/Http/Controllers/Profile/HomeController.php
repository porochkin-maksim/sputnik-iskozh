<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home.pages.index');
    }

    public function invoices(): View
    {
        return view('home.pages.invoices');
    }
}
