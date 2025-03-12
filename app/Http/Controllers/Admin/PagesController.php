<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use app;
use App\Http\Controllers\Controller;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Resources\Views\ViewNames;

class PagesController extends Controller
{
    public function index()
    {
        return view(ViewNames::ADMIN_PAGES_INDEX);

        abort(403);
    }

    public function services()
    {
        if (app::roleDecorator()->can(PermissionEnum::SERVICES_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_SERVICES);
        }

        abort(403);
    }

    public function periods()
    {
        if (app::roleDecorator()->can(PermissionEnum::PERIODS_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_PERIODS);
        }

        abort(403);
    }

    public function accounts()
    {
        if (app::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_ACCOUNTS);
        }

        abort(403);
    }

    public function invoices()
    {
        if (app::roleDecorator()->can(PermissionEnum::INVOICES_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_INVOICES);
        }

        abort(403);
    }

    public function roles()
    {
        if (app::roleDecorator()->can(PermissionEnum::ROLES_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_ROLES);
        }

        abort(403);
    }

    public function users()
    {
        if (app::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_USERS);
        }

        abort(403);
    }
}
