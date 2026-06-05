<?php declare(strict_types=1);

use App\Http\Resources\Admin\AccountResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\Account\AccountEntity;

/**
 * @var AccountEntity $account
 */
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_ACCOUNT_VIEW, $account) }}
    <account-item-view :model-value='@json(new AccountResource($account))' ></account-item-view>
@endsection
