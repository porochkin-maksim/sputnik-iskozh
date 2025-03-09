<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Core\Resources\Views\ViewNames;

class PagesController extends Controller
{
    public function index()
    {
        return view(ViewNames::ADMIN_PAGES_INDEX);
    }

    public function services()
    {
        return view(ViewNames::ADMIN_PAGES_SERVICES);
    }

    public function periods()
    {
        return view(ViewNames::ADMIN_PAGES_PERIODS);
    }

    public function accounts()
    {
        return view(ViewNames::ADMIN_PAGES_ACCOUNTS);
    }

    public function invoices()
    {
        return view(ViewNames::ADMIN_PAGES_INVOICES);
    }
}
