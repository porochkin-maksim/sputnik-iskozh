<?php declare(strict_types=1);
// routes/breadcrumbs.php

// https://github.com/diglactic/laravel-breadcrumbs

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Resources\RouteNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for(RouteNames::ADMIN, static function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route(RouteNames::ADMIN));
});

Breadcrumbs::for(RouteNames::ADMIN_INVOICE_INDEX, static function (BreadcrumbTrail $trail) {
    $previousUrl = url()->previous();
    $routeName   = Route::getRoutes()->match(Request::create($previousUrl))->getName();
    $route       = route(RouteNames::ADMIN_INVOICE_INDEX);
    if ($routeName === RouteNames::ADMIN_INVOICE_INDEX) {
        $route = $previousUrl;
    }

    $trail->parent(RouteNames::ADMIN);
    $trail->push(RouteNames::name(RouteNames::ADMIN_INVOICE_INDEX), $route);
});
Breadcrumbs::for(RouteNames::ADMIN_INVOICE_VIEW, static function (BreadcrumbTrail $trail, InvoiceDTO $invoice) {
    $trail->parent(RouteNames::ADMIN_INVOICE_INDEX);
    $trail->push('Счёт №' . $invoice->getId(), route(RouteNames::ADMIN_INVOICE_VIEW, $invoice->getId()));
});

Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_INDEX, static function (BreadcrumbTrail $trail) {
    $trail->push('Участки', route(RouteNames::ADMIN));
});
Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_VIEW, static function (BreadcrumbTrail $trail, AccountDTO $account) {
    $trail->parent(RouteNames::ADMIN_ACCOUNT_INDEX);
    $trail->push('Участок №' . $account->getNumber(), route(RouteNames::ADMIN_ACCOUNT_VIEW, $account->getId()));
});
Breadcrumbs::for(RouteNames::ADMIN_COUNTER_VIEW, static function (BreadcrumbTrail $trail, CounterDTO $counter) {
    $trail->parent(RouteNames::ADMIN_ACCOUNT_VIEW, $counter->getAccount(true));
    $trail->push('Счётчик №' . $counter->getNumber(), route(RouteNames::ADMIN_COUNTER_VIEW, [$counter->getAccountId(), $counter->getId()]));
});