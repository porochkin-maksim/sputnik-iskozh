<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Requests;

use Core\Domains\Access\PermissionEnum;
use lc;

class NewPaymentController
{
    public function index()
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            abort(403);
        }

        return view('pages.admin.billing.payments');
    }
}
