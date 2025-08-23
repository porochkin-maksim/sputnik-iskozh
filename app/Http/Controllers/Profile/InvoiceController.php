<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;

class InvoiceController extends Controller
{

    public function __construct()
    {
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_PROFILE_INVOICES);
    }
}
