<?php declare(strict_types=1);

use App\Http\Resources\Admin\Accounts\AccountResource;
use Core\Domains\Account\Models\AccountDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var AccountDTO $account
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_ACCOUNT_VIEW, $account) }}
    <account-item-view :model-value='@json(new AccountResource($account))' />
@endsection